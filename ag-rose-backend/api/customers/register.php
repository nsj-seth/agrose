<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Customer.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'], $data['email'], $data['phone'], $data['password'], $data['address'])) {
    echo json_encode(["status"=>"error","message"=>"Missing required fields"]);
    exit;
}

$customer = new Customer($pdo);
$customer->name = $data['name'];
$customer->email = $data['email'];
$customer->phone = $data['phone'];
$customer->password = $data['password'];
$customer->address = $data['address'];

try {
    if ($customer->register()) {
        echo json_encode(["status"=>"success","message"=>"Customer registered successfully"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Failed to register customer"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status"=>"error","message"=>"Database error: ".$e->getMessage()]);
}
?>
