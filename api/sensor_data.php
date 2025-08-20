<?php
header('Content-Type: application/json; charset=utf-8');

// Database connection details
// You must fill these in with your actual database credentials
$servername = "sql306.infinityfree.com";
$port = 3306;
$username = "if0_39512375";
$password = "NudNud123";
$dbname = "if0_39512375_fishtank_monitor";

// Check if all required GET parameters are present
if (!isset($_GET['temperature']) || !isset($_GET['ph_value']) || !isset($_GET['turbidity'])) {
    echo json_encode(["status" => "error", "message" => "Missing required GET parameters"]);
    exit();
}

// Sanitize and validate input
// The variable names from ESP32 code are 'temperature', 'ph_value' and 'turbidity'.
$temperature = filter_var($_GET['temperature'], FILTER_VALIDATE_FLOAT);
$ph_value = filter_var($_GET['ph_value'], FILTER_VALIDATE_FLOAT);
$turbidity = filter_var($_GET['turbidity'], FILTER_VALIDATE_FLOAT);

if ($temperature === false || $ph_value === false || $turbidity === false) {
    echo json_encode(["status" => "error", "message" => "Invalid data format"]);
    exit();
}

try {
    // Create a PDO connection with the port included
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO sensor_data (temperature, ph_value, turbidity) VALUES (:temperature, :ph_value, :turbidity)");
    $stmt->bindParam(':temperature', $temperature);
    $stmt->bindParam(':ph_value', $ph_value);
    $stmt->bindParam(':turbidity', $turbidity);
    $stmt->execute();
    
    // Return a JSON success response
    echo json_encode(["status" => "success", "message" => "Data inserted successfully"]);

} catch (PDOException $e) {
    // Handle database errors
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

// Close the connection
$conn = null;
?>
