<?php
session_start();
include_once("../db/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic password validation
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/', $password)) {
        echo "<p style='color: red; font-family: Arial;'>Password must be at least 6 characters with a letter, number, and symbol.</p>";
        exit;
    }

    // Check if user already exists
    $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
        echo "<p style='color: red; font-family: Arial;'>Username already exists. Choose another.</p>";
        exit;
    }

    // Hash and store
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashed, $role]);

    // Store in session for testing
    $_SESSION['registered_user'] = [
        'username' => $username,
        'role' => $role
    ];

    echo "<p style='color: green; text-align: center; font-family: Arial;'>Registered successfully! <a href='login.php'>Go to login</a></p>";
    exit;
}
?>

<form method="POST" style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; font-family: Arial; background: #f9f9f9;">
    <h2 style="text-align: center;">Register</h2>
    <input name="username" required placeholder="Username" style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;">
    <input name="password" type="password" required placeholder="Password" style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;">
    <select name="role" required style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;">
        <option value="">Select Role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit" style="width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px;">Register</button>
</form>
