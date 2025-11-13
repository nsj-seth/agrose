<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/OrderItem.php";

try {
    // ✅ Check if we're fetching for a specific customer or all
    if (isset($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        $query = "SELECT o.order_id, o.customer_id, o.order_date, o.status, o.total_amount, o.order_code
                  FROM orders o
                  WHERE o.customer_id = :customer_id
                  ORDER BY o.order_id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":customer_id", $customer_id);
    } else {
        // ✅ Admin mode — get ALL orders
        $query = "SELECT o.order_id, o.customer_id, o.order_date, o.status, o.total_amount, o.order_code
                  FROM orders o
                  ORDER BY o.order_id DESC";
        $stmt = $pdo->prepare($query);
    }

    $stmt->execute();
    $orders = [];
    $orderItem = new OrderItem($pdo);

    while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $orderItem->order_id = $order['order_id'];
        $items = $orderItem->readByOrder();
        $order['items'] = $items;
        $orders[] = $order;
    }

    echo json_encode([
        "status" => "success",
        "data" => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
