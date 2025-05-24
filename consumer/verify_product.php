<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'consumer') {
    header("Location: ../login.php");
    exit;
}

include('../db/db_connection.php');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No product ID provided']);
    exit;
}

$productId = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($product = $result->fetch_assoc()) {
    echo json_encode([
        'status' => 'success',
        'product' => [
            'name' => $product['name'],
            'batch_number' => $product['batch_number'],
            'harvest_date' => $product['harvest_date'],
            'location' => $product['location'],
            'assigned_seller_id' => $product['assigned_seller_id'],
            'is_sold' => boolval($product['is_sold']),
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
}
?>
