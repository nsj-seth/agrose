<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Cart.php";
include_once "../../models/Order.php";
include_once "../../models/OrderItem.php";
include_once "../../models/Payment.php";

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

$cart = new Cart($conn);
$order = new Order($conn);
$orderItem = new OrderItem($conn);
$payment = new Payment($conn);

// 1️⃣ Get all cart items for the user
$cartItems = $cart->getUserCart($user_id);

if (empty($cartItems)) {
    echo json_encode(["message" => "Your cart is empty"]);
    exit;
}

// 2️⃣ Create a new order
$order_id = $order->createOrder($user_id, "Pending");
$totalAmount = 0;

// 3️⃣ Insert cart items into order_items table
foreach ($cartItems as $item) {
    $orderItem->addItem($order_id, $item['product_id'], $item['quantity']);
    $totalAmount += $item['price'] * $item['quantity'];
}

// 4️⃣ Record payment (simulating success for now)
$payment->createPayment($order_id, $totalAmount, "Momo", "Success");

// 5️⃣ Clear the cart
$cart->clearCart($user_id);

// 6️⃣ Return response
echo json_encode([
    "message" => "Checkout successful",
    "order_id" => $order_id,
    "total" => $totalAmount,
    "status" => "Paid"
]);
