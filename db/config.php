<?php
// Updated PDO version of the Pet Palace project

// db/config.php
$dsn = "mysql:host=localhost;dbname=pet_palace;charset=utf8";
$user = "root";
$pass = "";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>