<?php
header('Content-Type: application/json');

require_once '../db_conn.php';

// ตรวจสอบว่า request method เป็น POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Please use POST.']);
    exit();
}

// รับข้อมูล POST ที่ส่งมาจาก Arduino
$temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
$ph_value = isset($_POST['ph_value']) ? floatval($_POST['ph_value']) : null;
$turbidity = isset($_POST['turbidity']) ? floatval($_POST['turbidity']) : null;

// ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วนหรือไม่
if ($temperature === null || $ph_value === null || $turbidity === null) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required POST parameters.']);
    exit();
}

try {
    // ใช้ PDO เพื่อเตรียมและรันคำสั่ง SQL
    $stmt = $conn->prepare("INSERT INTO sensor_data (temperature, ph_value, turbidity) VALUES (?, ?, ?)");
    
    // Bind ค่าและ execute
    $stmt->execute([$temperature, $ph_value, $turbidity]);

    echo json_encode(['status' => 'success', 'message' => 'Data saved successfully.']);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>
