<?php
// แก้ไขโค้ดนี้เพื่อใช้ Pooler Connection
$host = 'aws-1-ap-southeast-1.pooler.supabase.com'; // หรือชื่อ host ของ pooler ของคุณ
$port = '6543';
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

// กำหนดตัวแปรสำหรับเก็บสถานะการเชื่อมต่อ
$conn = null;

try {
    // สร้าง DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    
    // สร้างการเชื่อมต่อ PDO
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    // หากการเชื่อมต่อล้มเหลว จะไม่ echo อะไรออกมา
}
?>
