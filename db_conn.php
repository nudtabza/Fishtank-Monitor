<?php

$host = "dpg-d2gb4fvdiees73dashs0-a.oregon-postgres.render.com"; // Hostname จาก Render
$port = "5432"; // Port จาก Render
$dbname = "fishtank_monitor"; // ชื่อฐานข้อมูล
$user = "nudtabza"; // Username
$password = "2gR0SGTsc1hORz1KZNCRulU7J93IVDSZ"; // รหัสผ่านที่ถูกต้อง

// สร้าง Connection String
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// เชื่อมต่อฐานข้อมูล
$conn = pg_connect($conn_string);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . pg_last_error());
} else {
    echo "Connected successfully to PostgreSQL database.";
}

// ปิดการเชื่อมต่อเมื่อใช้งานเสร็จ
// pg_close($conn);

?>
