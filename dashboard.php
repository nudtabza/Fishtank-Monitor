<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
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
    <style>
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            -webkit-animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-light-blue">
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="bg-white border-end" id="sidebar-wrapper">
            <div class="sidebar-heading p-4">
                <i class="fas fa-water me-2"></i> Dashboard
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> ข้อมูลล่าสุด</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="settings.php"><i class="fas fa-cog me-2"></i> ตั้งค่า</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ</a>
            </div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item">
                                <span class="nav-link text-dark">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Page content-->
            <div class="container-fluid p-4">
                <h1 class="mt-4">ข้อมูลล่าสุด</h1>
                <div class="row g-4 mt-2">
                    <!-- Temperature Card -->
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="fas fa-thermometer-half me-2"></i>อุณหภูมิ</h5>
                                <p class="card-text fs-2" id="tempValue">--</p>
                                <span class="badge bg-secondary" id="tempStatus">กำลังโหลด...</span>
                            </div>
                        </div>
                    </div>
                    <!-- pH Card -->
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-success"><i class="fas fa-flask me-2"></i>ค่า pH</h5>
                                <p class="card-text fs-2" id="phValue">--</p>
                                <span class="badge bg-secondary" id="phStatus">กำลังโหลด...</span>
                            </div>
                        </div>
                    </div>
                    <!-- Turbidity Card -->
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-info"><i class="fas fa-tint me-2"></i>ความขุ่น</h5>
                                <p class="card-text fs-2" id="turbidityValue">--</p>
                                <span class="badge bg-secondary" id="turbidityStatus">กำลังโหลด...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="mt-5">ข้อมูลย้อนหลัง (50 ค่าล่าสุด)</h2>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <canvas id="temperatureChart"></canvas>
                    </div>
                    <div class="col-md-12 mt-4">
                        <canvas id="phChart"></canvas>
                    </div>
                    <div class="col-md-12 mt-4">
                        <canvas id="turbidityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });
        
        // Function to update status based on thresholds
        function updateStatus(value, min, max, elementId) {
            const statusElement = document.getElementById(elementId);
            if (value >= min && value <= max) {
                statusElement.textContent = 'ปกติ';
                statusElement.classList.remove('bg-danger', 'bg-warning', 'bg-secondary');
                statusElement.classList.add('bg-success');
            } else if (value < min) {
                statusElement.textContent = 'ต่ำเกินไป';
                statusElement.classList.remove('bg-success', 'bg-warning', 'bg-secondary');
                statusElement.classList.add('bg-danger');
            } else if (value > max) {
                statusElement.textContent = 'สูงเกินไป';
                statusElement.classList.remove('bg-success', 'bg-warning', 'bg-secondary');
                statusElement.classList.add('bg-danger');
            }
        }

        // Initialize charts
        const ctxTemp = document.getElementById('temperatureChart').getContext('2d');
        const temperatureChart = new Chart(ctxTemp, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'อุณหภูมิ (°C)',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });

        const ctxPh = document.getElementById('phChart').getContext('2d');
        const phChart = new Chart(ctxPh, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'ค่า pH',
                    data: [],
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }]
            }
        });

        const ctxTurbidity = document.getElementById('turbidityChart').getContext('2d');
        const turbidityChart = new Chart(ctxTurbidity, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'ความขุ่น (NTU)',
                    data: [],
                    borderColor: 'rgb(255, 206, 86)',
                    tension: 0.1
                }]
            }
        });
        
        // Fetch and display latest sensor data
        async function fetchLatestSensorData() {
            try {
                // แก้ไข URL ให้ถูกต้อง
                const response = await fetch('api/get_data.php');
                const data = await response.json();
                
                if (data.status === 'success' && data.data) {
                    const sensorData = data.data;
                    document.getElementById('tempValue').textContent = `${sensorData.temperature} °C`;
                    document.getElementById('phValue').textContent = sensorData.ph_value;
                    document.getElementById('turbidityValue').textContent = `${sensorData.turbidity} NTU`;

                    // Fetch user thresholds to update status
                    const settingsResponse = await fetch('api/get_settings.php');
                    const settingsData = await settingsResponse.json();
                    
                    if (settingsData.status === 'success' && settingsData.data) {
                        const thresholds = settingsData.data;
                        updateStatus(parseFloat(sensorData.temperature), parseFloat(thresholds.temp_min), parseFloat(thresholds.temp_max), 'tempStatus');
                        updateStatus(parseFloat(sensorData.ph_value), parseFloat(thresholds.ph_min), parseFloat(thresholds.ph_max), 'phStatus');
                        updateStatus(parseFloat(sensorData.turbidity), null, parseFloat(thresholds.turbidity_max), 'turbidityStatus');
                    } else {
                        // If thresholds are not set, default to "Unknown" status
                        document.getElementById('tempStatus').textContent = 'ไม่ทราบสถานะ';
                        document.getElementById('phStatus').textContent = 'ไม่ทราบสถานะ';
                        document.getElementById('turbidityStatus').textContent = 'ไม่ทราบสถานะ';
                        document.getElementById('tempStatus').className = 'badge bg-secondary';
                        document.getElementById('phStatus').className = 'badge bg-secondary';
                        document.getElementById('turbidityStatus').className = 'badge bg-secondary';
                    }
                }
            } catch (error) {
                console.error('Error fetching latest data:', error);
                document.getElementById('tempValue').textContent = 'Error';
                document.getElementById('phValue').textContent = 'Error';
                document.getElementById('turbidityValue').textContent = 'Error';
            }
        }
        
        // Fetch and update historical data
        async function fetchHistoricalData() {
            try {
                // แก้ไข URL ให้ถูกต้อง
                const response = await fetch('api/get_history.php');
                const data = await response.json();

                if (data.status === 'success' && data.data) {
                    const historicalData = data.data;
                    const labels = historicalData.map(item => new Date(item.timestamp).toLocaleTimeString());
                    const temperatures = historicalData.map(item => item.temperature);
                    const phValues = historicalData.map(item => item.ph_value);
                    const turbidities = historicalData.map(item => item.turbidity);

                    temperatureChart.data.labels = labels;
                    temperatureChart.data.datasets[0].data = temperatures;
                    temperatureChart.update();

                    phChart.data.labels = labels;
                    phChart.data.datasets[0].data = phValues;
                    phChart.update();

                    turbidityChart.data.labels = labels;
                    turbidityChart.data.datasets[0].data = turbidities;
                    turbidityChart.update();
                }
            } catch (error) {
                console.error('Error fetching historical data:', error);
            }
        }
    
        fetchLatestSensorData();
        fetchHistoricalData();
    
        setInterval(fetchLatestSensorData, 5000);
        setInterval(fetchHistoricalData, 60000);
    });
    </script>
</body>
</html>
