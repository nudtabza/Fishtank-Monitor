<?php
// กำหนดข้อมูลการเชื่อมต่อฐานข้อมูลตามรูปภาพที่ให้มา
$host = "dpg-d2gb4fvdiees73dashs0.oregon-postgres.render.com"; // แก้ไขตามในรูป
$port = "5432";
$dbname = "fishtank_monitor";
$user = "nudtabza";
$password = "2gR0SGTsc1hORz1KZNCRulU7J93IVDSZ";

try {
    // สร้าง DSN (Data Source Name) สำหรับ PostgreSQL
    // ใช้ PDO เพื่อให้สามารถทำงานร่วมกับไฟล์อื่นๆ ในระบบได้
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password;sslmode=require";
    
    // สร้าง PDO instance
    $conn = new PDO($dsn);
    
    // ตั้งค่าโหมดการแสดงข้อผิดพลาดของ PDO เป็น exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // แสดงข้อความผิดพลาดหากเชื่อมต่อไม่สำเร็จ
    die("Error: Could not connect. " . $e->getMessage());
}
?>
