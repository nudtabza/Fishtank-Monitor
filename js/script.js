// js/script.js
let tempChart, phChart, turbidityChart; // Global chart instances

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Charts
    // Temperature Chart
    const ctxTemp = document.getElementById('tempChart').getContext('2d');
    tempChart = new Chart(ctxTemp, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'อุณหภูมิ (°C)',
                data: [],
                borderColor: '#fd7e14', // Orange
                backgroundColor: 'rgba(253, 126, 20, 0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    title: { display: true, text: 'อุณหภูมิ (°C)' }
                },
                x: {
                    title: { display: true, text: 'เวลา' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });

    // pH Chart
    const ctxPh = document.getElementById('phChart').getContext('2d');
    phChart = new Chart(ctxPh, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'ค่า pH',
                data: [],
                borderColor: '#6f42c1', // Purple
                backgroundColor: 'rgba(111, 66, 193, 0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    title: { display: true, text: 'ค่า pH' },
                    min: 0, // pH scale typically 0-14
                    max: 14
                },
                x: {
                    title: { display: true, text: 'เวลา' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });

    // Turbidity Chart
    const ctxTurbidity = document.getElementById('turbidityChart').getContext('2d');
    turbidityChart = new Chart(ctxTurbidity, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'ความขุ่น (%)',
                data: [],
                borderColor: '#6c757d', // Grey
                backgroundColor: 'rgba(108, 117, 125, 0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'ความขุ่น (%)' }
                },
                x: {
                    title: { display: true, text: 'เวลา' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });

    // Fetch initial data and start polling
    fetchDataAndRender();
    // Fetch data every 5 seconds (adjust as needed)
    setInterval(fetchDataAndRender, 5000);
});

async function fetchDataAndRender() {
    try {
        const response = await fetch('api/get_data.php');
        const data = await response.json();

        if (data.status === 'success' && data.data) {
            const sensorData = data.data;

            // Update current values display
            document.getElementById('currentTemp').textContent = parseFloat(sensorData.temperature).toFixed(2) + ' °C';
            document.getElementById('currentPh').textContent = parseFloat(sensorData.ph_value).toFixed(2);
            document.getElementById('currentTurbidity').textContent = parseFloat(sensorData.turbidity).toFixed(2) + ' %';

            // Update charts
            const time = new Date(sensorData.timestamp).toLocaleTimeString(); // Format time nicely

            updateChart(tempChart, time, parseFloat(sensorData.temperature));
            updateChart(phChart, time, parseFloat(sensorData.ph_value));
            updateChart(turbidityChart, time, parseFloat(sensorData.turbidity));

        } else if (data.status === 'error' && data.message === 'No data found') {
            console.warn('No sensor data yet. Waiting for ESP32 to send data.');
            // Optional: Show a message on the dashboard that no data is available
        } else {
            console.error('Error fetching data:', data.message);
        }
    } catch (error) {
        console.error('Network or parsing error:', error);
        // Handle network errors, e.g., show an offline message
    }
}

function updateChart(chart, label, value) {
    chart.data.labels.push(label);
    chart.data.datasets[0].data.push(value);

    // Limit data points to keep graph readable (e.g., last 20 points)
    const maxDataPoints = 20;
    if (chart.data.labels.length > maxDataPoints) {
        chart.data.labels.shift();
        chart.data.datasets[0].data.shift();
    }
    chart.update();
}

// Function to simulate notifications (for demonstration)
// In a real app, notifications would come from the backend based on thresholds
/*
function addNotification(message, type = 'info') {
    const list = document.getElementById('notificationList');
    const newItem = document.createElement('li');
    newItem.className = `list-group-item d-flex justify-content-between align-items-center alert-${type}`;
    newItem.innerHTML = `
        <span>${message}</span>
        <span class="badge bg-secondary rounded-pill">ตอนนี้</span>
    `;
    list.prepend(newItem); // Add to top
    // Optionally remove old notifications if too many
    if (list.children.length > 5) {
        list.lastChild.remove();
    }
}

// Example usage (you'd replace this with actual backend integration)
// setTimeout(() => addNotification('แจ้งเตือน: ค่า pH ต่ำกว่าปกติ! (6.2)', 'danger'), 10000);
// setTimeout(() => addNotification('แจ้งเตือน: อุณหภูมิสูงขึ้นผิดปกติ (30.5°C)', 'warning'), 20000);
*/