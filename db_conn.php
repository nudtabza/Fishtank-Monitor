<?php
// แก้ไขโค้ดนี้เพื่อใช้ Pooler Connection ที่ถูกต้อง
$host = 'aws-0-ap-southeast-1.pooler.supabase.com'; // Host ที่ถูกต้อง
$port = '5432'; // Port ที่ถูกต้อง
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

// กำหนดตัวแปรสำหรับเก็บสถานะการเชื่อมต่อ
$conn = null;

try {
    // สร้าง DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    
    // สร้างการเชื่อมต่อ PDO พร้อมกับบังคับใช้ SSL
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // เพิ่มบรรทัดนี้เพื่อบังคับใช้ SSL
        PDO::PGSQL_ATTR_SSLMODE => 'require'
    ]);
} catch (PDOException $e) {
    // หากการเชื่อมต่อล้มเหลว จะไม่ echo อะไรออกมา
}
?>
