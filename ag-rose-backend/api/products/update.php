<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Product.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'])) {
    echo json_encode(["status"=>"error","message"=>"Product ID is required"]);
    exit;
}

$product = new Product($pdo);
$product->product_id = $data['product_id'];
$product->name = $data['name'] ?? null;
$product->description = $data['description'] ?? null;
$product->price = $data['price'] ?? null;
$product->stock_quantity = $data['stock_quantity'] ?? null;
$product->division_id = $data['division_id'] ?? null;

if ($product->update()) {
    echo json_encode(["status"=>"success","message"=>"Product updated successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to update product"]);
}
?>
