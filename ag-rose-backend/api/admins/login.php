<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = include "../../config/db.php";
include_once "../../models/Admin.php";

$admin = new Admin($pdo);
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $admin->email = $data->email;
    $admin->password = $data->password;

    $result = $admin->login();

    if ($result) {
        $_SESSION['admin_id'] = $result['admin_id'];
        $_SESSION['admin_name'] = $result['name'];
        $_SESSION['admin_role'] = $result['role'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful.",
            "admin" => [
                "id" => $result['admin_id'],
                "name" => $result['name'],
                "email" => $result['email'],
                "role" => $result['role']
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Email and password required."]);
}
?>
