<?php
$host = getenv('db.blyckkguxpqctpcfebco.supabase.co');
$port = getenv('5432');
$db   = getenv('postgres');
$user = getenv('postgres');
$pass = getenv('oo83EYxDvIzAsZvq');

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => $e->getMessage()
    ]);
    exit;
}
?>

