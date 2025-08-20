<?php
session_start();
header('Content-Type: application/json');

// Include the database connection file
// ตรวจสอบเส้นทางให้ถูกต้อง: '../db_conn.php' หมายถึง ถอยออกจากโฟลเดอร์ 'api' ไปหนึ่งระดับ
require_once '../db_conn.php';

// เมื่อเรียกใช้ db_conn.php แล้ว ตัวแปร $conn จะพร้อมใช้งานทันที
// ไม่ต้องประกาศ $servername, $username, $password, $dbname หรือสร้าง $conn ใหม่ในไฟล์นี้

$action = $_GET['action'] ?? ''; // ดึงค่า action จาก URL

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
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรืออีเมลนี้มีผู้ใช้งานแล้ว"]);
        $stmt->close();
        $conn->close(); // ปิดการเชื่อมต่อเมื่อเกิดข้อผิดพลาดและ exit
        exit();
    }
    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $email);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ"]);
    } else {
        echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $stmt->error]);
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
    $stmt->fetch(); // ดึงผลลัพธ์มาใส่ตัวแปร

    if ($stmt->num_rows === 1 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $db_username;
        echo json_encode(["status" => "success", "message" => "เข้าสู่ระบบสำเร็จ"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"]);
    }
    $stmt->close();

} else {
    // ถ้าไม่มี action ที่ถูกต้อง (เช่น ไม่มี ?action=register หรือ ?action=login ใน URL)
    echo json_encode(["status" => "error", "message" => "คำขอไม่ถูกต้อง"]);
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูลเมื่อเสร็จสิ้นการทำงาน
?>