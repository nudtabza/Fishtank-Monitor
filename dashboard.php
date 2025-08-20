<?php
session_start();
// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // ถ้ายังไม่ได้ล็อกอิน ให้ redirect ไปหน้า Login
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ระบบตรวจสอบคุณภาพน้ำตู้ปลา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light-blue">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-fish me-2"></i> Aquarium Monitor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php"><i class="fas fa-chart-line me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php"><i class="fas fa-cogs me-1"></i> ตั้งค่า</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1 class="display-4 text-blue"><i class="fas fa-tint me-2"></i> สถานะคุณภาพน้ำตู้ปลาเรียลไทม์</h1>
                <p class="lead text-muted">ข้อมูลอัปเดตล่าสุดจากเซนเซอร์ ESP32 ของคุณ</p>
                <!-- Alert message container -->
                <div id="alert-container" class="alert d-none" role="alert"></div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-lg sensor-card bg-temp">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-thermometer-half fa-3x text-white mb-3"></i>
                        <h5 class="card-title text-white">อุณหภูมิ</h5>
                        <p class="display-3 text-white" id="currentTemp">-- °C</p>
                        <p class="card-text text-white-50">ค่าอุณหภูมิน้ำปัจจุบัน</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-lg sensor-card bg-ph">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-flask fa-3x text-white mb-3"></i>
                        <h5 class="card-title text-white">ค่า pH</h5>
                        <p class="display-3 text-white" id="currentPh">--</p>
                        <p class="card-text text-white-50">ค่ากรด-ด่างของน้ำ</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-lg sensor-card bg-turbidity">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-cloud fa-3x text-white mb-3"></i>
                        <h5 class="card-title text-white">ความขุ่น</h5>
                        <p class="display-3 text-white" id="currentTurbidity">-- %</p>
                        <p class="card-text text-white-50">ระดับความขุ่นของน้ำ</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-gradient-chart-header">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-chart-area me-2"></i> กราฟอุณหภูมิ</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="tempChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-gradient-chart-header">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-chart-line me-2"></i> กราฟค่า pH</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="phChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-gradient-chart-header">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-chart-bar me-2"></i> กราฟความขุ่น</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="turbidityChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header bg-gradient-alert-header">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-bell me-2"></i> การแจ้งเตือนล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="notificationList">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ไม่มีข้อความแจ้งเตือนใหม่
                                <span class="badge bg-secondary rounded-pill">ตอนนี้</span>
                            </li>
                        </ul>
                        <button class="btn btn-outline-info btn-sm mt-3 w-100">ดูประวัติทั้งหมด</button>
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
        // URLs to fetch the latest sensor data and historical data
        const latestDataUrl = "http://fishtankmonitor.wuaze.com/api/get_data.php";
        const historyDataUrl = "http://fishtankmonitor.wuaze.com/api/get_history.php";

        // Chart.js instances
        let tempChart, phChart, turbidityChart;
        let alertContainer = document.getElementById("alert-container");

        // Function to display an alert message
        function showAlert(message, type = 'warning') {
            alertContainer.innerText = message;
            alertContainer.className = `alert alert-${type} d-block`;
        }

        // Function to fetch the latest sensor data
        async function fetchLatestSensorData() {
            try {
                const response = await fetch(latestDataUrl);
                if (!response.ok) {
                    // Show a detailed error message if the network request failed
                    throw new Error(`HTTP error! status: ${response.status} from ${latestDataUrl}`);
                }
                const result = await response.json();

                if (result.status === "success") {
                    const data = result.data;
                    document.getElementById("currentTemp").innerText = data.temperature + " °C";
                    document.getElementById("currentPh").innerText = data.ph_value;
                    document.getElementById("currentTurbidity").innerText = data.turbidity + " %";
                    alertContainer.className = "alert d-none"; // Hide alert on success
                } else {
                    // Show a more user-friendly error message if the API returns an error
                    showAlert(`API Error: ${result.message}`);
                    console.error("API Error:", result.message);
                }
            } catch (error) {
                // Show a user-friendly error message on network failure
                showAlert(`ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้: ${error.message}`, 'danger');
                console.error("Fetch Error:", error);
            }
        }

        // Function to fetch historical data and update the charts
        async function fetchHistoricalData() {
            try {
                const response = await fetch(historyDataUrl);
                if (!response.ok) {
                    // Show a detailed error message if the network request failed
                    throw new Error(`HTTP error! status: ${response.status} from ${historyDataUrl}`);
                }
                const result = await response.json();

                if (result.status === "success" && result.data.length > 0) {
                    const data = result.data;
                    const labels = data.map(item => new Date(item.timestamp).toLocaleTimeString());
                    const tempValues = data.map(item => item.temperature);
                    const phValues = data.map(item => item.ph_value);
                    const turbidityValues = data.map(item => item.turbidity);

                    // Update charts with new data
                    updateChart(tempChart, labels, tempValues);
                    updateChart(phChart, labels, phValues);
                    updateChart(turbidityChart, labels, turbidityValues);
                    alertContainer.className = "alert d-none"; // Hide alert on success
                } else {
                    // Show a more user-friendly error message if the API returns an error or no data
                    showAlert(`API Error หรือไม่มีข้อมูล: ${result.message || 'ไม่มีข้อมูลในฐานข้อมูล'}`);
                    console.error("API Error or no data:", result.message);
                }
            } catch (error) {
                // Show a user-friendly error message on network failure
                showAlert(`ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้: ${error.message}`, 'danger');
                console.error("Fetch Historical Data Error:", error);
            }
        }

        // Function to create or update a Chart.js instance
        function updateChart(chart, labels, data) {
            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = data;
                chart.update();
            }
        }

        // Initialize charts when the DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const tempCtx = document.getElementById('tempChart').getContext('2d');
            tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'อุณหภูมิ (°C)',
                        data: [],
                        borderColor: '#FF7F50',
                        backgroundColor: 'rgba(255, 127, 80, 0.2)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'เวลา'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'ค่าอุณหภูมิ'
                            }
                        }
                    }
                }
            });

            const phCtx = document.getElementById('phChart').getContext('2d');
            phChart = new Chart(phCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'ค่า pH',
                        data: [],
                        borderColor: '#6A5ACD',
                        backgroundColor: 'rgba(106, 90, 205, 0.2)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'เวลา'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'ค่า pH'
                            },
                            min: 0,
                            max: 14
                        }
                    }
                }
            });

            const turbidityCtx = document.getElementById('turbidityChart').getContext('2d');
            turbidityChart = new Chart(turbidityCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'ความขุ่น (%)',
                        data: [],
                        backgroundColor: '#4682B4',
                        borderColor: '#4682B4',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'เวลา'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'ความขุ่น (%)'
                            },
                            min: 0,
                            max: 100
                        }
                    }
                }
            });

            // Initial fetch and update
            fetchLatestSensorData();
            fetchHistoricalData();

            // Set up intervals for real-time updates
            setInterval(fetchLatestSensorData, 5000); // Update latest data every 5 seconds
            setInterval(fetchHistoricalData, 60000); // Update charts every 60 seconds
        });
    </script>
</body>
</html>
