<?php
session_start();
include("../db/config.php");

$user = $_SESSION['user'] ?? null;
$uid = $user['id'] ?? null;

$popup = "";

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    if ($uid) {
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM user_cart WHERE user_id = ?");
        $stmt->execute([$uid]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $_SESSION['cart'][$item['product_id']] = $item['quantity'];
        }
    }
}

// Function to sync session to DB
function syncCartToDB($pdo, $uid, $cart) {
    if (!$uid) return;
    foreach ($cart as $pid => $qty) {
        $stmt = $pdo->prepare("INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE quantity = ?");
        $stmt->execute([$uid, $pid, $qty, $qty]);
    }
}

// Handle Add/Inc/Dec
if (isset($_GET['add']) || isset($_GET['inc'])) {
    $id = $_GET['add'] ?? $_GET['inc'];
    $currentQty = $_SESSION['cart'][$id] ?? 0;

    // Check stock before increment
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $currentQty + 1 <= $product['stock']) {
        $_SESSION['cart'][$id] = $currentQty + 1;
    } else {
        $popup = "Cannot add more than available stock.";
    }
}

if (isset($_GET['dec'])) {
    $id = $_GET['dec'];
    if (isset($_SESSION['cart'][$id]) && $_SESSION['cart'][$id] > 1) {
        $_SESSION['cart'][$id]--;
    }
}

if ($uid) syncCartToDB($pdo, $uid, $_SESSION['cart']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f9f9f9; padding: 20px; }
        .cart-container { max-width: 700px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .cart-item { padding: 10px 0; border-bottom: 1px solid #e9ecef; }
        .cart-item:last-child { border-bottom: none; }
        .btn-custom { width: 35px; height: 35px; padding: 0; }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <?php if (!empty($popup)) : ?>
        <script>alert("<?= htmlspecialchars($popup) ?>");</script>
    <?php endif; ?>

    <div class="container mt-4">
        <div class="cart-container">
            <h3 class="text-center mb-4">Your Cart</h3>
            <?php
            if (empty($_SESSION['cart'])) {
                echo "<p class='text-center'>Your cart is empty.</p>";
            } else {
                foreach ($_SESSION['cart'] as $id => $qty) {
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $p = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($p) {
                        echo "<div class='row cart-item align-items-center'>";
                        echo "<div class='col-md-6'>" . htmlspecialchars($p['name']) . "</div>";
                        echo "<div class='col-md-2'>₹" . ($p['price'] * $qty) . "</div>";
                        echo "<div class='col-md-4 text-end'>";
                        echo "<a class='btn btn-sm btn-outline-success btn-custom' href='?inc=$id'>+</a> ";
                        echo "<span class='mx-2'>$qty</span>";
                        echo "<a class='btn btn-sm btn-outline-danger btn-custom' href='?dec=$id'>-</a>";
                        echo "</div></div>";
                    }
                }
                echo "<div class='text-center mt-4'><a href='payment.php' class='btn btn-primary'>Proceed to Pay</a></div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
