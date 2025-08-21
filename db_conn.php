<?php
// ดึงค่าจาก Environment Variables ใน Render.com
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
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
        // เพิ่มบรรทัดนี้เพื่อบังคับใช้ SSL ซึ่งจำเป็นสำหรับ Supabase
        PDO::PGSQL_ATTR_SSLMODE => 'require'
    ]);
} catch (PDOException $e) {
    // หากการเชื่อมต่อล้มเหลว จะแสดงข้อความผิดพลาด
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => $e->getMessage()
    ]);
    exit;
}
?>
