<?php
session_start();
include_once("../db/config.php");

$lockout_duration = 30;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $selectedRole = $_POST['role'] ?? null;
    $current_time = time();

    if (!isset($_SESSION['login_users'][$username])) {
        $_SESSION['login_users'][$username] = ['attempts' => 0, 'last_attempt_time' => 0];
    }

    $userData = &$_SESSION['login_users'][$username];

    if ($userData['attempts'] >= 3 && ($current_time - $userData['last_attempt_time']) < $lockout_duration) {
        $remaining = $lockout_duration - ($current_time - $userData['last_attempt_time']);
        $error = "⛔ Too many failed attempts. Please wait {$remaining} seconds.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            // Case-insensitive role check
            if ($selectedRole === null) {
                $error = "❌ Please select a role.";
            } elseif (strcasecmp($selectedRole, $user['role']) !== 0) {
                $error = "❌ Selected role does not match your registered role.";
            } else {
                // Successful login
                $_SESSION['user'] = $user;
                $_SESSION['user']['selected_role'] = $selectedRole;

                $_SESSION['login_info'] = [
                    'username' => $username,
                    'password' => $_POST['password'],  // Ideally don't store raw password here
                    'role' => $selectedRole,
                    'timestamp' => date("Y-m-d H:i:s")
                ];
                $_SESSION['login_users'][$username] = ['attempts' => 0, 'last_attempt_time' => 0];

                header("Location: ../pages/home.php");
                exit;
            }
        } else {
            $userData['attempts']++;
            $userData['last_attempt_time'] = $current_time;

            if ($userData['attempts'] >= 3) {
                $error = "⛔ Too many failed attempts. Try again after {$lockout_duration} seconds.";
            } else {
                $error = "❌ Invalid credentials. Attempt {$userData['attempts']} of 3.";
            }
        }
    }

    // If there's an error, redirect to clear form fields but preserve error message via session
    if ($error) {
        $_SESSION['login_error'] = $error;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// On GET load, get error from session if any and then clear it
if (!empty($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - Pet Palace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="max-width: 420px; width: 100%;">
        <h2 class="text-center mb-4 text-primary">Login</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center" id="error-box"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <div class="alert alert-danger text-center" id="error-box" style="display:none;"></div>
        <?php endif; ?>

        <form method="POST" id="loginForm" autocomplete="off">
            <div class="mb-3">
                <input
                    name="username"
                    id="username"
                    required
                    placeholder="Username"
                    class="form-control form-control-lg"
                    value=""
                />
            </div>

            <div class="mb-3">
                <select
                    name="role"
                    id="role"
                    class="form-select form-select-lg"
                    required
                >
                    <option value="" disabled selected>Select Role</option>
                    <option value="user">User</option>
                    <option value="admin1">Admin1</option>
                    <option value="admin2">Admin2</option>
                    <option value="admin3">Admin3</option>
                </select>
            </div>

            <div class="mb-4">
                <input
                    name="password"
                    type="password"
                    required
                    placeholder="Password"
                    class="form-control form-control-lg"
                />
            </div>

            <button type="submit" class="btn btn-success w-100 btn-lg" id="submitBtn">Login</button>
        </form>

        <p class="text-center mt-3 text-secondary">
            Don't have an account?
            <a href="../auth/register.php" class="text-decoration-none">Register here</a>
        </p>
    </div>

    <script>
        const usernameInput = document.getElementById('username');
        const errorBox = document.getElementById('error-box');
        const submitBtn = document.getElementById('submitBtn');
        let interval = null;

        function checkLockout(username) {
            if (!username) return;

            fetch(`login_check.php?username=${encodeURIComponent(username)}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    const remaining = data.remaining || 0;
                    if (remaining > 0) {
                        startCountdown(remaining);
                    } else {
                        clearInterval(interval);
                        errorBox.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                })
                .catch(() => {
                    clearInterval(interval);
                    errorBox.style.display = 'none';
                    submitBtn.disabled = false;
                });
        }

        function startCountdown(seconds) {
            clearInterval(interval);
            let timeLeft = seconds;
            submitBtn.disabled = true;
            errorBox.style.display = 'block';
            errorBox.innerText = `⛔ Please wait ${timeLeft} seconds...`;

            interval = setInterval(() => {
                timeLeft--;
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    errorBox.style.display = 'none';
                    submitBtn.disabled = false;
                } else {
                    errorBox.innerText = `⛔ Please wait ${timeLeft} seconds...`;
                }
            }, 1000);
        }

        usernameInput.addEventListener('input', () => {
            const username = usernameInput.value.trim();
            if (username) checkLockout(username);
            else {
                clearInterval(interval);
                errorBox.style.display = 'none';
                submitBtn.disabled = false;
            }
        });

        window.addEventListener('load', () => {
            const initialUsername = usernameInput.value.trim();
            if (initialUsername) checkLockout(initialUsername);
        });
    </script>
</body>
</html>
