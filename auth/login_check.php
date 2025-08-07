<?php
session_start();

$lockout_duration = 30;
$remaining = 0;

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $current_time = time();

    if (isset($_SESSION['login_users'][$username])) {
        $userData = $_SESSION['login_users'][$username];
        if ($userData['attempts'] >= 3) {
            $remaining = $lockout_duration - ($current_time - $userData['last_attempt_time']);
            if ($remaining < 0) $remaining = 0;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['remaining' => $remaining]);
