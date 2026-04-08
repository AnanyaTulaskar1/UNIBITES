<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header("Location: receipts.php");
    exit();
}

$order = null;
$stmt = mysqli_prepare($conn, "SELECT id, shop_label, token_code, items_json, item_count, total_amount, status, payment_method, payment_status, receipt_no, payment_ref, created_at FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $order = $row;
    }
    mysqli_stmt_close($stmt);
}

if (!$order) {
    header("Location: receipts.php");
    exit();
}

$items = json_decode((string) ($order['items_json'] ?? ''), true);
if (!is_array($items)) {
    $items = [];
}
$statusUpper = strtoupper((string) ($order['status'] ?? ''));
$isCancelled = ($statusUpper === 'CANCELLED');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
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
            padding: 16px;
            margin-top: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .token {
            font-size: 26px;
            font-weight: 700;
            margin: 8px 0 12px;
        }
        .row { margin: 5px 0; color: #374151; }
        .items {
            margin-top: 10px;
            background: #f9fafb;
            padding: 10px;
            border-radius: 10px;
        }
        .items ul { margin: 6px 0 0 18px; padding: 0; }
        .muted { color: #6b7280; font-size: 12px; margin-top: 6px; }
        .notice-cancelled {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="top">
        <a href="receipts.php">Back</a>
        <h2>Receipt</h2>
    </div>

    <div class="card">
        <?php if ($isCancelled): ?>
            <div class="notice-cancelled">Order Cancelled by Shop</div>
        <?php else: ?>
            <div class="row" style="color:#16a34a;font-weight:700;">Payment Successful</div>
        <?php endif; ?>
        <div class="row"><b>Payment:</b> <?= htmlspecialchars((string) ($order['payment_method'] ?? 'UPI') . ' - ' . (string) ($order['payment_status'] ?? 'PAID')) ?></div>
        <div class="row"><b>Token:</b></div>
        <div class="token"><?= htmlspecialchars((string) $order['token_code']) ?></div>
        <div class="row"><b>Shop:</b> <?= htmlspecialchars((string) $order['shop_label']) ?></div>
        <div class="row"><b>Status:</b> <?= htmlspecialchars((string) $order['status']) ?></div>
        <div class="row"><b>Date & Time:</b> <?= htmlspecialchars(date('d-m-Y H:i', strtotime((string) $order['created_at']))) ?></div>
        <?php if (!empty($order['receipt_no'])): ?>
            <div class="row"><b>Receipt No:</b> <?= htmlspecialchars((string) $order['receipt_no']) ?></div>
        <?php endif; ?>
        <?php if (!empty($order['payment_ref'])): ?>
            <div class="row"><b>Payment Ref:</b> <?= htmlspecialchars((string) $order['payment_ref']) ?></div>
        <?php endif; ?>

        <div class="items">
            <b>Items</b>
            <?php if (empty($items)): ?>
                <div class="muted">No item details available.</div>
            <?php else: ?>
                <ul>
                    <?php foreach ($items as $item): ?>
                        <li>
                            <?= htmlspecialchars((string) ($item['name'] ?? 'Item')) ?>
                            x <?= (int) ($item['qty'] ?? 0) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="row"><b>Total:</b> Rs <?= number_format((float) $order['total_amount'], 2) ?></div>
        <div class="muted">Show this receipt at the counter to collect your order.</div>
    </div>
</body>
</html>
