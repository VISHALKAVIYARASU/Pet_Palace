<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">

<!-- Bootstrap 5 CSS (if not already included) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .navbar-brand {
        font-family: 'Pacifico', cursive;
        color: #ffcc00 !important;
        font-size: 28px;
        margin: auto;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .navbar-custom {
        background-color: #333;
    }

    .navbar-custom .nav-link {
        color: white !important;
    }
</style>

<nav class="navbar navbar-expand-md navbar-custom">
    <div class="container-fluid">
        <!-- Brand center -->
        <a class="navbar-brand mx-auto" style="font-size: 30px;" href="#">PetPalace</a>

        <!-- Hamburger toggle -->
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible content -->
        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
            <!-- Left side nav links -->
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="invoice.php">Invoice Report</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                <?php if (!isset($_SESSION['user'])): ?>
                    <li class="nav-item"><a class="nav-link" href="../auth/login.php">Login</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>

            <!-- Search bar only on home.php -->
            <?php if ($current_page == 'home.php'): ?>
                <form class="d-flex" action="home.php" method="GET">
                    <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Search products..." aria-label="Search">
                    <button class="btn btn-sm btn-outline-light" type="submit">Search</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<hr style="border: 1px solid #ccc;">
