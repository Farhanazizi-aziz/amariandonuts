<?php
header("Content-Type: application/json; charset=UTF-8");
include 'db_connection.php';

$query = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($query);

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orderId = $row['id'];
    
    // Ambil rincian item untuk setiap order
    $itemQuery = "SELECT * FROM order_items WHERE order_id = '$orderId'";
    $itemResult = $conn->query($itemQuery);
    $items = [];
    
    while ($itemRow = $itemResult->fetch_assoc()) {
        $items[] = [
            "id" => $itemRow['menu_item_id'],
            "name" => $itemRow['item_name'],
            "price" => floatval($itemRow['price']),
            "quantity" => intval($itemRow['quantity'])
        ];
    }

    $orders[] = [
        "id" => $row['id'],
        "timestamp" => date('d M Y, H:i', strtotime($row['created_at'])) . " WIB",
        "customerName" => $row['customer_name'],
        "customerContact" => $row['customer_contact'],
        "total" => floatval($row['total_amount']),
        "notes" => $row['notes'],
        "status" => $row['status'],
        "items" => $items
    ];
}

echo json_encode(["success" => true, "orders" => $orders]);
$conn->close();
?>