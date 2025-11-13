<?php
session_start();
header("Content-Type: application/json");

// Check login
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

include_once "../config/db.php";
include_once "../models/Cart.php";

$customer_id = $_SESSION['customer_id'];

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? null;
$quantity = $data['quantity'] ?? 1;

// Validate
if (!$product_id || $quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity']);
    exit;
}

// Add to cart
$cart = new Cart($conn);
$cart->customer_id = $customer_id;
$cart->product_id = $product_id;
$cart->quantity = $quantity;

if ($cart->addItem()) {
    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart']);
}
