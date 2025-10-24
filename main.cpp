#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BMP280.h>
#include <BH1750.h>         
#include "DHT.h"

// --- 1. ตั้งค่า WiFi และ Server ---
const char* ssid = "BillAtsoft";
const char* password = "5544332211";
const char* serverName = "https://angsila.informatics.buu.ac.th/~67160162/forecast/dbwrite.php";
String apiKeyValue = "tPmAT5Ab3j7F9";

// --- 2. ตั้งค่าเซ็นเซอร์และขาเชื่อมต่อ ---
// LDR
const int ldrPin = 34;
#define V_REF 3.3
#define ADC_RESOLUTION 4095.0
#define FIXED_RESISTOR 10000.0

// DHT22
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

// BH1750
BH1750 lightMeter; // สร้างอ็อบเจกต์สำหรับ BH1750

// BMP280
Adafruit_BMP280 bmp;

void setup() {
  Serial.begin(115200);

  // --- เริ่มการทำงานของเซ็นเซอร์ ---
  dht.begin();
  
  if (!bmp.begin(0x76)) {
    Serial.println("ไม่พบเซ็นเซอร์ BMP280, โปรดตรวจสอบการเชื่อมต่อ!");
    while (1);
  }
  Serial.println("เริ่มต้นการทำงานเซ็นเซอร์เรียบร้อยแล้ว");

  // --- เริ่มการเชื่อมต่อ WiFi ---
  WiFi.begin(ssid, password);
  Serial.print("กำลังเชื่อมต่อ WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nเชื่อมต่อ WiFi สำเร็จ!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // --- อ่านค่าจากเซ็นเซอร์ทั้งหมด ---
  
  // 1. DHT22
  float humidity_dht = dht.readHumidity();
  float temperature_dht = dht.readTemperature(); // ยังคงอ่านค่าเพื่อเช็ค error แต่ไม่ได้ส่งไป

  // 2. LDR
  int rawAdcValue = analogRead(ldrPin);
  long lightLevel = map(rawAdcValue, 0, 4095, 100, 0);
  // float voltage = rawAdcValue * (V_REF / ADC_RESOLUTION);
  // float resistanceLDR = 0;
  // if (V_REF - voltage > 0) {
  //     resistanceLDR = (voltage * 3000) / (V_REF - voltage);
  // }
  // float lux = 500.0 / (resistanceLDR / 1000.0);

  // 3. อ่านค่า Lux จาก BH1750
  float lux = lightMeter.readLightLevel();

  // 3. BMP280
  float temperature_bmp = bmp.readTemperature();
  float pressure_pa = bmp.readPressure(); 
  float pressure_hpa = pressure_pa / 100.0F;

  // ตรวจสอบว่าอ่านค่าจาก DHT ได้หรือไม่
  if (isnan(humidity_dht) || isnan(temperature_dht)) {
    Serial.println("ไม่สามารถอ่านค่าจากเซ็นเซอร์ DHT ได้!");
    delay(2000);
    return;
  }
  
  // --- [โค้ดที่เพิ่มเข้ามา] แสดงผลค่าที่อ่านได้ทั้งหมดทาง Serial Monitor ---
  Serial.println("----------------------------------------");
  Serial.println("ค่าที่อ่านได้จากเซ็นเซอร์:");
  Serial.print("  - อุณหภูมิ (BMP280): "); Serial.print(temperature_bmp, 2); Serial.println(" *C");
  Serial.print("  - ความชื้น (DHT22): "); Serial.print(humidity_dht, 1); Serial.println(" %");
  Serial.print("  - ค่า Lux (LDR): "); Serial.println(lux, 2);
  Serial.print("  - ระดับแสง (LDR): "); Serial.print(lightLevel); Serial.println(" %");
  Serial.print("  - ความกดอากาศ (BMP280): "); Serial.print(pressure_hpa, 2); Serial.println(" hPa");
  Serial.println("----------------------------------------");

  // --- ส่งข้อมูลไปยัง Server ---
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // สร้าง String สำหรับส่งข้อมูลตามที่กำหนด
    String httpRequestData = "api_key=" + apiKeyValue +
                             "&temp=" + String(temperature_bmp) +
                             "&hum=" + String(humidity_dht) +
                             "&lux=" + String(lux) +
                             "&lvl=" + String(lightLevel) +
                             "&pres=" + String(pressure_hpa);

    Serial.print("กำลังส่งข้อมูลไปยัง Server: ");
    Serial.println(httpRequestData);

    // ส่ง POST request
    int httpResponseCode = http.POST(httpRequestData);

    if (httpResponseCode > 0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      String payload = http.getString();
      Serial.println("Server response: " + payload);
    } else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi ถูกตัดการเชื่อมต่อ");
  }

  Serial.println("\nรอ 10 วินาทีเพื่อส่งข้อมูลครั้งต่อไป...");
  delay(2000);
}