<?php
// api/get_data.php

session_start();
header('Content-Type: application/json; charset=utf-8');

// Calling the database connection file
require_once '../db_conn.php';

try {
    // SQL query to fetch the latest sensor data
    $sql = "SELECT temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Return data as JSON
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        // If no data is found, return success with an empty object
        echo json_encode(["status" => "success", "data" => (object)[]]);
    }
} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn = null; // Close the connection
?>
