<?php
include("../db/config.php");
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->execute([$_POST['name'], $_POST['price'], $_POST['stock'], $_POST['image'], $id]);
    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0;">

    <!-- Common Header -->
    <?php include("../includes/header.php"); ?>

    <!-- Centered Form -->
    <div style="display: flex; justify-content: center; align-items: center; height: calc(100vh - 100px);"> <!-- Adjust height if needed -->
        <form method="POST" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px;">
            
            <h2 style="text-align: center; margin-bottom: 20px;">Edit Product</h2>

            <label style="display: block; margin-bottom: 8px;">Name</label>
            <input name="name" value="<?= htmlspecialchars($p['name']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">

            <label style="display: block; margin-bottom: 8px;">Price</label>
            <input name="price" type="number" value="<?= htmlspecialchars($p['price']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">

            <label style="display: block; margin-bottom: 8px;">Stock</label>
            <input name="stock" type="number" value="<?= htmlspecialchars($p['stock']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">

            <label style="display: block; margin-bottom: 8px;">Image URL</label>
            <input name="image" value="<?= htmlspecialchars($p['image']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">

            <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Update</button>
        </form>
    </div>

</body>
</html>
