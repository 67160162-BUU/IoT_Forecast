<?php
// ตั้งค่าให้ Server ตอบกลับเป็น JSON
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// --- ✨ 1. ดึงไฟล์เชื่อมต่อเข้ามา (ส่วนที่แก้ไข) ✨ ---
require 'connect.php'; // จะทำให้ตัวแปร $conn ใช้งานได้

// --- ตั้งค่าเพิ่มเติม ---
$tableName = "SensorForecast_Saved"; // << ใส่ชื่อตารางของคุณ

// สร้าง object สำหรับการตอบกลับ
$response = array('status' => 'error', 'message' => 'An unknown error occurred.');

// --- 2. ตรวจสอบว่าเป็นการส่งแบบ POST และมีข้อมูล 'savedata' ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['savedata'])) {
        // แปลงข้อมูล JSON string ที่ส่งมา กลับเป็น PHP object/array
        $data = json_decode($_POST['savedata'], true);

        // ตรวจสอบว่าแปลงค่าสำเร็จและมีข้อมูล location
        if (json_last_error() === JSON_ERROR_NONE && isset($data['location'])) {

            // (ส่วนเชื่อมต่อ Database เดิมถูกลบออกไป เพราะ require 'connect.php' ทำงานแล้ว)
            
            // --- 4. เตรียมคำสั่ง SQL แบบ Prepared Statement ---
            $sql = "INSERT INTO $tableName (temp, hum, lux, lvl, pres, location) VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // --- 5. Bind Parameters ---
                $stmt->bind_param("dddids", // แก้จาก "dddis" เป็น "ddddis"
                    $data['temperature'],
                    $data['humidity'],
                    $data['lux'],
                    $data['light_level'],
                    $data['pressure'], // เพิ่มตัวแปรนี้เข้ามา
                    $data['location']
                );

                // --- 6. สั่ง Execute และตรวจสอบผลลัพธ์ ---
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Data inserted successfully.';
                } else {
                    $response['message'] = "Execute failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = "Prepare failed: " . $conn->error;
            }
            $conn->close();

        } else {
            $response['message'] = 'Invalid data format or missing location.';
        }
    } else {
        $response['message'] = 'No data received.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// --- 7. ส่งผลลัพธ์กลับไปให้ JavaScript ---
echo json_encode($response);

?>