
<?php

// testing the db connection
header("Content-Type: application/json");
include_once "config/db.php";

if ($conn) {
    echo json_encode(["success" => true, "message" => "Database connection successful!"]);
}
?>
