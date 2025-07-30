<?php
include_once("../db/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // if ($user && password_verify($_POST['password'], $user['password'])) 
    if ($user && $_POST['password'] == $user['password']) {
        $_SESSION['user'] = $user;
        header("Location: ../pages/home.php");
    } else {
        echo "<div style='color: red; text-align: center; margin-top: 20px;'>Invalid credentials.</div>";
    }
}
?>

<form method="POST" style="max-width: 400px; margin: 80px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: Arial, sans-serif;">
    <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Login</h2>
    
    <input name="username" required placeholder="Username" style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
    
    <input name="password" type="password" required placeholder="Password" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
    
    <button type="submit" style="width: 100%; padding: 10px; background-color: #5cb85c; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Login</button>
</form>
