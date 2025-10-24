<?php
// get_saved_data.php

header('Content-Type: application/json');

require_once('connect.php'); // เรียกใช้ไฟล์เชื่อมต่อ

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

$response = [];
$data = [];

// --- ✨ ส่วนที่แก้ไข ---
// เพิ่ม `pres` เข้าไปในรายการคอลัมน์ที่จะดึงข้อมูล
$sql = "SELECT save_id, temp, hum, pres, lvl, lux, location, datetime 
        FROM SensorForecast_Saved 
        ORDER BY save_id DESC 
        LIMIT 7";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $response['status'] = 'success';
        $response['data'] = $data;
    } else {
        $response['status'] = 'success';
        $response['data'] = []; 
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Query failed: ' . $conn->error;
}

$conn->close();
echo json_encode($response);
?>