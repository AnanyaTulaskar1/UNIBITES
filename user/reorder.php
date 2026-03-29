<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int) $_POST['order_id'];
    $stmt = mysqli_prepare($conn, "SELECT shop_key, items_json FROM orders WHERE id = ? AND user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($order && !empty($order['shop_key']) && !empty($order['items_json'])) {
            $items = json_decode($order['items_json'], true);
            if (is_array($items)) {
                $_SESSION['cart'] = [];
                $_SESSION['cart_shop'] = (string) $order['shop_key'];
                $idx = 0;
                foreach ($items as $item) {
                    $name = (string) ($item['name'] ?? '');
                    $price = (int) ($item['price'] ?? 0);
                    $qty = (int) ($item['qty'] ?? 0);
                    if ($name === '' || $qty <= 0) {
                        continue;
                    }
                    $cart_key = $_SESSION['cart_shop'] . '_r' . $idx;
                    $_SESSION['cart'][$cart_key] = [
                        'shop' => $_SESSION['cart_shop'],
                        'name' => $name,
                        'price' => $price,
                        'qty' => $qty,
                    ];
                    $idx++;
                }
                header("Location: cart.php");
                exit();
            }
        }
    }
}

$orders = [];
$stmt = mysqli_prepare($conn, "SELECT id, shop_label, item_count, total_amount, created_at FROM orders WHERE user_id = ? ORDER BY id DESC");
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
    <title>Reorder</title>
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
        .title {
            font-weight: 700;
            margin-bottom: 6px;
        }
        .muted {
            color: #4b5563;
            margin: 3px 0;
        }
        .btn {
            border: none;
            background: #fc8019;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
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
    <h2>Reorder</h2>

    <?php if (empty($orders)): ?>
        <div class="empty">No orders yet.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card">
                <div class="title"><?= htmlspecialchars($order['shop_label']) ?></div>
                <div class="muted"><b>Items:</b> <?= (int) $order['item_count'] ?></div>
                <div class="muted"><b>Total:</b> Rs <?= (int) $order['total_amount'] ?></div>
                <div class="muted"><b>Time:</b> <?= htmlspecialchars($order['created_at']) ?></div>
                <form method="post">
                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                    <button type="submit" class="btn">Reorder</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
