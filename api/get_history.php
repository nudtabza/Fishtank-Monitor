<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

require_once '../db_conn.php';

try {
    $sql = "SELECT id, temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY id DESC LIMIT 50";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($results) {
        $reversedResults = array_reverse($results);
        
        echo json_encode(["status" => "success", "data" => $reversedResults]);
    } else {
        echo json_encode(["status" => "success", "data" => []]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
$conn = null;
?>