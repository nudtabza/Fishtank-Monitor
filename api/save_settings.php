<?php
session_start();
header('Content-Type: application/json');

function sendJsonResponse($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    sendJsonResponse("error", "Unauthorized access.");
}

require_once '../db_conn.php';

$user_id = $_SESSION['user_id'];

$temp_min = $_POST['temp_min'] ?? null;
$temp_max = $_POST['temp_max'] ?? null;
$ph_min = $_POST['ph_min'] ?? null;
$ph_max = $_POST['ph_max'] ?? null;
$turbidity_max = $_POST['turbidity_max'] ?? null;

$temp_min = ($temp_min === '') ? null : floatval($temp_min);
$temp_max = ($temp_max === '') ? null : floatval($temp_max);
$ph_min = ($ph_min === '') ? null : floatval($ph_min);
$ph_max = ($ph_max === '') ? null : floatval($ph_max);
$turbidity_max = ($turbidity_max === '') ? null : floatval($turbidity_max);

try {
    $sql = "INSERT INTO user_thresholds (user_id, temp_min, temp_max, ph_min, ph_max, turbidity_max)
            VALUES (?, ?, ?, ?, ?, ?)
            ON CONFLICT (user_id) DO UPDATE SET
            temp_min = EXCLUDED.temp_min,
            temp_max = EXCLUDED.temp_max,
            ph_min = EXCLUDED.ph_min,
            ph_max = EXCLUDED.ph_max,
            turbidity_max = EXCLUDED.turbidity_max";
    
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([
        $user_id, 
        $temp_min, 
        $temp_max, 
        $ph_min, 
        $ph_max, 
        $turbidity_max
    ])) {
        sendJsonResponse("success", "บันทึกการตั้งค่าสำเร็จ");
    } else {
        sendJsonResponse("error", "เกิดข้อผิดพลาดในการบันทึกการตั้งค่า");
    }

} catch (PDOException $e) {
    http_response_code(500);
    sendJsonResponse("error", "Database error: " . $e->getMessage());
}
$conn = null;
?>