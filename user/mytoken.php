<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$orders = [];

$stmt = mysqli_prepare($conn, "SELECT token_code, shop_label, item_count, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tokens</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 16px;
        }
        .top a {
            text-decoration: none;
            background: #111827;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        }
        .token {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .muted {
            color: #4b5563;
            margin: 3px 0;
        }
        .empty {
            background: #fff;
            border-radius: 10px;
            padding: 14px;
        }
    </style>
</head>
<body>
    <div class="top">
        <a href="dashboard.php">Back</a>
    </div>
    <h2>My Tokens</h2>

    <?php if (empty($orders)): ?>
        <div class="empty">No orders yet.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card">
                <div class="token"><?= htmlspecialchars($order['token_code']) ?></div>
                <div class="muted"><b>Shop:</b> <?= htmlspecialchars($order['shop_label']) ?></div>
                <div class="muted"><b>Items:</b> <?= (int) $order['item_count'] ?></div>
                <div class="muted"><b>Total:</b> Rs <?= (int) $order['total_amount'] ?></div>
                <div class="muted"><b>Status:</b> <?= htmlspecialchars($order['status']) ?></div>
                <div class="muted"><b>Time:</b> <?= htmlspecialchars($order['created_at']) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
