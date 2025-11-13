<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

require_once "../../config/db.php";
require_once "../../models/Payment.php";

try {
    // Connect to database
    $pdo = require "../../config/db.php";
    $payment = new Payment($pdo);

    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['order_id'], $data['amount'], $data['payment_method'], $data['status'])) {
        echo json_encode(["status" => "error", "message" => "Missing fields"]);
        exit;
    }

    $order_id = $data['order_id'];
    $amount = $data['amount'];
    $method = $data['payment_method'];
    $status = $data['status'];

    // Insert payment record
    if ($payment->createPayment($order_id, $amount, $method, $status)) {

        // ✅ Update the related order's status to 'Paid'
        $updateOrder = $pdo->prepare("UPDATE orders SET status = 'Paid' WHERE order_id = ?");
        $updateOrder->execute([$order_id]);

        echo json_encode([
            "status" => "success",
            "message" => "Payment recorded and order marked as Paid"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to record payment"]);
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
