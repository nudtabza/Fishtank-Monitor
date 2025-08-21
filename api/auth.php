<?php
// auth.php

require_once 'db_conn.php';

// ตั้งค่า Header สำหรับ CORS และ Content-Type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if ($action === 'register') {
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Sanitize and validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["message" => "รูปแบบอีเมลไม่ถูกต้อง"]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(["message" => "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว"]);
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password])) {
        echo json_encode(["message" => "ลงทะเบียนสำเร็จ!", "success" => true]);
    } else {
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการลงทะเบียน"]);
    }
    
    // Check for potential errors
    if ($stmt->errorCode() !== '00000') {
        error_log("DB Error: " . print_r($stmt->errorInfo(), true));
    }

} elseif ($action === 'login') {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        echo json_encode(["message" => "เข้าสู่ระบบสำเร็จ!", "success" => true]);
    } else {
        echo json_encode(["message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"]);
    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
}
?>
