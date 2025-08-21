<?php
// auth.php

session_start();
require_once './/db_conn.php';

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
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit();
}

// ดึงค่า 'action' จาก URL (query string)
$action = $_GET['action'] ?? '';

if ($action === 'register') {
    // ใช้ $_POST ในการรับข้อมูลจากฟอร์ม FormData
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Sanitize and validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "รูปแบบอีเมลไม่ถูกต้อง"]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว"]);
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password])) {
        echo json_encode(["status" => "success", "message" => "ลงทะเบียนสำเร็จ!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการลงทะเบียน"]);
    }
    
} elseif ($action === 'login') {
    // ใช้ $_POST ในการรับข้อมูลจากฟอร์ม FormData
    // ตรวจสอบว่าชื่อฟิลด์ถูกต้องกับใน index.php: username ไม่ใช่อีเมล
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน"]);
        exit();
    }

    // ใช้ 'username' ในการค้นหาในฐานข้อมูล
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        echo json_encode(["status" => "success", "message" => "เข้าสู่ระบบสำเร็จ!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"]);
    }

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

// ปิดการเชื่อมต่อ
$conn = null;
?>

