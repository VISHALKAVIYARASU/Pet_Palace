
<div style="background-color: #333; overflow: hidden; padding: 10px 0; font-family: Arial;">
    <div style="max-width: 1000px; margin: 0 auto; display: flex; justify-content: center; gap: 30px;">
        <a href="home.php" style="color: white; text-decoration: none; padding: 14px 16px; display: inline-block;">🏠 Home</a>
        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="admin.php" style="color: white; text-decoration: none; padding: 14px 16px; display: inline-block;">🛠️ Admin Panel</a>
        <?php endif; ?>
        <a href="cart.php" style="color: white; text-decoration: none; padding: 14px 16px; display: inline-block;">🛒 Cart</a>
        <a href="../logout.php" style="color: white; text-decoration: none; padding: 14px 16px; display: inline-block;">🚪 Logout</a>
    </div>
</div>
<hr style="border: 1px solid #ccc;">
