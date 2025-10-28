<?php

header('Content-Type: application/json');
require_once('connect.php'); 

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

$range = isset($_GET['range']) ? $_GET['range'] : '7'; 

$response = [];
$data = [];
$sql = "";

if ($range == '7') {
    $sql = "SELECT * FROM (
                SELECT temp, hum, pres, lvl, lux, datetime, save_id 
                FROM SensorForecast_Saved  
                ORDER BY save_id DESC 
                LIMIT 7
            ) AS sub
            ORDER BY sub.save_id ASC";

} else {
    $sql = "SELECT temp, hum, pres, lvl, lux, datetime 
            FROM SensorForecast         
            ORDER BY datetime ASC";    
}

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
    $response['message'] = 'Query failed: ' . $conn->error . ' (SQL: ' . $sql . ')';
}

$conn->close();
echo json_encode($response);
?>