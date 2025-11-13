<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once "../../config/db.php";
include_once "../../models/Admin.php";

$pdo = include "../../config/db.php";  // ✅ load your PDO connection
$admin = new Admin($pdo);

$result = $admin->read();
$admins = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $admins[] = $row;
}

echo json_encode($admins);
?>
