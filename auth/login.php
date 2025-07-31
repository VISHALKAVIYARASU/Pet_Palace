<?php
include_once("../db/config.php");

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

$lockout_duration = 30; // seconds

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_time = time();

    // Check if user is locked out
    if ($_SESSION['login_attempts'] >= 3 && ($current_time - $_SESSION['last_attempt_time']) < $lockout_duration) {
        $remaining = $lockout_duration - ($current_time - $_SESSION['last_attempt_time']);
        echo "<div style='color: red; text-align: center; margin-top: 20px;'>Too many failed attempts. Please wait {$remaining} seconds.</div>";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // if ($user && password_verify($_POST['password'], $user['password']))
        // if ($user && $_POST['password'] == $user['password']) 
        if ($user && password_verify($_POST['password'], $user['password'])){
            $_SESSION['user'] = $user;
            $_SESSION['login_attempts'] = 0; // reset on successful login
            header("Location: ../pages/home.php");
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = $current_time;

            if ($_SESSION['login_attempts'] >= 3) {
                echo "<div style='color: red; text-align: center; margin-top: 20px;'>Too many failed attempts. Try again after 30 seconds.</div>";
            } else {
                echo "<div style='color: red; text-align: center; margin-top: 20px;'>Invalid credentials. Attempt {$_SESSION['login_attempts']} of 3.</div>";
            }
        }
    }
}
?>

<form method="POST" style="max-width: 400px; margin: 80px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: Arial, sans-serif;">
    <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Login</h2>
    
    <input name="username" required placeholder="Username" style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
    
    <input name="password" type="password" required placeholder="Password" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
    
    <button type="submit" style="width: 100%; padding: 10px; background-color: #5cb85c; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Login</button>

    <p style="text-align: center; margin-top: 15px; color: #555;">Don't have an account? <a href="../auth/register.php" style="color: #007bff;">Register here</a></p>
</form>
