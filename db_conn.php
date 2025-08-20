<?php
// กำหนดข้อมูลการเชื่อมต่อฐานข้อมูล
// Get the database connection URL from the environment variable provided by Render.com
$db_url = getenv('DATABASE_URL');
if (!$db_url) {
    die("Error: DATABASE_URL environment variable is not set.");
}

// Parse the database URL to extract connection components (host, port, user, password, dbname).
$url_parts = parse_url($db_url);
$host = $url_parts['host'];
$port = $url_parts['port'];
$dbname = ltrim($url_parts['path'], '/');
$user = $url_parts['user'];
$password = $url_parts['pass'];
$sslmode = "require";

try {
    // Create the DSN (Data Source Name) string for a PDO connection to PostgreSQL.
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password;sslmode=$sslmode";
    
    // Establish the PDO connection. This is the standard and safest method.
    $conn = new PDO($dsn);
    
    // Set the PDO error mode to throw exceptions on errors, making debugging easier.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Catch any connection errors and display a user-friendly message.
    die("Error: Could not connect. " . $e->getMessage());
}
?>
