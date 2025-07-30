<?php
session_start();

// If already logged in, go to home
if (isset($_SESSION['user'])) {
    header("Location: pages/home.php");
    exit;
} else {
    header("Location: auth/login.php");
    exit;
}
?>
