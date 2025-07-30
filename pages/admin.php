<?php
session_start();
include("../db/config.php");

if ($_SESSION['user']['role'] != 'admin') die("Access Denied");

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <?php include("../includes/header.php"); ?>
    <h2 style="color: #333;">Admin Panel</h2>
    <a href='add_product.php' style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">➕ Add New Product</a>
    <br><br>
    
    <?php
    $stmt = $pdo->query("SELECT * FROM products");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div style='background-color: white; border: 1px solid #ccc; border-radius: 8px; padding: 15px; margin-bottom: 10px; width: fit-content;'>";
        echo "<strong style='color: #333;'>{$row['name']}</strong> - ₹{$row['price']} - Stock: {$row['stock']}";
        echo " <a href='edit_product.php?id={$row['id']}' style='margin-left: 10px; text-decoration: none;'>✏️</a> <a href='?delete={$row['id']}' style='margin-left: 5px; text-decoration: none;'>❌</a>";
        echo "</div>";
    }
    ?>
</body>
</html>
