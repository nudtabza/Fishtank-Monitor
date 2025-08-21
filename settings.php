<?php
session_start();
// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // ถ้ายังไม่ได้ล็อกอิน ให้ redirect ไปหน้า Login
    exit();
}

// Include database connection
require_once 'db_conn.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$temp_min = '';
$temp_max = '';
$ph_min = '';
$ph_max = '';
$turbidity_max = '';

try {
    // ดึงข้อมูลการตั้งค่าเกณฑ์ปัจจุบันของผู้ใช้ด้วย PDO
    $stmt = $conn->prepare("SELECT temp_min, temp_max, ph_min, ph_max, turbidity_max FROM user_thresholds WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // ดึงผลลัพธ์มาใส่ในตัวแปร
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $temp_min = $result['temp_min'];
        $temp_max = $result['temp_max'];
        $ph_min = $result['ph_min'];
        $ph_max = $result['ph_max'];
        $turbidity_max = $result['turbidity_max'];
    }
} catch (PDOException $e) {
    // สามารถจัดการข้อผิดพลาดได้ที่นี่ ถ้าจำเป็น
    error_log("Database error: " . $e->getMessage());
}

$conn = null; // ปิดการเชื่อมต่อ
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่า - ระบบตรวจสอบคุณภาพน้ำตู้ปลา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light-blue">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('settingsForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // หยุดการ submit แบบปกติ
            const formData = new FormData(this);
            const messageDiv = document.getElementById('settingsMessage');
            messageDiv.classList.add('d-none'); // ซ่อนข้อความเก่า

            try {
                const response = await fetch('api/save_settings.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('alert-danger');
                    messageDiv.classList.add('alert-success');
                    messageDiv.classList.remove('d-none');
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('alert-success');
                    messageDiv.classList.add('alert-danger');
                    messageDiv.classList.remove('d-none');
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์';
                messageDiv.classList.remove('alert-success');
                messageDiv.classList.add('alert-danger');
                messageDiv.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
