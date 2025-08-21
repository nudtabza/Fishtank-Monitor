<?php
// แก้ไขโค้ดนี้เพื่อใช้ Pooler Connection
$host = 'aws-1-ap-southeast-1.pooler.supabase.com'; // โฮสต์สำหรับ Transaction Pooler
$port = '6543'; // พอร์ตสำหรับ Transaction Pooler
$db   = getenv('DB_NAME'); // ดึงจาก Environment Variables ใน Render.com
$user = getenv('DB_USER'); // ดึงจาก Environment Variables ใน Render.com
$pass = getenv('DB_PASSWORD'); // ดึงจาก Environment Variables ใน Render.com

// กำหนดตัวแปรสำหรับเก็บสถานะการเชื่อมต่อ
$conn = null;

try {
    // สร้าง DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    
    // สร้างการเชื่อมต่อ PDO พร้อมกับบังคับใช้ SSL
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // เพิ่มบรรทัดนี้เพื่อบังคับใช้ SSL ซึ่งจำเป็นสำหรับ Supabase
        PDO::PGSQL_ATTR_SSLMODE => 'require'
    ]);
} catch (PDOException $e) {
    // หากการเชื่อมต่อล้มเหลว จะไม่มีการ echo อะไรออกมา
    // เพราะไฟล์อื่นๆ ที่เรียกใช้ไฟล์นี้จะจัดการข้อผิดพลาดเอง
}
?>
