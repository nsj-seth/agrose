<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

include_once "../config/db.php";
include_once "../models/Cart.php";

$customer_id = $_SESSION['customer_id'];
$data = json_decode(file_get_contents("php://input"), true);
$cart_id = $data['cart_id'] ?? null;

if (!$cart_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid cart item']);
    exit;
}

$cart = new Cart($conn);
$cart->customer_id = $customer_id;
$cart->cart_id = $cart_id;

if ($cart->removeItem()) {
    echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
}
