<?php
// ตั้งค่า header ให้เป็น JSON
header('Content-Type: application/json; charset=utf-8');
require_once 'connect.php'; 
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit; 
}

$conn->set_charset("utf8");
$sql = "SELECT id, datetime, temp, hum, pres, lvl, lux FROM SensorForecast ORDER BY datetime DESC";
$result = $conn->query($sql);

$data = []; 

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $conn->error]);
}

$conn->close();

?>