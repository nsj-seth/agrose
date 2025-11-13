<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = include "../../config/db.php";
include_once "../../models/Admin.php";

$admin = new Admin($pdo);
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->admin_id)) {
    $admin->admin_id = $data->admin_id;

    if ($admin->delete()) {
        echo json_encode(["message" => "Admin deleted successfully."]);
    } else {
        echo json_encode(["message" => "Failed to delete admin."]);
    }
} else {
    echo json_encode(["message" => "Admin ID required."]);
}
?>
