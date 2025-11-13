<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = include "../../config/db.php";
include_once "../../models/Admin.php";

$admin = new Admin($pdo);
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->admin_id) && !empty($data->name) && !empty($data->email) && !empty($data->role)) {
    $admin->admin_id = $data->admin_id;
    $admin->name = $data->name;
    $admin->email = $data->email;
    $admin->role = $data->role;

    if ($admin->update()) {
        echo json_encode(["message" => "Admin updated successfully."]);
    } else {
        echo json_encode(["message" => "Failed to update admin."]);
    }
} else {
    echo json_encode(["message" => "Incomplete data."]);
}
?>
