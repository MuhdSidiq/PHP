<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "php_demo";

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>