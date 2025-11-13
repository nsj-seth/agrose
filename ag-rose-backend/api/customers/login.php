<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Customer.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['password'])) {
    echo json_encode(["status"=>"error","message"=>"Email and password are required"]);
    exit;
}

$customer = new Customer($pdo);
$customer->email = $data['email'];
$customer->password = $data['password'];

if ($customer->login()) {
    echo json_encode([
        "status"=>"success",
        "message"=>"Login successful",
        "customer"=>[
            "customer_id"=>$customer->customer_id,
            "name"=>$customer->name,
            "email"=>$customer->email,
            "phone"=>$customer->phone,
            "address"=>$customer->address
        ]
    ]);
} else {
    echo json_encode(["status"=>"error","message"=>"Invalid email or password"]);
}
?>
