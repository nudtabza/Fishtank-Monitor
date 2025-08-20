<?php
// api/get_data.php

session_start(); // เริ่มต้น session เพื่อใช้ตรวจสอบการ Login
header('Content-Type: application/json');

// ตรวจสอบการ Login (แนะนำให้เปิดใช้งานใน Production เพื่อความปลอดภัย)
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
//     exit();
// }

// Include the database connection file
// เส้นทาง: '../db_conn.php' หมายถึง ถอยออกจากโฟลเดอร์ 'api' ไปหนึ่งระดับ
require_once '../db_conn.php';

// Fetch latest sensor data
$sql = "SELECT temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "error", "message" => "No data found"]);
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>