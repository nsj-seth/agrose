<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once "../../config/db.php";
require_once "../../models/OrderItem.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['customer_id']) || !isset($data['items']) || !is_array($data['items'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    exit;
}

$customer_id = $data['customer_id'];
$items = $data['items'];

// Calculate total amount
$total_amount = 0;
foreach ($items as $item) {
    if (!isset($item['price']) || !isset($item['quantity'])) {
        echo json_encode(["status" => "error", "message" => "Each item must include 'price' and 'quantity'"]);
        exit;
    }
    $total_amount += $item['price'] * $item['quantity'];
}

// Generate a unique order code (like Jumia)
function generateOrderCode($customer_id) {
    $prefix = 'AGR';
    $timestamp = strtoupper(dechex(time())); // Convert current timestamp to hex
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4)); // Random 4 chars
    return "{$prefix}-{$customer_id}-{$timestamp}-{$random}";
}

try {
    $pdo->beginTransaction();

    // Generate order code
    $order_code = generateOrderCode($customer_id);

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, order_code) VALUES (:customer_id, :total_amount, 'pending', :order_code)");
    $stmt->execute([
        ':customer_id' => $customer_id,
        ':total_amount' => $total_amount,
        ':order_code' => $order_code
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert order items using OrderItem model
    foreach ($items as $item) {
        $orderItem = new OrderItem($pdo);
        $orderItem->order_id = $order_id;
        $orderItem->product_id = $item['product_id'];
        $orderItem->quantity = $item['quantity'];
        $orderItem->price = $item['price'];
        $orderItem->create();
    }

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Order created successfully",
        "order_id" => $order_id,
        "order_code" => $order_code,
        "total_amount" => $total_amount
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
