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

// ลบบรรทัดนี้: ไม่จำเป็นต้องดึง line_notify_token แล้ว
// $line_notify_token = ''; 

$temp_min = '';
$temp_max = '';
$ph_min = '';
$ph_max = '';
$turbidity_max = '';

// ลบโค้ดส่วนนี้ออกไป: ไม่ต้องดึง line_notify_token จากตาราง users แล้ว
// $stmt = $conn->prepare("SELECT line_notify_token FROM users WHERE id = ?");
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $stmt->bind_result($line_notify_token);
// $stmt->fetch();
// $stmt->close();

// ดึงข้อมูลการตั้งค่าเกณฑ์ปัจจุบันของผู้ใช้ (โค้ดนี้ยังคงเดิม)
$stmt = $conn->prepare("SELECT temp_min, temp_max, ph_min, ph_max, turbidity_max FROM user_thresholds WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($temp_min, $temp_max, $ph_min, $ph_max, $turbidity_max);
$stmt->fetch();
$stmt->close();

$conn->close(); // ปิดการเชื่อมต่อหลังจากดึงข้อมูลเสร็จ
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-fish me-2"></i> Aquarium Monitor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-chart-line me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="settings.php"><i class="fas fa-cogs me-1"></i> ตั้งค่า</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-4">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1 class="display-4 text-blue"><i class="fas fa-cogs me-2"></i> ตั้งค่าระบบ</h1>
                <p class="lead text-muted">กำหนดค่าเกณฑ์การแจ้งเตือน</p> </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mb-4">
                    <div class="card-header bg-gradient-chart-header p-4">
                        <h4 class="mb-0 text-white"><i class="fas fa-sliders-h me-2"></i> เกณฑ์การแจ้งเตือน</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="settingsForm">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                            <h5><i class="fas fa-thermometer-half me-2 text-info"></i> อุณหภูมิ (°C)</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tempMin" class="form-label">ค่าต่ำสุดที่ยอมรับได้</label>
                                    <input type="number" step="0.1" class="form-control" id="tempMin" name="temp_min" value="<?php echo htmlspecialchars($temp_min); ?>" placeholder="เช่น 25.0">
                                </div>
                                <div class="col-md-6">
                                    <label for="tempMax" class="form-label">ค่าสูงสุดที่ยอมรับได้</label>
                                    <input type="number" step="0.1" class="form-control" id="tempMax" name="temp_max" value="<?php echo htmlspecialchars($temp_max); ?>" placeholder="เช่น 28.0">
                                </div>
                            </div>

                            <h5 class="mt-4"><i class="fas fa-flask me-2 text-primary"></i> ค่า pH</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phMin" class="form-label">ค่าต่ำสุดที่ยอมรับได้</label>
                                    <input type="number" step="0.01" class="form-control" id="phMin" name="ph_min" value="<?php echo htmlspecialchars($ph_min); ?>" placeholder="เช่น 6.5">
                                </div>
                                <div class="col-md-6">
                                    <label for="phMax" class="form-label">ค่าสูงสุดที่ยอมรับได้</label>
                                    <input type="number" step="0.01" class="form-control" id="phMax" name="ph_max" value="<?php echo htmlspecialchars($ph_max); ?>" placeholder="เช่น 7.5">
                                </div>
                            </div>

                            <h5 class="mt-4"><i class="fas fa-cloud me-2 text-secondary"></i> ความขุ่น (%)</h5>
                            <div class="mb-3">
                                <label for="turbidityMax" class="form-label">ค่าสูงสุดที่ยอมรับได้</label>
                                <input type="number" step="0.1" class="form-control" id="turbidityMax" name="turbidity_max" value="<?php echo htmlspecialchars($turbidity_max); ?>" placeholder="เช่น 20.0">
                                <div class="form-text">เมื่อค่าความขุ่นเกินกว่านี้ จะมีการแจ้งเตือน</div>
                            </div>

                            <div id="settingsMessage" class="alert d-none mt-4" role="alert"></div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i> บันทึกการตั้งค่า</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-dark-blue text-white-50 text-center">
        <div class="container">
            <span>&copy; 2025 Aquarium Monitor. Powered by ESP32 & PHP.</span>
        </div>
    </footer>

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