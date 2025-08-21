<?php
// db_conn.php

// ข้อมูลการเชื่อมต่อ Supabase PostgreSQL
// ⚠️ เปลี่ยนค่าเหล่านี้เป็นข้อมูลของโปรเจกต์คุณเอง
$db_host = 'dpg-d2gb4fvdiees73dashs0-a'; // เช่น db.abcdefghijklm.supabase.co
$db_port = '5432'; // Supabase ใช้ port 5432
$db_name = 'fishtank_monitor'; // ชื่อฐานข้อมูล
$db_user = 'nudtabza'; // ชื่อผู้ใช้งาน (มักจะเป็น postgres)
$db_pass = '2gR0SGTsc1hORz1KZNCRulU7J93IVDSZ'; // รหัสผ่านฐานข้อมูล

try {
    // ใช้ PDO สำหรับ PostgreSQL (pgsql)
    $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500); // ตั้งค่า HTTP status code เป็น 500 (Internal Server Error)
    echo json_encode(["message" => "Database connection failed"]);
    error_log("Database connection failed: " . $e->getMessage());
    exit();
}
?>


