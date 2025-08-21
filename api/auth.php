<?php
session_start();
header('Content-Type: application/json');

require_once '../db_conn.php';

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // ใช้ PDO: ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลมีอยู่แล้วหรือไม่
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว"]);
            exit();
        }

        // ใช้ PDO: ลงทะเบียนผู้ใช้ใหม่
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            echo json_encode(["status" => "success", "message" => "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ"]);
        } else {
            echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการสมัครสมาชิก"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }

} elseif ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน"]);
        exit();
    }

    try {
        // ใช้ PDO: ดึงข้อมูลผู้ใช้จาก username
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(["status" => "success", "message" => "เข้าสู่ระบบสำเร็จ"]);
        } else {
            echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
