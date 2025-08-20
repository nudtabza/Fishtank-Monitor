<?php
header('Content-Type: application/json; charset=utf-8');

// Database connection details
// You must fill these in with your actual database credentials
$servername = "sql306.infinityfree.com";
$port = 3306;
$username = "if0_39512375";
$password = "NudNud123";
$dbname = "if0_39512375_fishtank_monitor";

try {
    // Create a PDO connection with the port included
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        // No data found in the database
        echo json_encode(["status" => "error", "message" => "No data found"]);
    }

} catch (PDOException $e) {
    // Handle database connection errors and query errors
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

// Close the connection
$conn = null;
?>
