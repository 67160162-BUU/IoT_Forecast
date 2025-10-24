<?php
$servername = "localhost";
$dbname = "s67160162";
$username = "s67160162";
$password = "xPnHASsm";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    // ใช้ die() เพื่อหยุดการทำงานทันทีหากเชื่อมต่อไม่ได้
    die("Connection failed: " . $conn->connect_error);
}
?>