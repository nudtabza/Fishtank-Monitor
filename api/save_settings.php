<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

// ⚠️ ตรวจสอบว่ามีไฟล์ db_conn.php อยู่จริง
require_once '../db_conn.php';

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลจาก POST request
$temp_min = $_POST['temp_min'] ?? null;
$temp_max = $_POST['temp_max'] ?? null;
$ph_min = $_POST['ph_min'] ?? null;
$ph_max = $_POST['ph_max'] ?? null;
$turbidity_max = $_POST['turbidity_max'] ?? null;

// Convert empty strings to NULL for database
$temp_min = ($temp_min === '') ? null : floatval($temp_min);
$temp_max = ($temp_max === '') ? null : floatval($temp_max);
$ph_min = ($ph_min === '') ? null : floatval($ph_min);
$ph_max = ($ph_max === '') ? null : floatval($ph_max);
$turbidity_max = ($turbidity_max === '') ? null : floatval($turbidity_max);

try {
    // ใช้ INSERT ... ON CONFLICT สำหรับ PostgreSQL
    $sql = "INSERT INTO user_thresholds (user_id, temp_min, temp_max, ph_min, ph_max, turbidity_max)
            VALUES (?, ?, ?, ?, ?, ?)
            ON CONFLICT (user_id) DO UPDATE
            SET temp_min = EXCLUDED.temp_min,
                temp_max = EXCLUDED.temp_max,
                ph_min = EXCLUDED.ph_min,
                ph_max = EXCLUDED.ph_max,
                turbidity_max = EXCLUDED.turbidity_max";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $user_id,
        $temp_min,
        $temp_max,
        $ph_min,
        $ph_max,
        $turbidity_max
    ]);

    echo json_encode(["status" => "success", "message" => "บันทึกการตั้งค่าเกณฑ์เรียบร้อยแล้ว!"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage()]);
}

$conn = null; // ปิดการเชื่อมต่อ
?>
