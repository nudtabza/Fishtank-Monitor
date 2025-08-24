<?php
session_start();
// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // ถ้ายังไม่ได้ล็อกอิน ให้ redirect ไปหน้า Login
    exit();
}

require_once 'db_conn.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// กำหนดค่าเริ่มต้นเป็นสตริงว่างเปล่า
$temp_min = '';
$temp_max = '';
$ph_min = '';
$ph_max = '';
$turbidity_max = '';

try {
    // ดึงข้อมูลการตั้งค่าเกณฑ์ปัจจุบันของผู้ใช้ด้วย PDO
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
} catch (PDOException $e) {
    // ถ้ามีข้อผิดพลาดเกี่ยวกับฐานข้อมูล
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่า - ระบบตรวจสอบคุณภาพน้ำตู้ปลา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-dark-bg">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-water me-2"></i>Fish Tank Monitor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>แดชบอร์ด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="settings.php">
                            <i class="fas fa-cogs me-1"></i>ตั้งค่า
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                            <li><a class="dropdown-item" href="settings.php">ตั้งค่า</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="main-content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4"><i class="fas fa-cogs me-2"></i>ตั้งค่า</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">ตั้งค่าเกณฑ์การแจ้งเตือน</li>
                </ol>

                <div class="card shadow-lg border-0 rounded-lg mt-5 mb-4">
                    <div class="card-header bg-dark text-white"><h3 class="fw-light my-4">ตั้งค่าเกณฑ์คุณภาพน้ำ</h3></div>
                    <div class="card-body">
                        <div id="message" class="alert d-none" role="alert"></div>
                        <form id="settingsForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="temp_min" name="temp_min" type="number" step="0.1" value="<?php echo htmlspecialchars($temp_min); ?>" placeholder="อุณหภูมิต่ำสุด">
                                        <label for="temp_min">อุณหภูมิต่ำสุด (°C)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="temp_max" name="temp_max" type="number" step="0.1" value="<?php echo htmlspecialchars($temp_max); ?>" placeholder="อุณหภูมิสูงสุด">
                                        <label for="temp_max">อุณหภูมิสูงสุด (°C)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="ph_min" name="ph_min" type="number" step="0.1" value="<?php echo htmlspecialchars($ph_min); ?>" placeholder="ค่า pH ต่ำสุด">
                                        <label for="ph_min">ค่า pH ต่ำสุด</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="ph_max" name="ph_max" type="number" step="0.1" value="<?php echo htmlspecialchars($ph_max); ?>" placeholder="ค่า pH สูงสุด">
                                        <label for="ph_max">ค่า pH สูงสุด</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="turbidity_max" name="turbidity_max" type="number" step="0.1" value="<?php echo htmlspecialchars($turbidity_max); ?>" placeholder="ความขุ่นสูงสุด">
                                        <label for="turbidity_max">ความขุ่นสูงสุด (NTU)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                <button class="btn btn-primary btn-lg w-100" type="submit"><i class="fas fa-save me-2"></i>บันทึกการตั้งค่า</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">ลิขสิทธิ์ &copy; FishTank_Moniter Real-time 2025</div>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('settingsForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const messageDiv = document.getElementById('message');

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

