<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once "../../config/db.php";
include_once "../../models/Division.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['division_id'])) {
    echo json_encode(["status"=>"error","message"=>"Division ID is required"]);
    exit;
}

$division = new Division($pdo);
$division->division_id = $data['division_id'];
$division->name = $data['name'] ?? null;
$division->description = $data['description'] ?? null;

if ($division->update()) {
    echo json_encode(["status"=>"success","message"=>"Division updated successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to update division"]);
}
?>
