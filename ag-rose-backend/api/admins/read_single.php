<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$pdo = include "../../config/db.php";
include_once "../../models/Admin.php";

$admin = new Admin($pdo);

// get admin_id from query string (?id=1)
$admin->admin_id = isset($_GET['id']) ? $_GET['id'] : die(json_encode(["message" => "No ID provided."]));

if ($admin->read_single()) {
    echo json_encode([
        "admin_id" => $admin->admin_id,
        "name" => $admin->name,
        "email" => $admin->email,
        "role" => $admin->role,
        "created_at" => $admin->created_at
    ]);
} else {
    echo json_encode(["message" => "Admin not found."]);
}
?>
