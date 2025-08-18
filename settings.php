<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'db_conn.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$temp_min = '';
$temp_max = '';
$ph_min = '';
$ph_max = '';
$turbidity_max = '';

try {
    $stmt = $conn->prepare("SELECT temp_min, temp_max, ph_min, ph_max, turbidity_max FROM user_thresholds WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $temp_min = $result['temp_min'];
        $temp_max = $result['temp_max'];
        $ph_min = $result['ph_min'];
        $ph_max = $result['ph_max'];
        $turbidity_max = $result['turbidity_max'];
    }
} catch (Exception $e) {
    error_log("Error in settings.php: " . $e->getMessage());
}

$conn = null;
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-water me-2"></i> ระบบตรวจสอบคุณภาพน้ำตู้ปลา
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="settings.php">
                            <i class="fas fa-cog me-1"></i> ตั้งค่า
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-gradient-header p-4">
                        <h3 class="text-center font-weight-light my-4 text-white">
                            <i class="fas fa-sliders-h me-2"></i> ตั้งค่าระบบ
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <form id="settingsForm">
                            <div class="alert d-none" role="alert" id="settingsMessage"></div>
                            <h5 class="mb-3 text-secondary">
                                <i class="fas fa-chart-line me-2"></i> การตั้งค่าเกณฑ์คุณภาพน้ำ
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="temp_min" class="form-label">อุณหภูมิ (ขั้นต่ำ)</label>
                                    <input type="number" class="form-control" id="temp_min" name="temp_min" placeholder="เช่น 24" step="0.1" value="<?= htmlspecialchars($temp_min) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="temp_max" class="form-label">อุณหภูมิ (สูงสุด)</label>
                                    <input type="number" class="form-control" id="temp_max" name="temp_max" placeholder="เช่น 28" step="0.1" value="<?= htmlspecialchars($temp_max) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="ph_min" class="form-label">ค่า pH (ขั้นต่ำ)</label>
                                    <input type="number" class="form-control" id="ph_min" name="ph_min" placeholder="เช่น 6.5" step="0.1" value="<?= htmlspecialchars($ph_min) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="ph_max" class="form-label">ค่า pH (สูงสุด)</label>
                                    <input type="number" class="form-control" id="ph_max" name="ph_max" placeholder="เช่น 7.5" step="0.1" value="<?= htmlspecialchars($ph_max) ?>">
                                </div>
                                <div class="col-12">
                                    <label for="turbidity_max" class="form-label">ความขุ่น (สูงสุด)</label>
                                    <input type="number" class="form-control" id="turbidity_max" name="turbidity_max" placeholder="เช่น 50" step="0.1" value="<?= htmlspecialchars($turbidity_max) ?>">
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-block">บันทึกการตั้งค่า</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settingsForm = document.getElementById('settingsForm');
            settingsForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                const messageDiv = document.getElementById('settingsMessage');
                messageDiv.classList.add('d-none');

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
        });
    </script>
</body>
</html>