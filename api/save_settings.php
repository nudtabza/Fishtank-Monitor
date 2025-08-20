<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

require_once '../db_conn.php';

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลจาก POST request (ตัด line_notify_token ออกไป)
// $line_notify_token = $_POST['line_notify_token'] ?? null; // ลบบรรทัดนี้
$temp_min = $_POST['temp_min'] ?? null;
$temp_max = $_POST['temp_max'] ?? null;
$ph_min = $_POST['ph_min'] ?? null;
$ph_max = $_POST['ph_max'] ?? null;
$turbidity_max = $_POST['turbidity_max'] ?? null;

// Convert empty strings to NULL for database (if column allows NULL)
$temp_min = ($temp_min === '') ? NULL : floatval($temp_min);
$temp_max = ($temp_max === '') ? NULL : floatval($temp_max);
$ph_min = ($ph_min === '') ? NULL : floatval($ph_min);
$ph_max = ($ph_max === '') ? NULL : floatval($ph_max);
$turbidity_max = ($turbidity_max === '') ? NULL : floatval($turbidity_max);
// $line_notify_token = ($line_notify_token === '') ? NULL : $line_notify_token; // ลบบรรทัดนี้


try {
    // --- ไม่ต้องอัปเดต LINE Notify Token ใน 'users' table อีกแล้ว ---
    // $stmt = $conn->prepare("UPDATE users SET line_notify_token = ? WHERE id = ?"); // ลบบรรทัดนี้
    // $stmt->bind_param("si", $line_notify_token, $user_id); // ลบบรรทัดนี้
    // $stmt->execute(); // ลบบรรทัดนี้
    // $stmt->close(); // ลบบรรทัดนี้

    // --- Insert or Update thresholds in 'user_thresholds' table (ยังคงเดิม) ---
    $stmt = $conn->prepare("INSERT INTO user_thresholds (user_id, temp_min, temp_max, ph_min, ph_max, turbidity_max)
                            VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                            temp_min = VALUES(temp_min),
                            temp_max = VALUES(temp_max),
                            ph_min = VALUES(ph_min),
                            ph_max = VALUES(ph_max),
                            turbidity_max = VALUES(turbidity_max)");
    $stmt->bind_param("iddddd",
        $user_id,
        $temp_min,
        $temp_max,
        $ph_min,
        $ph_max,
        $turbidity_max
    );
    $stmt->execute();
    $stmt->close();

    echo json_encode(["status" => "success", "message" => "บันทึกการตั้งค่าเกณฑ์เรียบร้อยแล้ว!"]); // เปลี่ยนข้อความ

} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage()]);
} finally {
    $conn->close();
}
?>