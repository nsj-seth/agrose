<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once "../../config/db.php";
include_once "../../models/Customer.php";

if (!isset($_GET['customer_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing customer ID"
    ]);
    exit;
}

$customer_id = intval($_GET['customer_id']);

$customer = new Customer($pdo);
$customer->customer_id = $customer_id;

$result = $customer->readOne();

if ($result) {
    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Customer not found"
    ]);
}
?>
