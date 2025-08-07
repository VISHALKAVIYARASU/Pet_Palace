<?php
session_start();
include("../db/config.php");
include("../includes/header.php");

// Initialize variables
$name = $price = $stock = $image = '';
$errors = [];
$success = '';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $image = trim($_POST['image']);

    // Store in session for testing
    $_SESSION['last_product'] = [
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'image' => $image,
    ];

    // Basic validation
    if (empty($name)) $errors[] = "Product name is required.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Enter a valid positive price.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "Stock must be a non-negative number.";
    if (empty($image)) $errors[] = "Image filename is required.";

    // If no errors, insert into DB
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $price, $stock, $image]);
            $success = "✅ Product added successfully!";
            // Clear form fields
            $name = $price = $stock = $image = '';
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!-- Feedback section -->
<div style="max-width: 500px; margin: 20px auto; font-family: Arial;">
    <?php if (!empty($errors)): ?>
        <div style="color: red; padding: 10px; border: 1px solid red; border-radius: 5px; margin-bottom: 15px;">
            <?php foreach ($errors as $error) echo "<p>• $error</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color: green; padding: 10px; border: 1px solid green; border-radius: 5px; margin-bottom: 15px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Product Form -->
<form method="POST" style="max-width: 400px; margin: 0 auto 40px; display: flex; flex-direction: column; gap: 15px; font-family: Arial;">
    <input name="name" value="<?= htmlspecialchars($name) ?>" required placeholder="Product Name" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input name="price" type="number" value="<?= htmlspecialchars($price) ?>" required placeholder="Price" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input name="stock" type="number" value="<?= htmlspecialchars($stock) ?>" required placeholder="Stock" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input name="image" value="<?= htmlspecialchars($image) ?>" required placeholder="Image Filename (e.g., toy.jpg)" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <button type="submit" style="padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
        Add Product
    </button>
</form>
