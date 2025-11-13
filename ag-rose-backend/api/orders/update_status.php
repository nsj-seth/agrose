<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Order.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id'], $data['status'])) {
    echo json_encode(["status"=>"error","message"=>"Order ID and status are required"]);
    exit;
}

// Optional: restrict allowed statuses
$allowed_statuses = ['pending','completed','cancelled','delivered'];
$status = strtolower(trim($data['status']));
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(["status"=>"error","message"=>"Invalid status value"]);
    exit;
}

$order = new Order($pdo);
$order->order_id = $data['order_id'];
$order->status = $status;

if ($order->updateStatus()) {
    echo json_encode(["status"=>"success","message"=>"Order status updated successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"No order found with the given ID"]);
}
?>
