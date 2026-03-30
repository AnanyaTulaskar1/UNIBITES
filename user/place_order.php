<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$cart = $_SESSION['cart'];
$shop = $_SESSION['cart_shop'] ?? '';

if ($shop === '') {
    foreach ($cart as $item) {
        if (!empty($item['shop'])) {
            $shop = (string) $item['shop'];
            break;
        }
    }
}

if ($shop === '') {
    header("Location: cart.php");
    exit();
}

$shopNames = [
    'tiffany' => 'Tiffany',
    'zamorin_humanities' => 'Zamorin (Humanities)',
    'north' => 'North Delicacies',
    'south' => 'South Delicacies',
    'zamorin' => 'Zamorin (Admin)',
    'shalom' => 'Shalom Cafe',
    'heavens' => 'Heavens Kitchen',
    'dreamland' => 'Dream Land Food Corner',
];

$tokenPrefixes = [
    'tiffany' => 'TIF',
    'zamorin_humanities' => 'ZMH',
    'north' => 'NOR',
    'south' => 'SOU',
    'zamorin' => 'ZAM',
    'shalom' => 'SHA',
    'heavens' => 'HEV',
    'dreamland' => 'DRM',
];

$shopLabel = $shopNames[$shop] ?? strtoupper($shop);
$tokenPrefix = $tokenPrefixes[$shop] ?? 'UNI';

$total = 0;
$itemCount = 0;
$items = [];

foreach ($cart as $item) {
    $name = (string) ($item['name'] ?? '');
    $price = (int) ($item['price'] ?? 0);
    $qty = (int) ($item['qty'] ?? 0);

    if ($name === '' || $qty <= 0) {
        continue;
    }

    $lineTotal = $price * $qty;
    $total += $lineTotal;
    $itemCount += $qty;

    $items[] = [
        'name' => $name,
        'price' => $price,
        'qty' => $qty,
        'line_total' => $lineTotal,
    ];
}

if ($itemCount <= 0) {
    header("Location: cart.php");
    exit();
}

// Ensure daily token reset follows IST date.
mysqli_query($conn, "SET time_zone = '+05:30'");

mysqli_begin_transaction($conn);

try {
    $stmtToken = mysqli_prepare($conn, "SELECT COALESCE(MAX(token_no), 0) + 1 AS next_token FROM orders WHERE shop_key = ? AND DATE(created_at) = CURDATE() FOR UPDATE");
    if (!$stmtToken) {
        throw new Exception(mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmtToken, "s", $shop);
    mysqli_stmt_execute($stmtToken);
    $tokenResult = mysqli_stmt_get_result($stmtToken);
    $tokenRow = mysqli_fetch_assoc($tokenResult);
    $tokenNo = (int) ($tokenRow['next_token'] ?? 1);
    mysqli_stmt_close($stmtToken);

    $tokenCode = $tokenPrefix . "-" . str_pad((string) $tokenNo, 3, "0", STR_PAD_LEFT);
    $itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);
    $userId = (int) $_SESSION['user_id'];
    $totalAmount = number_format($total, 2, '.', '');

    $insertSql = "INSERT INTO orders (user_id, shop_key, shop_label, token_no, token_code, items_json, item_count, total_amount, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PLACED')";
    $stmtInsert = mysqli_prepare($conn, $insertSql);
    if (!$stmtInsert) {
        throw new Exception(mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmtInsert, "ississid", $userId, $shop, $shopLabel, $tokenNo, $tokenCode, $itemsJson, $itemCount, $totalAmount);
    mysqli_stmt_execute($stmtInsert);
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtInsert);

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    die("Failed to place order: " . $e->getMessage());
}

$_SESSION['last_order_id'] = $orderId;
$_SESSION['last_token'] = $tokenCode;
$_SESSION['cart'] = [];
unset($_SESSION['cart_shop']);

$orderStatus = 'PLACED';
$orderTime = '';
$stmtStatus = mysqli_prepare($conn, "SELECT status, created_at FROM orders WHERE id = ? AND user_id = ?");
if ($stmtStatus) {
    $uid = (int) $_SESSION['user_id'];
    mysqli_stmt_bind_param($stmtStatus, "ii", $orderId, $uid);
    mysqli_stmt_execute($stmtStatus);
    $statusResult = mysqli_stmt_get_result($stmtStatus);
    if ($statusRow = mysqli_fetch_assoc($statusResult)) {
        $orderStatus = (string) ($statusRow['status'] ?? $orderStatus);
        $orderTime = (string) ($statusRow['created_at'] ?? '');
    }
    mysqli_stmt_close($stmtStatus);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Placed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 24px;
        }
        .card {
            max-width: 560px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }
        .token {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            margin: 10px 0 14px;
        }
        .row {
            margin: 6px 0;
            color: #374151;
        }
        .links a {
            display: inline-block;
            margin-right: 10px;
            margin-top: 12px;
            text-decoration: none;
            background: #111827;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
        }
        .auto {
            margin-top: 8px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Order placed successfully</h2>
        <div class="token"><?= htmlspecialchars($tokenCode) ?></div>
        <div class="row"><b>Shop:</b> <?= htmlspecialchars($shopLabel) ?></div>
        <div class="row"><b>Items:</b> <?= (int) $itemCount ?></div>
        <div class="row"><b>Total:</b> Rs <?= (int) $total ?></div>
        <div class="row"><b>Status:</b> <?= htmlspecialchars($orderStatus) ?></div>
        <?php if ($orderTime !== ''): ?>
            <div class="row"><b>Time:</b> <?= htmlspecialchars($orderTime) ?></div>
        <?php endif; ?>
        <div class="auto">Auto-refresh is on (every 20 seconds).</div>
        <div class="links">
            <a href="mytoken.php">View My Tokens</a>
            <a href="dashboard.php">Back to Home</a>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 20000);
    </script>
</body>
</html>
