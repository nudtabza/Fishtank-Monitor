<?php
session_start();
header('Content-Type: application/json');

require_once '../db_conn.php';

function sendJsonResponse($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        sendJsonResponse("error", "กรุณากรอกข้อมูลให้ครบถ้วน");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            sendJsonResponse("error", "ชื่อผู้ใช้หรืออีเมลนี้มีผู้ใช้งานแล้ว");
        }

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            sendJsonResponse("success", "สมัครสมาชิกสำเร็จ");
        } else {
            sendJsonResponse("error", "เกิดข้อผิดพลาดในการสมัครสมาชิก");
        }

    } catch (PDOException $e) {
        sendJsonResponse("error", "Database error: " . $e->getMessage());
    }
    
} elseif ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        sendJsonResponse("error", "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน");
    }

    try {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            sendJsonResponse("success", "เข้าสู่ระบบสำเร็จ");
        } else {
            sendJsonResponse("error", "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
        }

    } catch (PDOException $e) {
        sendJsonResponse("error", "Database error: " . $e->getMessage());
    }

} else {
    sendJsonResponse("error", "Invalid action");
}

$conn = null;
?>