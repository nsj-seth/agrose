<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once "../../config/db.php";
include_once "../../models/Product.php";

// ✅ Use the existing $pdo variable directly
$product = new Product($pdo);

// Fetch products
$stmt = $product->read();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response
echo json_encode($products);
?>
