<?php
include("../db/config.php");
include("../includes/header.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['price'], $_POST['stock'], $_POST['image']]);
    header("Location: admin.php");
}
?>

<form method="POST" style="max-width: 400px; margin: 40px auto; display: flex; flex-direction: column; gap: 15px; font-family: Arial;">
    <input name="name" required placeholder="Product Name" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input name="price" type="number" required placeholder="Price" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input name="stock" type="number" required placeholder="Stock" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input name="image" required placeholder="Image Filename (e.g., toy.jpg)" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <button type="submit" style="padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
        Add
    </button>
</form>
