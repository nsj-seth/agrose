<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
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

if ($division->delete()) {
    echo json_encode(["status"=>"success","message"=>"Division deleted successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to delete division"]);
}
?>
