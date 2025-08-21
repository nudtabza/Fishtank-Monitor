<?php
$host = 'db.blyckkguxpqctpcfebco.supabase.co';
$port = '5432';
$db   = 'postgres';
$user = 'postgres';
$pass = 'oo83EYxDvIzAsZvq';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    // หากเชื่อมต่อสำเร็จ สามารถใส่โค้ดที่นี่เพื่อดำเนินการต่อ
    // ตัวอย่าง: echo json_encode(["success" => true, "message" => "Database connection successful"]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => $e->getMessage()
    ]);
    exit;
}
?>
