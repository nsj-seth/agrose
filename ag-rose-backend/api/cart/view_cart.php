<?php
session_start();
header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

include_once "../config/db.php";    // Your database connection
include_once "../models/Cart.php";  // Cart model

$customer_id = $_SESSION['customer_id'];

// Initialize Cart
$cart = new Cart($conn);
$cart->customer_id = $customer_id;

// Get all items
$result = $cart->getItems();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;

    $items[] = [
        'cart_id' => $row['cart_id'],
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => $row['quantity'],
        'subtotal' => $subtotal,
        'image_url' => $row['image_url']
    ];
}

echo json_encode([
    'status' => 'success',
    'items' => $items,
    'total' => $total
]);
