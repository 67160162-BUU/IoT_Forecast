<?php
header('Content-Type: application/json');
include 'connect.php';

$response = [];

try {
    // ดึงข้อมูลแถวล่าสุดเพียงแถวเดียว
    $sql = "SELECT temp, hum, lux, lvl, pres, datetime FROM SensorForecast ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'status' => 'success',
            'data' => [
                'temperature' => $row['temp'],
                'humidity'    => $row['hum'],
                'lux'         => $row['lux'],
                'light_level' => $row['lvl'],
                'pressure'    => $row['pres'],
                'timestamp'   => $row['datetime']
            ]
        ];
    } else {
        throw new Exception("No data found.");
    }
    $conn->close();
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>