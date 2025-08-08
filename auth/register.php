<?php
session_start();
include_once("../db/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate role value against allowed roles
    $allowedRoles = ['user', 'admin1', 'admin2', 'admin3'];
    if (!in_array($role, $allowedRoles, true)) {
        echo "<p style='color: red; font-family: Arial;'>Invalid role selected.</p>";
        exit;
    }

    // Basic password validation: at least 6 chars, letter, number, symbol
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/', $password)) {
        echo "<p style='color: red; font-family: Arial;'>Password must be at least 6 characters with a letter, number, and symbol.</p>";
        exit;
    }

    // Check if username exists
    $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
        echo "<p style='color: red; font-family: Arial;'>Username already exists. Choose another.</p>";
        exit;
    }

    // Hash password securely
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user with role
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashed, $role]);

    // Optional: store registered user info in session
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
        <option value="admin1">Admin1</option>
        <option value="admin2">Admin2</option>
        <option value="admin3">Admin3</option>
    </select>
    <button type="submit" style="width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px;">Register</button>
</form>
