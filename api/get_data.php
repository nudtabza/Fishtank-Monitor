<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

require_once '../db_conn.php';

$response = ["status" => "error", "message" => "An unknown error occurred."];

try {
    $sql = "SELECT temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $response = ["status" => "success", "data" => $data];
    } else {
        $response = ["status" => "success", "data" => (object)[]];
    }
} catch (PDOException $e) {
    http_response_code(500);
    $response = ["status" => "error", "message" => "Database error: " . $e->getMessage()];
}

echo json_encode($response);
$conn = null;
?>