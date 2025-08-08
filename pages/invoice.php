<?php
session_start();
include("../db/config.php");

// Assuming user info is stored in session like this:
$user = $_SESSION['user'] ?? null;

if (!$user) {
    // Not logged in - redirect to login or show error
    header("Location: ../auth/login.php");
    exit;
}

$userRole = strtolower($user['role']); // e.g. 'admin1', 'user', etc.
$userId = $user['id'] ?? null;          // Assuming user id is stored as 'id' in session

// If user is admin (role contains 'admin'), show all transactions; else filter by user_id
if (strpos($userRole, 'admin') === 0) {
    // Admin roles: admin, admin1, admin2, admin3
    $stmt = $pdo->prepare("SELECT * FROM transactions ORDER BY created_at DESC");
    $stmt->execute();
} else {
    // Normal user: only their transactions
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
}

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("../includes/header.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Invoice Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .print-button {
            display: block;
            margin: 0 auto 30px;
            width: 220px;
            text-align: center;
            padding: 12px 20px;
            background-color: #3498db;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        /* Print Styling */
        @media print {
            .print-button {
                display: none;
            }

            body {
                margin: 0;
                background: white;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>

    <h2>Pet Palace - All Transactions</h2>
    <button class="print-button" onclick="window.print()">🖨️ Download Invoice / Print</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Method</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $txn): ?>
            <tr>
                <td><?= htmlspecialchars($txn['id']) ?></td>
                <td><?= htmlspecialchars($txn['user_id']) ?></td>
                <td>₹<?= number_format($txn['amount'], 2) ?></td>
                <td><?= htmlspecialchars($txn['payment_status']) ?></td>
                <td><?= htmlspecialchars($txn['payment_method']) ?></td>
                <td><?= htmlspecialchars($txn['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr><td colspan="6" style="text-align:center; color:#666;">No transactions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
