<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'amarian_donuts';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false, 
        "message" => "Koneksi database gagal: " . $conn->connect_error
    ]));
}

// Set charset UTF-8 agar data teks/emoji aman
$conn->set_charset("utf8mb4");
?>