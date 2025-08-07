<?php
session_start();
include("../db/config.php");
require_once '../stripe-php-17.5.0/init.php';

$key = getenv('STRIPE_SECRET_KEY');
\Stripe\Stripe::setApiKey($key);

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    die("❌ Invalid session.");
}

$checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
$uid = $checkout_session->metadata['user_id'] ?? 0;

$totalAmount = $checkout_session->amount_total / 100; // convert paise to INR

// Insert transaction
$stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, payment_status, payment_method, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$uid, $totalAmount, 'success', 'stripe']);

// Update product stock
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity_bought) {
        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty");
        $updateStmt->execute([
            ':qty' => $quantity_bought,
            ':id' => $product_id
        ]);
    }
}

// Clear user cart table (optional)
if ($uid && is_numeric($uid)) {
    $clearCartStmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ?");
    $clearCartStmt->execute([$uid]);
}

// Clear session cart
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include("../includes/header.php"); ?>

<div class="container mt-5 text-center">
    <div class="alert alert-success p-5 shadow-lg">
        <h1 class="display-5">✅ Payment Successful</h1>
        <p class="lead mt-3">Thank you for your order. Invoice is being generated.</p>
        <a href="invoice.php" class="btn btn-outline-success mt-3">View Invoice</a>
    </div>
</div>
</body>
</html>
