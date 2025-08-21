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
    <style>
        :root {
            --dark-bg: #121212;
            --dark-card: #1e1e1e;
            --dark-text: #e0e0e0;
            --dark-secondary-text: #b0b0b0;
            --blue-accent: #007bff;
            --blue-accent-hover: #0056b3;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            --card-hover-scale: scale(1.02);
        }

        body {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        /* Sidebar & Wrapper */
        .d-flex {
            display: flex;
            min-height: 100vh;
        }

        #wrapper {
            transition: margin 0.25s ease-out;
        }

        #sidebar-wrapper {
            width: 250px;
            background-color: var(--dark-card);
            border-right: 1px solid #2e2e2e;
            transition: margin 0.25s ease-out;
        }

        .sidebar-heading {
            font-size: 1.5rem;
            color: var(--blue-accent);
            font-weight: 600;
            border-bottom: 1px solid #2e2e2e;
        }

        #sidebar-wrapper .list-group-item {
            background-color: var(--dark-card);
            color: var(--dark-secondary-text);
            border: none;
            transition: background-color 0.2s ease-in-out;
        }

        #sidebar-wrapper .list-group-item:hover,
        #sidebar-wrapper .list-group-item.active {
            background-color: #2e2e2e;
            color: var(--blue-accent);
        }

        #page-content-wrapper {
            flex-grow: 1;
            padding: 20px;
        }

        /* Navbar */
        .navbar {
            background-color: var(--dark-card) !important;
            border-bottom: 1px solid #2e2e2e;
            margin-bottom: 2rem;
        }

        .navbar-brand, .nav-link {
            color: var(--dark-text) !important;
        }

        .btn-primary {
            background-color: var(--blue-accent);
            border-color: var(--blue-accent);
        }

        /* Cards */
        .card {
            background-color: var(--dark-card);
            color: var(--dark-text);
            border: none;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: var(--card-hover-scale);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--dark-text);
        }

        .form-control, .input-group-text {
            background-color: #2e2e2e;
            color: var(--dark-text);
            border: 1px solid #444;
        }
        .form-control::placeholder {
            color: #888;
        }
    </style>
</head>
<body class="bg-dark">
    <div class="d-flex" id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading p-4">
                <i class="fas fa-water me-2"></i> Dashboard
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action p-3" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> ข้อมูลล่าสุด</a>
                <a class="list-group-item list-group-item-action p-3 active" href="settings.php"><i class="fas fa-cog me-2"></i> ตั้งค่า</a>
                <a class="list-group-item list-group-item-action p-3" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-dark border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item">
                                <span class="nav-link text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid p-4">
                <h1 class="mt-4 text-center text-light">ตั้งค่าเกณฑ์คุณภาพน้ำ</h1>
                <p class="text-center text-muted">กำหนดค่าสูงสุด-ต่ำสุดสำหรับแต่ละพารามิเตอร์</p>

                <div class="row justify-content-center mt-4">
                    <div class="col-lg-8">
                        <div class="card p-4 shadow-sm">
                            <div class="card-body">
                                <div id="settingsMessage" class="alert d-none" role="alert"></div>

                                <form id="settingsForm">
                                    <div class="mb-4">
                                        <label for="temp_min" class="form-label text-light">อุณหภูมิ (°C)</label>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="number" step="0.1" class="form-control" id="temp_min" name="temp_min" placeholder="ค่าต่ำสุด" value="<?php echo htmlspecialchars($temp_min); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" step="0.1" class="form-control" id="temp_max" name="temp_max" placeholder="ค่าสูงสุด" value="<?php echo htmlspecialchars($temp_max); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="ph_min" class="form-label text-light">ค่า pH</label>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="number" step="0.1" class="form-control" id="ph_min" name="ph_min" placeholder="ค่าต่ำสุด" value="<?php echo htmlspecialchars($ph_min); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" step="0.1" class="form-control" id="ph_max" name="ph_max" placeholder="ค่าสูงสุด" value="<?php echo htmlspecialchars($ph_max); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="turbidity_max" class="form-label text-light">ความขุ่น (NTU)</label>
                                        <input type="number" step="0.1" class="form-control" id="turbidity_max" name="turbidity_max" placeholder="ค่าสูงสุด" value="<?php echo htmlspecialchars($turbidity_max); ?>">
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg mt-3">บันทึกการตั้งค่า</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('settingsForm').addEventListener('submit', async function(event) {
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

        // Toggle the side navigation
        const sidebarToggle = document.body.querySelector('#sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                localStorage.setItem('sb-sidenav-toggle', document.body.classList.contains('sb-sidenav-toggled'));
            });
        }
    </script>
</body>
</html>
