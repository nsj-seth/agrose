<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Product.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'], $data['description'], $data['price'], $data['stock_quantity'], $data['division_id'])) {
    echo json_encode(["status"=>"error","message"=>"Missing required fields"]);
    exit;
}

$product = new Product($pdo);
$product->name = $data['name'];
$product->description = $data['description'];
$product->price = $data['price'];
$product->stock_quantity = $data['stock_quantity'];
$product->division_id = $data['division_id'];

if ($product->create()) {
    echo json_encode(["status"=>"success","message"=>"Product added successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to add product"]);
}
?>
