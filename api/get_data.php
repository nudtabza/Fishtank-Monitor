<?php
// api/get_data.php

session_start();
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบการเข้าสู่ระบบ
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
//     exit();
// }

// เรียกใช้งานไฟล์เชื่อมต่อฐานข้อมูล
require_once '../db_conn.php';

try {
    // SQL query เพื่อดึงข้อมูลเซ็นเซอร์ล่าสุด 1 รายการ
    $sql = "SELECT temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // ส่งข้อมูลเป็น JSON
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        // ถ้าไม่พบข้อมูล
        echo json_encode(["status" => "success", "data" => (object)[]]);
    }
} catch (PDOException $e) {
    // จัดการข้อผิดพลาดที่เกี่ยวกับฐานข้อมูล
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn = null; // ปิดการเชื่อมต่อ
?>
