<?php
session_start();
require "../config/db.php";
require "../config/schema.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'shop') {
    header("Location: ../login.php");
    exit();
}

ensure_orders_schema($conn);

$shopName = trim((string) ($_SESSION['shop_name'] ?? $_SESSION['name'] ?? ''));
$shopKeyRaw = strtolower($shopName);
$shopKeyClean = preg_replace('/[^a-z]/', '', $shopKeyRaw);

$shopNameToKey = [
    'north' => 'north',
    'northdelicacies' => 'north',
    'south' => 'south',
    'southdelicacies' => 'south',
    'zamorin' => 'zamorin',
    'zamorinadmin' => 'zamorin',
    'tiffany' => 'tiffany',
    'zamorinhumanities' => 'zamorin_humanities',
    'shalom' => 'shalom',
    'shalomcafe' => 'shalom',
    'heavens' => 'heavens',
    'heavenskitchen' => 'heavens',
    'dreamland' => 'dreamland',
    'dreamlandfoodcorner' => 'dreamland',
];

$shopKey = $shopNameToKey[$shopKeyClean] ?? ($shopNameToKey[$shopKeyRaw] ?? '');
$allowedStatuses = ['PLACED', 'READY', 'CANCELLED'];

$filterStatus = strtoupper(trim((string) ($_GET['status'] ?? 'ALL')));
if ($filterStatus !== 'ALL' && !in_array($filterStatus, $allowedStatuses, true)) {
    $filterStatus = 'ALL';
}

$msg = ['type' => '', 'text' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    $orderId = (int) ($_POST['order_id'] ?? 0);

    if ($shopKey === '') {
        $msg = ['type' => 'err', 'text' => 'Shop mapping not found. Unable to cancel this order.'];
    } elseif ($orderId <= 0) {
        $msg = ['type' => 'err', 'text' => 'Invalid order selected for cancellation.'];
    } else {
        $stmtCancel = mysqli_prepare(
            $conn,
            "UPDATE orders SET status = 'CANCELLED' WHERE id = ? AND shop_key = ? AND status NOT IN ('COMPLETED', 'CANCELLED')"
        );
        if ($stmtCancel) {
            mysqli_stmt_bind_param($stmtCancel, "is", $orderId, $shopKey);
            mysqli_stmt_execute($stmtCancel);
            $affected = mysqli_stmt_affected_rows($stmtCancel);
            mysqli_stmt_close($stmtCancel);

            if ($affected > 0) {
                $msg = ['type' => 'ok', 'text' => 'Order cancelled successfully.'];
            } else {
                $msg = ['type' => 'err', 'text' => 'Unable to cancel. It may already be completed or cancelled.'];
            }
        } else {
            $msg = ['type' => 'err', 'text' => 'Unable to cancel this order right now.'];
        }
    }
}

