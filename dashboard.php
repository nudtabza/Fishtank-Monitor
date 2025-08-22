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
    <style>
        :root {
            --dark-bg: #121212;
            --dark-card: #1e1e1e;
            --dark-text: #e0e0e0;
            --dark-secondary-text: #b0b0b0;
            --blue-accent: #007bff;
            --blue-accent-hover: #0056b3;
            --card-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        body {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }
        .navbar {
            background-color: var(--dark-card);
        }
        .sidebar {
            background-color: var(--dark-card);
            color: var(--dark-text);
        }
        .card {
            background-color: var(--dark-card);
            color: var(--dark-text);
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }
        .card-header {
            border-bottom: 1px solid #333;
        }
        .form-control, .btn {
            background-color: #333;
            color: var(--dark-text);
            border-color: #444;
        }
        .btn-primary {
            background-color: var(--blue-accent);
            border-color: var(--blue-accent);
        }
        .btn-primary:hover {
            background-color: var(--blue-accent-hover);
            border-color: var(--blue-accent-hover);
        }
        .nav-link.active {
            background-color: var(--blue-accent) !important;
            color: white !important;
        }
        .nav-link:hover {
            background-color: #333;
        }
        .sidebar a {
            color: var(--dark-secondary-text);
        }
        .sidebar a:hover {
            color: var(--dark-text);
            background-color: #333;
        }
        .card-title, .card-subtitle {
            color: var(--dark-secondary-text);
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="dashboard.php">ระบบตรวจสอบคุณภาพน้ำ</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link active" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>
                        <a class="nav-link" href="settings.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                            Settings
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Live Data & Historical Charts</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h3" id="temperature-display">-- °C</div>
                                            <div class="text-white-50">อุณหภูมิ</div>
                                        </div>
                                        <i class="fas fa-thermometer-half fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h3" id="ph-display">-- pH</div>
                                            <div class="text-white-50">ค่า pH</div>
                                        </div>
                                        <i class="fas fa-flask fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h3" id="turbidity-display">-- NTU</div>
                                            <div class="text-white-50">ความขุ่น</div>
                                        </div>
                                        <i class="fas fa-water fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    อุณหภูมิ (°C)
                                </div>
                                <div class="card-body"><canvas id="temperatureChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    ค่า pH
                                </div>
                                <div class="card-body"><canvas id="phChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-line me-1"></i>
                                    ความขุ่น (NTU)
                                </div>
                                <div class="card-body"><canvas id="turbidityChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2024</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const temperatureDisplay = document.getElementById('temperature-display');
        const phDisplay = document.getElementById('ph-display');
        const turbidityDisplay = document.getElementById('turbidity-display');

        // Initial charts setup (unchanged)
        const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
        const temperatureChart = new Chart(temperatureCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'อุณหภูมิ',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        const phCtx = document.getElementById('phChart').getContext('2d');
        const phChart = new Chart(phCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'ค่า pH',
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        suggestedMin: 6.0,
                        suggestedMax: 8.0
                    }
                }
            }
        });

        const turbidityCtx = document.getElementById('turbidityChart').getContext('2d');
        const turbidityChart = new Chart(turbidityCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'ความขุ่น',
                    data: [],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
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
                    temperatureDisplay.textContent = `${parseFloat(sensorData.temperature).toFixed(2)} °C`;
                    phDisplay.textContent = `${parseFloat(sensorData.ph_value).toFixed(2)} pH`;
                    turbidityDisplay.textContent = `${parseFloat(sensorData.turbidity).toFixed(2)} NTU`;
                } else {
                    console.log("No new data received. Keeping old display values.");
                }
            } catch (error) {
                console.error('Error fetching latest data:', error);
                // On error, do nothing and keep the last known values displayed
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
