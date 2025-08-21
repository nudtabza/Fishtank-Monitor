<?php
session_start();
header('Content-Type: application/json');

require_once '../db_conn.php';

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    // ... (โค้ดส่วนนี้ยังคงใช้ bind_param/mysqli อยู่) ...
    // ต้องแก้ไขให้ใช้ PDO syntax ด้วย
    // ...
} elseif ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน"]);
        exit();
    }

    try {
        // ใช้ PDO Syntax ที่ถูกต้อง
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
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