$orders = [];
if ($shopKey !== '') {
    if ($filterStatus === 'ALL') {
        $sql = "SELECT id, token_code, shop_label, item_count, total_amount, status, payment_method, payment_status, receipt_no, payment_ref, created_at, items_json FROM orders WHERE shop_key = ? ORDER BY id DESC";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $shopKey);
        }
    } else {
        $sql = "SELECT id, token_code, shop_label, item_count, total_amount, status, payment_method, payment_status, receipt_no, payment_ref, created_at, items_json FROM orders WHERE shop_key = ? AND status = ? ORDER BY id DESC";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $shopKey, $filterStatus);
        }
    }

    if (!empty($stmt)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

function parseItems(string $itemsJson): array {
    $decoded = json_decode($itemsJson, true);
    if (!is_array($decoded)) {
        return [];
    }
    return $decoded;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop Orders - UniBites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; padding: 16px; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .top a { text-decoration: none; background: #111827; color: #fff; padding: 8px 12px; border-radius: 8px; display: inline-block; }
        .filters a { margin-right: 6px; margin-top: 8px; display: inline-block; text-decoration: none; padding: 6px 10px; border-radius: 8px; background: #e5e7eb; color: #111827; }
        .filters a.active { background: #111827; color: #fff; }
        .card { background: #fff; border-radius: 10px; padding: 14px; margin-bottom: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.06); transition: background 0.2s ease, border-color 0.2s ease; border-left: 4px solid transparent; }
        .card.new { background: #ecfdf3; border-left-color: #16a34a; }
        .token { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .muted { color: #4b5563; margin: 3px 0; }
        .empty { background: #fff; border-radius: 10px; padding: 14px; }
        .items { margin-top: 8px; background: #f9fafb; padding: 10px; border-radius: 8px; }
        .items ul { margin: 6px 0 0 18px; padding: 0; }
        .status-form { margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap; }
        select, button { padding: 8px; border-radius: 8px; border: 1px solid #d1d5db; }
        button { background: #16a34a; color: #fff; border: none; cursor: pointer; }
        .msg { margin: 10px 0; padding: 10px; border-radius: 8px; }
        .ok { background: #dcfce7; color: #166534; }
        .err { background: #fee2e2; color: #991b1b; }
        .auto { margin: 10px 0 0; font-size: 13px; color: #374151; }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <a href="dashboard.php">Back</a>
        </div>
        <h2>Shop Orders</h2>
    </div>
    <div class="auto">Auto-refresh is on (every 30 seconds). Orders are auto-marked ready after payment. Shops can cancel before completion.</div>

    <div class="filters">
        <?php foreach (array_merge(['ALL'], $allowedStatuses) as $status): ?>
            <a class="<?= $filterStatus === $status ? 'active' : '' ?>" href="orders.php?status=<?= urlencode($status) ?>"><?= htmlspecialchars($status) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($msg['text'])): ?>
        <div class="msg <?= $msg['type'] === 'ok' ? 'ok' : 'err' ?>"><?= htmlspecialchars($msg['text']) ?></div>
    <?php endif; ?>

    <?php if ($shopKey === ''): ?>
        <div class="empty">Shop mapping not found for this account. Use shop account name like north/south/tiffany/shalom.</div>
    <?php elseif (empty($orders)): ?>
        <div class="empty">No orders found for this filter.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card" data-order-id="<?= (int) $order['id'] ?>">
                <div class="token"><?= htmlspecialchars($order['token_code']) ?></div>
                <div class="muted"><b>Shop:</b> <?= htmlspecialchars($order['shop_label']) ?></div>
                <div class="muted"><b>Items:</b> <?= (int) $order['item_count'] ?></div>
                <div class="muted"><b>Total:</b> Rs <?= number_format((float) $order['total_amount'], 2) ?></div>
                <div class="muted"><b>Status:</b> <?= htmlspecialchars($order['status']) ?></div>
                <div class="muted"><b>Payment:</b> <?= htmlspecialchars(($order['payment_method'] ?? 'UPI') . ' - ' . ($order['payment_status'] ?? 'PAID')) ?></div>
                <?php if (!empty($order['receipt_no'])): ?>
                    <div class="muted"><b>Receipt:</b> <?= htmlspecialchars($order['receipt_no']) ?></div>
                <?php endif; ?>
                <?php if (!empty($order['payment_ref'])): ?>
                    <div class="muted"><b>Payment Ref:</b> <?= htmlspecialchars($order['payment_ref']) ?></div>
                <?php endif; ?>
                <div class="muted"><b>Time:</b> <?= htmlspecialchars($order['created_at']) ?></div>

                <?php $items = parseItems((string) $order['items_json']); ?>
                <?php if (!empty($items)): ?>
                    <div class="items">
                        <b>Items List:</b>
                        <ul>
                            <?php foreach ($items as $item): ?>
                                <li>
                                    <?= htmlspecialchars((string) ($item['name'] ?? 'Item')) ?>
                                    x <?= (int) ($item['qty'] ?? 0) ?>
                                    (Rs <?= number_format((float) ($item['price'] ?? 0), 2) ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!in_array(strtoupper((string) $order['status']), ['COMPLETED', 'CANCELLED'], true)): ?>
                    <form class="status-form" method="post" onsubmit="return confirm('Cancel this order? This cannot be undone.');">
                        <input type="hidden" name="action" value="cancel">
                        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                        <button type="submit" style="background:#dc2626;">Cancel Order</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        (function() {
            var cards = document.querySelectorAll('.card[data-order-id]');
            if (!cards.length) return;

            var maxId = 0;
            cards.forEach(function(card) {
                var id = parseInt(card.getAttribute('data-order-id'), 10);
                if (!isNaN(id) && id > maxId) maxId = id;
            });

            var storageKey = 'shop_last_seen_order_id';
            var lastSeen = parseInt(localStorage.getItem(storageKey) || '0', 10);

            if (!isNaN(lastSeen)) {
                cards.forEach(function(card) {
                    var id = parseInt(card.getAttribute('data-order-id'), 10);
                    if (!isNaN(id) && id > lastSeen) {
                        card.classList.add('new');
                    }
                });
            }

            if (maxId > lastSeen) {
                localStorage.setItem(storageKey, String(maxId));
            }

            setTimeout(function() {
                window.location.reload();
            }, 30000);
        })();
    </script>
</body>
</html>
