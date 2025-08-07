<?php
session_start();
include("../db/config.php");
include("../load_env.php");
require_once '../stripe-php-17.5.0/init.php';

loadEnv(); // Load the .env file
$key = getenv('STRIPE_SECRET_KEY');
if (!$key) {
    die('❌ Stripe secret key not loaded from .env');
}
\Stripe\Stripe::setApiKey($key);

$user = $_SESSION['user'] ?? null;
$uid = $user['id'] ?? null;
$totalAmount = 0;
$line_items = [];

// ✅ If POST request: Perform DB operations after payment button clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_payment'])) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo 'empty';
        exit;
    }

    foreach ($_SESSION['cart'] as $productId => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) continue;
        $totalAmount += $product['price'] * $qty;
    }

    // Insert transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, payment_status, payment_method, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$uid ?? 0, $totalAmount, 'success', 'stripe']);

    // Reduce stock
    foreach ($_SESSION['cart'] as $product_id => $quantity_bought) {
        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty");
        $updateStmt->execute([
            ':qty' => $quantity_bought,
            ':id' => $product_id
        ]);
    }

    // Clear user_cart table if logged in
    if ($uid) {
        $clearCartStmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ?");
        $clearCartStmt->execute([$uid]);
    }

    // Clear session cart
    unset($_SESSION['cart']);

    echo 'success';
    exit;
}

// ✅ Otherwise: normal GET request – prepare session
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div class='text-center mt-5 text-danger'><h3>🛒 Cart is empty</h3></div>";
    exit;
}

// Build Stripe line items
foreach ($_SESSION['cart'] as $productId => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) continue;

    $totalAmount += $product['price'] * $qty;

    $line_items[] = [
        'price_data' => [
            'currency' => 'inr',
            'product_data' => ['name' => $product['name']],
            'unit_amount' => $product['price'] * 100, // paise
        ],
        'quantity' => $qty,
    ];
}

// Create Stripe checkout session
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'http://localhost/PetPalace/pages/invoice.php', // Not used
    'cancel_url' => 'http://localhost/PetPalace/pages/invoice.php',  // Not used
    'metadata' => [
        'user_id' => $uid ?? 'guest',
    ],
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stripe Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="container mt-5 text-center">
    <h3 class="mb-4">Stripe Payment</h3>
    <p><strong>Total Amount:</strong> ₹<?= $totalAmount ?></p>
    <button id="checkout-button" class="btn btn-primary">Pay with Stripe</button>
</div>

<div id="payment-success" class="container mt-5 text-center d-none">
    <div class="alert alert-success p-4">
        <h1 class="display-5">✅ Payment Successful</h1>
        <p class="lead mt-3">Thank you for your order. You can view the invoice in the admin panel.</p>
    </div>
</div>

<script>
document.getElementById('checkout-button').addEventListener('click', function () {
    const stripeUrl = "<?= $checkout_session->url ?>";
    const win = window.open(stripeUrl, '_blank');

    setTimeout(() => {
        if (win && !win.closed) {
            win.close();
        }

        // ✅ Now simulate payment and trigger DB actions via POST
        fetch("payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "simulate_payment=1"
        })
        .then(res => res.text())
        .then(result => {
            if (result.trim() === "success") {
                document.querySelector('.container.mt-5.text-center').classList.add('d-none');
                document.getElementById('payment-success').classList.remove('d-none');
            } else {
                alert("❌ Payment failed or cart empty.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("❌ AJAX error occurred.");
        });

    }, 5000);
});
</script>

</body>
</html>
