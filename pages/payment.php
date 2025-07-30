<?php
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
        echo "<p style='color: green; font-weight: bold;'>Payment Successful!</p>";
        unset($_SESSION['cart']);
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
