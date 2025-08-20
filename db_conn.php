<?php
$servername = "sql306.infinityfree.com"; // Hostname ที่ได้จาก Railway
$port = 3306; // Port ที่ได้จาก Railway
$username = "if0_39512375"; // Username ที่ได้จาก Railway
$password = "NudNud123"; // รหัสผ่านจาก Railway (MYSQL_ROOT_PASSWORD)
$dbname = "if0_39512375_fishtank_monitor"; // ชื่อฐานข้อมูลบน Railway

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port); // เพิ่ม $port เข้าไปใน parameter สุดท้าย

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Set character set to UTF-8
$conn->set_charset("utf8mb4");
?>