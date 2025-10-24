<?php

// --- 1. การตั้งค่า Error Reporting (สำหรับตอนดีบักเท่านั้น) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- 2. ตั้งค่า Header และ Response ---
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// --- 3. ตรวจสอบข้อมูลเบื้องต้น ---
if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    $response['message'] = 'Invalid request method. Only POST is accepted.';
    echo json_encode($response);
    exit();
}

$api_key_value = "tPmAT5Ab3j7F9";
if (!isset($_POST['api_key']) || $_POST['api_key'] !== $api_key_value) {
    $response['message'] = 'Authentication failed. Missing or wrong API Key.';
    echo json_encode($response);
    exit();
}

// ตรสจสอบ parameter
$required_params = ['temp', 'hum', 'lux', 'lvl', 'pres'];
foreach ($required_params as $param) {
    if (!isset($_POST[$param])) {
        $response['message'] = "Missing required parameter: " . $param;
        echo json_encode($response);
        exit();
    }
}

// --- 4. เชื่อมต่อและบันทึกข้อมูลลงฐานข้อมูล ---
try {
    include 'connect.php';
    $sql = "INSERT INTO SensorForecast (temp, hum, lux, lvl, pres) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('SQL statement preparation failed: ' . $conn->error);
    }
    
    $temp = test_input($_POST["temp"]);
    $hum  = test_input($_POST["hum"]);
    $lux  = test_input($_POST["lux"]);
    $lvl  = test_input($_POST["lvl"]);
    $pres = test_input($_POST["pres"]); 
    $stmt->bind_param("dddid", $temp, $hum, $lux, $lvl, $pres);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'New record created successfully.';
    } else {
        throw new Exception('Statement execution failed: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // หากเกิด Error ใดๆ ในบล็อก try จะถูกดักจับที่นี่
    // http_response_code(500); // สามารถเปิดใช้งานเพื่อส่งสถานะ 500 กลับไปได้
    $response['message'] = 'Database Error: ' . $e->getMessage();
}

// --- 5. ส่งผลลัพธ์กลับไปในรูปแบบ JSON ---
echo json_encode($response);

// ฟังก์ชันสำหรับทำความสะอาดข้อมูล (เหมือนเดิม)
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>