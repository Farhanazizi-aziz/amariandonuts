<?php
// Koneksi ke Database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'amarian_donuts';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Koneksi gagal"]));
}

// Ambil ID dari request JSON atau GET/POST
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Menu berhasil dihapus dari database"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menghapus menu"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
}

$conn->close();
?>