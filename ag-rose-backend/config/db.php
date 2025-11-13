<?php
$host = "localhost";
$db_name = "agrose_shop";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>