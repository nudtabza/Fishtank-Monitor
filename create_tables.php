<?php

require_once 'db_conn.php';

try {
    $sql_users = "
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
        );
    ";

    $sql_sensor_data = "
        CREATE TABLE IF NOT EXISTS sensor_data (
            id SERIAL PRIMARY KEY,
            temperature REAL,
            ph_value REAL,
            turbidity REAL,
            timestamp TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
        );
    ";

    $sql_thresholds = "
        CREATE TABLE IF NOT EXISTS user_thresholds (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL UNIQUE,
            temp_min REAL,
            temp_max REAL,
            ph_min REAL,
            ph_max REAL,
            turbidity_max REAL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ";

    $conn->exec($sql_users);
    echo "ตาราง 'users' ถูกสร้างสำเร็จแล้วหรือมีอยู่แล้ว<br>";
    
    $conn->exec($sql_sensor_data);
    echo "ตาราง 'sensor_data' ถูกสร้างสำเร็จแล้วหรือมีอยู่แล้ว<br>";

    $conn->exec($sql_thresholds);
    echo "ตาราง 'user_thresholds' ถูกสร้างสำเร็จแล้วหรือมีอยู่แล้ว<br>";

} catch (PDOException $e) {
    die("Error creating tables: " . $e->getMessage());
}

$conn = null;
?>