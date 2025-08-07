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
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include("../includes/header.php"); ?>
    
    <div class="container my-4">
        <h2 class="text-primary mb-4">Admin Panel</h2>

        <a href='add_product.php' class="btn btn-success mb-4">➕ Add New Product</a>

        <?php
        $stmt = $pdo->query("SELECT * FROM products");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <div class="card mb-3" style="max-width: 500px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="card-text mb-0">₹<?= $row['price'] ?> - Stock: <?= $row['stock'] ?></p>
                    </div>
                    <div>
                        <a href='edit_product.php?id=<?= $row['id'] ?>' class="btn btn-sm btn-outline-primary me-2">✏️</a>
                        <a href='?delete=<?= $row['id'] ?>' class="btn btn-sm btn-outline-danger">❌</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>
