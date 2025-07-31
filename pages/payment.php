<?php
session_start();
include("../db/config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
    <?php include("../includes/header.php"); ?>

    <h3 style="color: #333;">Payment</h3>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        // 1. Validate stock availability first
        foreach ($_SESSION['cart'] as $productId => $qty) {
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stock'] < $qty) {
                $errors[] = "Insufficient stock for product ID $productId.";
            }
        }

        // 2. If all stock is valid, proceed with payment
        if (empty($errors)) {
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$qty, $productId]);
            }
            echo "<p style='color: green; font-weight: bold;'>Payment Successful!</p>";
            unset($_SESSION['cart']);
        } else {
            // 3. Display error(s)
            foreach ($errors as $e) {
                echo "<p style='color: red; font-weight: bold;'>$e</p>";
            }
        }
    } else {
    ?>
        <form method="POST" style="display: flex; flex-direction: column; gap: 10px; max-width: 300px;">
            <input 
                name="upi" 
                required 
                placeholder="Enter UPI ID" 
                style="padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;"
            >
            <button 
                type="submit" 
                style="padding: 10px; background-color: #28a745; color: white; font-size: 16px; border: none; border-radius: 4px; cursor: pointer;"
            >
                Pay
            </button>
        </form>
    <?php } ?>
    
</body>
</html>
