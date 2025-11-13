<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once "../../config/db.php";
include_once "../../models/Customer.php";

$customer = new Customer($pdo);
$stmt = $customer->read();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status"=>"success",
    "data"=>$customers
]);
?>
