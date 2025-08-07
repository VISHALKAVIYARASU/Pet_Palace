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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("../includes/header.php"); ?>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <form method="POST" class="bg-white p-4 rounded shadow" style="width: 100%; max-width: 400px;">
            <h3 class="text-center mb-4">Edit Product</h3>

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" value="<?= htmlspecialchars($p['name']) ?>" required class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input name="price" type="number" value="<?= htmlspecialchars($p['price']) ?>" required class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input name="stock" type="number" value="<?= htmlspecialchars($p['stock']) ?>" required class="form-control">
            </div>

            <div class="mb-4">
                <label class="form-label">Image URL</label>
                <input name="image" value="<?= htmlspecialchars($p['image']) ?>" required class="form-control">
            </div>

            <button type="submit" class="btn btn-success w-100">Update</button>
        </form>
    </div>

</body>
</html>
