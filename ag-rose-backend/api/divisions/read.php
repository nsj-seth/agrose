<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once "../../config/db.php";
include_once "../../models/Division.php";

$division = new Division($pdo);
$stmt = $division->read();
$divisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($divisions);
?>
