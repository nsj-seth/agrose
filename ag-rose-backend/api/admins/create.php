<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = include "../../config/db.php";
include_once "../../models/Admin.php";

$admin = new Admin($pdo);
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->email) && !empty($data->password) && !empty($data->role)) {
    $admin->name = $data->name;
    $admin->email = $data->email;
    $admin->password = $data->password;
    $admin->role = $data->role;

    if ($admin->create()) {
        echo json_encode(["message" => "Admin created successfully."]);
    } else {
        echo json_encode(["message" => "Failed to create admin."]);
    }
} else {
    echo json_encode(["message" => "Incomplete data."]);
}
?>
