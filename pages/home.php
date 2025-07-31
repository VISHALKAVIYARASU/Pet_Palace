<?php
include("../db/config.php");
if (!isset($_SESSION['user'])) header("Location: ../auth/login.php");
$user = $_SESSION['user'];
?>
<?php include("../includes/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Shop</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">

    <h2 style="text-align: center; color: #333;">Welcome, <?= htmlspecialchars($user['username']) ?></h2>

    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 30px;">
        <?php
        $stmt = $pdo->query("SELECT * FROM products");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $imageSrc = (preg_match("/^https?:\/\//", $row["image"])) ? $row["image"] : "../assets/{$row["image"]}";
            
            echo "<div style='border: 1px solid #ccc; border-radius: 8px; padding: 15px; width: 180px; background-color: white; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>
                <img src='{$imageSrc}' height='100' style='margin-bottom: 10px; object-fit: contain; max-width: 100%;'><br>
                <strong>{$row['name']}</strong><br>
                ₹{$row['price']}<br><br>";
            
            if ($row['stock'] > 0) {
                echo "<button onclick=\"location.href='cart.php?add={$row['id']}'\" style='padding: 5px 10px; border: none; background-color: #28a745; color: white; border-radius: 4px; cursor: pointer;'>➕</button>";
            } else {
                echo "<span style='color:red;'>Out of Stock</span>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
