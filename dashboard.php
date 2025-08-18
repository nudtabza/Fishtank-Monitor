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
            to { -webkit-transform: rotate(360deg); }
        }
        @-webkit-keyframes spin {
            to { -webkit-transform: rotate(360deg); }
        }
    </style>
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
                        <a class="nav-link active" aria-current="page" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
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
        <h1 class="text-center text-primary-heading mb-4">Dashboard</h1>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-gradient-info text-white shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-thermometer-half me-2"></i> อุณหภูมิ</h5>
                        <div id="temperature-value" class="display-4 fw-bold">...</div>
                        <p class="card-text">สถานะ: <span id="temperature-status" class="fw-bold">...</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-warning text-white shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-flask me-2"></i> ค่า pH</h5>
                        <div id="ph-value" class="display-4 fw-bold">...</div>
                        <p class="card-text">สถานะ: <span id="ph-status" class="fw-bold">...</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-danger text-white shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-water me-2"></i> ความขุ่น</h5>
                        <div id="turbidity-value" class="display-4 fw-bold">...</div>
                        <p class="card-text">สถานะ: <span id="turbidity-status" class="fw-bold">...</span></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title text-primary-heading">กราฟข้อมูลย้อนหลัง 50 รายการ</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-center">อุณหภูมิ (°C)</h6>
                                <canvas id="temperatureChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-center">ค่า pH</h6>
                                <canvas id="phChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-center">ความขุ่น (ค่า ADC)</h6>
                                <canvas id="turbidityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
            const phCtx = document.getElementById('phChart').getContext('2d');
            const turbidityCtx = document.getElementById('turbidityChart').getContext('2d');

            let temperatureChart = new Chart(temperatureCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'อุณหภูมิ (°C)',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            let phChart = new Chart(phCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'ค่า pH',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 0,
                            max: 14
                        }
                    }
                }
            });

            let turbidityChart = new Chart(turbidityCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'ความขุ่น (ค่า ADC)',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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
                                text: 'ค่า ADC'
                            },
                            min: 0,
                            max: 4095
                        }
                    }
                }
            });
        
            async function fetchLatestSensorData() {
                try {
                    const response = await fetch('api/get_data.php');
                    const data = await response.json();
                    
                    if (data.status === 'success' && data.data && Object.keys(data.data).length > 0) {
                        const sensorData = data.data;
                        document.getElementById('temperature-value').textContent = sensorData.temperature + ' °C';
                        document.getElementById('ph-value').textContent = sensorData.ph_value;
                        document.getElementById('turbidity-value').textContent = sensorData.turbidity;
                        
                    } else {
                        console.log('No new data from server.');
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