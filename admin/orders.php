<?php
session_start();
require "../config/db.php";
require "../config/schema.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

ensure_orders_schema($conn);
date_default_timezone_set('Asia/Kolkata');

$statusFilter = strtoupper(trim((string) ($_GET['status'] ?? 'ALL')));
$allowedStatuses = ['ALL', 'PLACED', 'PREPARING', 'READY', 'COMPLETED', 'CANCELLED'];
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = 'ALL';
}

$orders = [];
$sql = "SELECT id, user_id, shop_label, token_code, item_count, total_amount, status, payment_method, payment_status, receipt_no, payment_ref, created_at 
        FROM orders";
if ($statusFilter !== 'ALL') {
    $sql .= " WHERE status = ?";
}
$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    if ($statusFilter !== 'ALL') {
        mysqli_stmt_bind_param($stmt, "s", $statusFilter);
    }
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
    <title>All Orders - UniBites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; padding: 18px; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .top a { text-decoration: none; background: #111827; color: #fff; padding: 8px 12px; border-radius: 8px; display: inline-block; }
        .filters a { margin-right: 6px; margin-top: 8px; display: inline-block; text-decoration: none; padding: 6px 10px; border-radius: 8px; background: #e5e7eb; color: #111827; }
        .filters a.active { background: #111827; color: #fff; }
        .card { background: #fff; border-radius: 10px; padding: 12px; margin-top: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .row { margin: 4px 0; }
        .empty { background: #fff; border-radius: 10px; padding: 14px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <a href="dashboard.php">Back</a>
        </div>
        <h2>All Orders</h2>
    </div>

    <div class="filters">
        <?php foreach ($allowedStatuses as $status): ?>
            <a class="<?= $statusFilter === $status ? 'active' : '' ?>" href="orders.php?status=<?= urlencode($status) ?>"><?= htmlspecialchars($status) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty">No orders found.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card">
                <div class="row"><b>Token:</b> <?= htmlspecialchars((string) $order['token_code']) ?></div>
                <div class="row"><b>Shop:</b> <?= htmlspecialchars((string) $order['shop_label']) ?></div>
                <div class="row"><b>User ID:</b> <?= (int) $order['user_id'] ?></div>
                <div class="row"><b>Items:</b> <?= (int) $order['item_count'] ?></div>
                <div class="row"><b>Total:</b> Rs <?= number_format((float) $order['total_amount'], 2) ?></div>
                <div class="row"><b>Status:</b> <?= htmlspecialchars((string) $order['status']) ?></div>
                <div class="row"><b>Payment:</b> <?= htmlspecialchars((string) ($order['payment_method'] ?? 'UPI') . ' - ' . (string) ($order['payment_status'] ?? 'PAID')) ?></div>
                <?php if (!empty($order['receipt_no'])): ?>
                    <div class="row"><b>Receipt:</b> <?= htmlspecialchars((string) $order['receipt_no']) ?></div>
                <?php endif; ?>
                <?php if (!empty($order['payment_ref'])): ?>
                    <div class="row"><b>UPI Ref:</b> <?= htmlspecialchars((string) $order['payment_ref']) ?></div>
                <?php endif; ?>
                <div class="row"><b>Time:</b> <?= htmlspecialchars((string) $order['created_at']) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
