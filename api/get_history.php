<?php
header('Content-Type: application/json; charset=utf-8');

// Include the centralized database connection file
require_once '../db_conn.php';

try {
    // SQL query to select the latest 50 records
    $sql = "SELECT id, temperature, ph_value, turbidity, timestamp FROM sensor_data ORDER BY timestamp DESC LIMIT 50";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
        
    // Fetch all the results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        // Reverse the array so the oldest data is first, which is suitable for charts
        $reversedResults = array_reverse($results);
            
        // Return a JSON success response with the data
        echo json_encode(["status" => "success", "data" => $reversedResults]);
    } else {
        // No data found in the database, return success with empty data
        echo json_encode(["status" => "success", "data" => []]);
    }
} catch (PDOException $e) {
    // Handle database connection errors gracefully
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
