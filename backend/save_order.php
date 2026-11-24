<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header("Content-Type: application/json; charset=UTF-8");

include __DIR__ . '/db_connection.php'; // Sesuaikan dengan nama file koneksi Anda

$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Data JSON kosong"]);
    exit;
}

$orderId         = $data['id'] ?? '';
$customerName    = $data['customerName'] ?? '';
$customerContact = $data['customerContact'] ?? '-';
$total           = $data['total'] ?? 0;
$notes           = $data['notes'] ?? '';
$status          = $data['status'] ?? 'Menunggu';
$items           = $data['items'] ?? [];

if (!isset($conn) || !$conn) {
    echo json_encode(["success" => false, "message" => "Koneksi database tidak terhubung"]);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Simpan ke tabel orders
    $stmtOrder = $conn->prepare("INSERT INTO orders (id, customer_name, customer_contact, total_amount, notes, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmtOrder->bind_param("sssdss", $orderId, $customerName, $customerContact, $total, $notes, $status);
    $stmtOrder->execute();
    $stmtOrder->close();

    // 2. Simpan setiap item ke tabel order_items (Gunakan NULL jika ID menu tidak ditemukan di database)
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $menuId   = !empty($item['id']) ? $item['id'] : NULL;
        $itemName = $item['name'] ?? 'Menu Donat';
        $price    = $item['price'] ?? 0;
        $quantity = $item['quantity'] ?? 1;
        $subtotal = $price * $quantity;

        $stmtItem->bind_param("sisdid", $orderId, $menuId, $itemName, $price, $quantity, $subtotal);
        $stmtItem->execute();
    }
    $stmtItem->close();

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Pesanan berhasil disimpan ke database!"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "SQL Error: " . $e->getMessage()]);
}

$conn->close();
?>