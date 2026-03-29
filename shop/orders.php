<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'shop') {
    header("Location: ../login.php");
    exit();
}

$createOrdersSql = "
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shop_key VARCHAR(64) NOT NULL,
    shop_label VARCHAR(120) NOT NULL,
    token_no INT NOT NULL,
    token_code VARCHAR(40) NOT NULL,
    items_json LONGTEXT NOT NULL,
    item_count INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'PLACED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_shop_token (shop_key, token_no),
    INDEX idx_user (user_id)
)";
mysqli_query($conn, $createOrdersSql);

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
$allowedStatuses = ['PLACED', 'PREPARING', 'READY', 'COMPLETED', 'CANCELLED'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'], $_POST['csrf_token'])) {
    $orderId = (int) $_POST['order_id'];
    $newStatus = strtoupper(trim((string) $_POST['new_status']));
    $csrf = (string) $_POST['csrf_token'];

    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = 'Invalid request token. Please refresh and try again.';
    } elseif ($shopKey === '') {
        $error = 'Shop mapping not found for this account.';
    } elseif (!in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid status selected.';
    } else {
        $sqlUpdate = "UPDATE orders SET status = ? WHERE id = ? AND shop_key = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        if ($stmtUpdate) {
            mysqli_stmt_bind_param($stmtUpdate, "sis", $newStatus, $orderId, $shopKey);
            mysqli_stmt_execute($stmtUpdate);
            if (mysqli_stmt_affected_rows($stmtUpdate) > 0) {
                $message = 'Order status updated successfully.';
            } else {
                $error = 'No matching order found or status unchanged.';
            }
            mysqli_stmt_close($stmtUpdate);
        } else {
            $error = 'Failed to update order status.';
        }
    }
}

$filterStatus = strtoupper(trim((string) ($_GET['status'] ?? 'ALL')));
if ($filterStatus !== 'ALL' && !in_array($filterStatus, $allowedStatuses, true)) {
    $filterStatus = 'ALL';
}

$orders = [];
if ($shopKey !== '') {
    if ($filterStatus === 'ALL') {
        $sql = "SELECT id, token_code, shop_label, item_count, total_amount, status, created_at, items_json FROM orders WHERE shop_key = ? ORDER BY id DESC";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $shopKey);
        }
    } else {
        $sql = "SELECT id, token_code, shop_label, item_count, total_amount, status, created_at, items_json FROM orders WHERE shop_key = ? AND status = ? ORDER BY id DESC";
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
    <div class="auto">Auto-refresh is on (every 30 seconds). New orders are highlighted.</div>

    <div class="filters">
        <?php foreach (array_merge(['ALL'], $allowedStatuses) as $status): ?>
            <a class="<?= $filterStatus === $status ? 'active' : '' ?>" href="orders.php?status=<?= urlencode($status) ?>"><?= htmlspecialchars($status) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if ($message !== ''): ?><div class="msg ok"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

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

                <form class="status-form" method="post" action="orders.php<?= $filterStatus !== 'ALL' ? '?status=' . urlencode($filterStatus) : '' ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                    <select name="new_status" required>
                        <?php foreach ($allowedStatuses as $status): ?>
                            <option value="<?= htmlspecialchars($status) ?>" <?= strtoupper((string) $order['status']) === $status ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Update Status</button>
                </form>
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
