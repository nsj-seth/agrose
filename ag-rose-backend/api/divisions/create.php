<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Division.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'], $data['description'])) {
    echo json_encode(["status"=>"error","message"=>"Missing required fields"]);
    exit;
}

$division = new Division($pdo);
$division->name = $data['name'];
$division->description = $data['description'];

if ($division->create()) {
    echo json_encode(["status"=>"success","message"=>"Division added successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to add division"]);
}
?>
