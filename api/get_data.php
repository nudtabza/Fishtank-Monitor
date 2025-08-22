<?php
// api/get_data.php

session_start();
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

require_once '../db_conn.php';

$user_id = $_SESSION['user_id'];
$alert_message = null; // เริ่มต้นข้อความแจ้งเตือนเป็น null

try {
    // 1. ดึงข้อมูลเกณฑ์ของผู้ใช้จากฐานข้อมูล
    $stmt_threshold = $conn->prepare("SELECT temp_min, temp_max, ph_min, ph_max, turbidity_max FROM user_thresholds WHERE user_id = ?");
    $stmt_threshold->execute([$user_id]);
    $thresholds = $stmt_threshold->fetch(PDO::FETCH_ASSOC);

    // 2. ดึงข้อมูลเซ็นเซอร์ล่าสุด 1 รายการ
    $sql_data = "SELECT temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY id DESC LIMIT 1";
    $stmt_data = $conn->prepare($sql_data);
    $stmt_data->execute();
    $data = $stmt_data->fetch(PDO::FETCH_ASSOC);

    if ($data && $thresholds) {
        $current_temp = $data['temperature'];
        $current_ph = $data['ph_value'];
        $current_turbidity = $data['turbidity'];

        $temp_min = $thresholds['temp_min'];
        $temp_max = $thresholds['temp_max'];
        $ph_min = $thresholds['ph_min'];
        $ph_max = $thresholds['ph_max'];
        $turbidity_max = $thresholds['turbidity_max'];
        
        // 3. เปรียบเทียบค่าเซ็นเซอร์กับเกณฑ์ที่ตั้งไว้
        $alerts = [];
        if ($temp_min !== null && $current_temp < $temp_min) {
            $alerts[] = "อุณหภูมิต่ำกว่าเกณฑ์: {$current_temp}°C";
        }
        if ($temp_max !== null && $current_temp > $temp_max) {
            $alerts[] = "อุณหภูมิสูงกว่าเกณฑ์: {$current_temp}°C";
        }
        if ($ph_min !== null && $current_ph < $ph_min) {
            $alerts[] = "ค่า pH ต่ำกว่าเกณฑ์: {$current_ph}";
        }
        if ($ph_max !== null && $current_ph > $ph_max) {
            $alerts[] = "ค่า pH สูงกว่าเกณฑ์: {$current_ph}";
        }
        if ($turbidity_max !== null && $current_turbidity > $turbidity_max) {
            $alerts[] = "ค่าความขุ่นสูงกว่าเกณฑ์: {$current_turbidity} NTU";
        }

        // รวมข้อความแจ้งเตือนทั้งหมด
        if (!empty($alerts)) {
            $alert_message = implode(" | ", $alerts);
        }
    }

    // 4. ส่งข้อมูลเป็น JSON พร้อมข้อความแจ้งเตือน (ถ้ามี)
    echo json_encode(["status" => "success", "data" => $data, "alert_message" => $alert_message]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn = null;
?>
