<?php
header('Content-Type: application/json; charset=utf-8');

// เรียกใช้งานไฟล์เชื่อมต่อฐานข้อมูล
require_once '../db_conn.php';

// เพิ่มการตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn === null) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Check if all required GET parameters are present
if (!isset($_GET['temperature']) || !isset($_GET['ph_value']) || !isset($_GET['turbidity'])) {
    echo json_encode(["status" => "error", "message" => "Missing required GET parameters"]);
    exit();
}

// Sanitize and validate input
$temperature = filter_var($_GET['temperature'], FILTER_VALIDATE_FLOAT);
$ph_value = filter_var($_GET['ph_value'], FILTER_VALIDATE_FLOAT);
$turbidity = filter_var($_GET['turbidity'], FILTER_VALIDATE_FLOAT);

if ($temperature === false || $ph_value === false || $turbidity === false) {
    echo json_encode(["status" => "error", "message" => "Invalid data format"]);
    exit();
}

// Prepare and execute the SQL statement to insert data
$stmt = $conn->prepare("INSERT INTO sensor_data (temperature, ph_value, turbidity) VALUES (:temperature, :ph_value, :turbidity)");
$stmt->bindParam(':temperature', $temperature);
$stmt->bindParam(':ph_value', $ph_value);
$stmt->bindParam(':turbidity', $turbidity);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save data"]);
}
?>
