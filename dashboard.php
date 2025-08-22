<?php
session_start();
// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // ถ้ายังไม่ได้ล็อกอิน ให้ redirect ไปหน้า Login
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? 'ผู้ใช้');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด - ระบบตรวจสอบคุณภาพน้ำตู้ปลา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .card-body .display-6 {
            font-size: 2.5rem;
        }

        .card-body .text-white-50 {
            opacity: 0.7;
        }
    </style>
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
                        <a class="nav-link active" aria-current="page" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>แดชบอร์ด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cogs me-1"></i>ตั้งค่า
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i><?php echo $username; ?>
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
                <h1 class="mt-4"><i class="fas fa-tachometer-alt me-2"></i>แดชบอร์ด</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">ภาพรวมข้อมูลคุณภาพน้ำ</li>
                </ol>
                <div class="row">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card bg-primary text-white h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 display-4"><i class="fas fa-thermometer-half"></i></div>
                                    <div>
                                        <div class="text-white-50 small">อุณหภูมิ</div>
                                        <div class="display-6 fw-bold" id="temperature">-°C</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card bg-success text-white h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 display-4"><i class="fas fa-tint"></i></div>
                                    <div>
                                        <div class="text-white-50 small">ค่า pH</div>
                                        <div class="display-6 fw-bold" id="ph_value">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card bg-warning text-white h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 display-4"><i class="fas fa-cloud"></i></div>
                                    <div>
                                        <div class="text-white-50 small">ความขุ่น</div>
                                        <div class="display-6 fw-bold" id="turbidity">- NTU</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg border-0 h-100">
                            <div class="card-header bg-dark text-white"><i class="fas fa-chart-area me-1"></i> กราฟอุณหภูมิย้อนหลัง</div>
                            <div class="card-body"><canvas id="temperatureChart"></canvas></div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg border-0 h-100">
                            <div class="card-header bg-dark text-white"><i class="fas fa-chart-bar me-1"></i> กราฟค่า pH ย้อนหลัง</div>
                            <div class="card-body"><canvas id="phChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="card shadow-lg border-0 h-100">
                            <div class="card-header bg-dark text-white"><i class="fas fa-chart-line me-1"></i> กราฟความขุ่นย้อนหลัง</div>
                            <div class="card-body"><canvas id="turbidityChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">ลิขสิทธิ์ &copy; เว็บไซต์ของคุณ 2024</div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // กำหนด font-family และสีของตัวอักษรสำหรับ Chart.js
        Chart.defaults.font.family = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.color = '#fff';

        const chartOptions = {
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: 'rgba(255,255,255,0.7)' }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: 'rgba(255,255,255,0.7)' }
                }
            },
            plugins: {
                legend: { display: false }
            },
            maintainAspectRatio: false,
            responsive: true,
        };

        const createGradient = (ctx, color) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, 'rgba(0,0,0,0)');
            return gradient;
        };

        const createChart = (ctx, label, color, historicalData, dataKey) => {
            const labels = historicalData.map(item => new Date(item.timestamp).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }));
            const data = historicalData.map(item => item[dataKey]);

            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: createGradient(ctx, color),
                        borderColor: color,
                        pointRadius: 3,
                        pointBackgroundColor: color,
                        pointBorderColor: 'rgba(255,255,255,0.8)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: color,
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions,
            });
        };

        let temperatureChart, phChart, turbidityChart;

        async function fetchLatestSensorData() {
            try {
                const response = await fetch('api/get_data.php');
                const data = await response.json();
                if (data.status === 'success' && data.data) {
                    document.getElementById('temperature').textContent = `${parseFloat(data.data.temperature).toFixed(1)}°C`;
                    document.getElementById('ph_value').textContent = parseFloat(data.data.ph_value).toFixed(2);
                    document.getElementById('turbidity').textContent = `${parseFloat(data.data.turbidity).toFixed(2)} NTU`;
                }
            } catch (error) {
                console.error('Error fetching latest data:', error);
            }
        }

        async function fetchHistoricalData() {
            try {
                const response = await fetch('api/get_history.php');
                const data = await response.json();
                if (data.status === 'success' && data.data) {
                    const historicalData = data.data;
                    const ctxTemp = document.getElementById('temperatureChart').getContext('2d');
                    const ctxPh = document.getElementById('phChart').getContext('2d');
                    const ctxTurb = document.getElementById('turbidityChart').getContext('2d');

                    if (temperatureChart) temperatureChart.destroy();
                    if (phChart) phChart.destroy();
                    if (turbidityChart) turbidityChart.destroy();

                    temperatureChart = createChart(ctxTemp, 'อุณหภูมิ', '#007bff', historicalData, 'temperature');
                    phChart = createChart(ctxPh, 'ค่า pH', '#198754', historicalData, 'ph_value');
                    turbidityChart = createChart(ctxTurb, 'ความขุ่น', '#ffc107', historicalData, 'turbidity');
                } else {
                    console.error('Error in historical data response:', data.message);
                }
            } catch (error) {
                console.error('Error fetching historical data:', error);
            }
        }

        fetchLatestSensorData();
        fetchHistoricalData();

        setInterval(fetchLatestSensorData, 5000);
        setInterval(fetchHistoricalData, 60000);
    </script>
</body>
</html>
