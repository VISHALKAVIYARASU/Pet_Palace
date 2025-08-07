<?php
session_start();
include("../db/config.php");

// Set default user if not logged in
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<?php include("../includes/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Shop</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light" style="font-family: Arial, sans-serif; padding: 20px;">

    <div class="container">
        <h2 class="text-center text-primary mb-4">
            <?= $user ? "Welcome, " . htmlspecialchars($user['username']) : "Welcome to Pet Palace!" ?>
        </h2>

        <div class="row justify-content-center">
            <?php
            $search = $_GET['search'] ?? '';
            if ($search !== '') {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
                $stmt->execute(["%$search%"]);
            } else {
                $stmt = $pdo->query("SELECT * FROM products");
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $imageSrc = (preg_match("/^https?:\/\//", $row["image"])) ? $row["image"] : "../assets/{$row["image"]}";

                echo "
                <div class='col-md-3 mb-4'>
                    <div class='card shadow-sm h-100'>
                        <img src='{$imageSrc}' class='card-img-top' style='height: 200px; object-fit: contain;' alt='Product'>
                        <div class='card-body text-center'>
                            <h5 class='card-title'>{$row['name']}</h5>
                            <p class='card-text text-success fw-bold'>₹{$row['price']}</p>";
                
                if ($row['stock'] > 0) {
                    echo "
                        <button class='btn btn-success' onclick=\"handleCart({$row['id']})\">Add to Cart</button>";
                } else {
                    echo "<span class='text-danger'>Out of Stock</span>";
                }

                echo "
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>

    <script>
        function handleCart(productId) {
            const loggedIn = <?= $user ? 'true' : 'false' ?>;
            if (!loggedIn) {
                alert('Please log in to add products to your cart.');
                window.location.href = '../auth/login.php';
            } else {
                window.location.href = 'cart.php?add=' + productId;
            }
        }
    </script>

</body>
</html>
