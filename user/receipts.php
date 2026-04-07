<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$orders = [];

$stmt = mysqli_prepare($conn, "SELECT id, shop_label, token_code, item_count, total_amount, payment_method, payment_status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC");
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
    <title>My Receipts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f5f5;
            padding: 16px;
            color: #111827;
        }
        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .top a {
            text-decoration: none;
            background: #111827;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            margin-top: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .row { margin: 4px 0; color: #374151; }
        .btn {
            text-decoration: none;
            background: #16a34a;
            color: #fff;
            padding: 6px 10px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
            margin-top: 8px;
        }
        .empty {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="top">
        <a href="account.php">Back</a>
        <h2>My Receipts</h2>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty">No receipts yet.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card">
                <div class="row"><b>Token:</b> <?= htmlspecialchars((string) $order['token_code']) ?></div>
                <div class="row"><b>Shop:</b> <?= htmlspecialchars((string) $order['shop_label']) ?></div>
                <div class="row"><b>Items:</b> <?= (int) $order['item_count'] ?></div>
                <div class="row"><b>Total:</b> Rs <?= number_format((float) $order['total_amount'], 2) ?></div>
                <div class="row"><b>Payment:</b> <?= htmlspecialchars((string) ($order['payment_method'] ?? 'UPI') . ' - ' . (string) ($order['payment_status'] ?? 'PAID')) ?></div>
                <div class="row"><b>Date & Time:</b> <?= htmlspecialchars(date('d-m-Y H:i', strtotime((string) $order['created_at']))) ?></div>
                <a class="btn" href="receipt.php?id=<?= (int) $order['id'] ?>">View Receipt</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
