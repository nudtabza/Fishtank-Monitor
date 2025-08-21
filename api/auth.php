<?php
session_start();
header('Content-Type: application/json');

// เรียกใช้งานไฟล์เชื่อมต่อฐานข้อมูล
require_once '../db_conn.php';

// เพิ่มการตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn === null) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    // โค้ดสำหรับลงทะเบียนผู้ใช้ใหม่
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว"]);
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ"]);
        } else {
            echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการสมัครสมาชิก"]);
        }
    }
    $stmt->close();
} elseif ($action === 'login') {
    // โค้ดสำหรับเข้าสู่ระบบผู้ใช้
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน"]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $db_username, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows === 1 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $db_username;
        echo json_encode(["status" => "success", "message" => "เข้าสู่ระบบสำเร็จ"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"]);
    }
    $stmt->close();
}
?>
