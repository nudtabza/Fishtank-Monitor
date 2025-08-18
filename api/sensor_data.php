<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

require_once '../db_conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

if (!isset($_POST['temperature']) || !isset($_POST['ph_value']) || !isset($_POST['turbidity'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required POST parameters"]);
    exit();
}

$temperature = filter_var($_POST['temperature'], FILTER_VALIDATE_FLOAT);
$ph_value = filter_var($_POST['ph_value'], FILTER_VALIDATE_FLOAT);
$turbidity = filter_var($_POST['turbidity'], FILTER_VALIDATE_FLOAT);

if ($temperature === false || $ph_value === false || $turbidity === false) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data format"]);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO sensor_data (temperature, ph_value, turbidity) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$temperature, $ph_value, $turbidity])) {
        echo json_encode(["status" => "success", "message" => "Data inserted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to insert data"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
$conn = null;
?>