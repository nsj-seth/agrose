<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
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

if ($product->delete()) {
    echo json_encode(["status"=>"success","message"=>"Product deleted successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to delete product"]);
}
?>
