<?php
include("../db/config.php");
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}
if (isset($_GET['inc'])) $_SESSION['cart'][$_GET['inc']]++;
if (isset($_GET['dec']) && $_SESSION['cart'][$_GET['dec']] > 1) $_SESSION['cart'][$_GET['dec']]--;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
   <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f9f9f9;
        }
        h3 {
            color: #333;
        }
        .cart-item {
            background: #fff;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .btn {
            display: inline-block;
            background: #28a745;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #218838;
        }
        a {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>
    <h3>Your Cart</h3>
    <?php
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<div class='cart-item'>";
        echo "{$p['name']} x $qty - ₹" . ($p['price'] * $qty);
        echo " <a href='?inc=$id'>➕</a> <a href='?dec=$id'>➖</a>";
        echo "</div><br>";
    }
    ?>
    <a class="btn" href="payment.php">Proceed to Pay</a>
</body>
</html>