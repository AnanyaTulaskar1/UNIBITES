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
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #f4f6f8 0%, #e8f4f8 100%);
            padding: 16px;
            color: #111827;
        }
        .topbar {
            background: linear-gradient(90deg, #924b22, #e09d44);
            color: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        .back-link {
            text-decoration: none;
            background: rgba(17, 24, 39, 0.9);
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
        }
        .card {
            background: #fff4e8;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 10px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
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
            background: #fff4e8;
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        }
        .auto {
            margin: 6px 0 12px;
            font-size: 12px;
            color: #6b7280;
        }
        .title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a class="back-link" href="dashboard.php">Back</a>
        <div class="title">My Tokens</div>
        <span></span>
    </div>
    <div class="auto">Auto-refresh is on (every 20 seconds).</div>

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
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 20000);
    </script>
</body>
</html>
