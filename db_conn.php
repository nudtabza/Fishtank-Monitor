<?php
$servername = "dpg-d2gb4fvdiees73dashs0-a"; // Hostname ที่ได้จาก Railway
$port = 5432; // Port ที่ได้จาก Railway
$username = "nudtabza"; // Username ที่ได้จาก Railway
$password = "2gR0SGTsc1hORz1KZNCRulU7J93IVDSZ"; // รหัสผ่านจาก Railway (MYSQL_ROOT_PASSWORD)
$dbname = "fishtank_monitor"; // ชื่อฐานข้อมูลบน Railway

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port); // เพิ่ม $port เข้าไปใน parameter สุดท้าย

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Set character set to UTF-8
$conn->set_charset("utf8mb4");

?>
