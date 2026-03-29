<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'shop') {
    header("Location: ../login.php");
    exit();
}

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

$stats = [
    'total' => 0,
    'today' => 0,
    'revenue' => 0.0,
    'PLACED' => 0,
    'PREPARING' => 0,
    'READY' => 0,
    'COMPLETED' => 0,
    'CANCELLED' => 0,
];

if ($shopKey !== '') {
    $sql = "SELECT
                COUNT(*) AS total_orders,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS today_orders,
                COALESCE(SUM(total_amount), 0) AS total_revenue
            FROM orders
            WHERE shop_key = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $shopKey);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $stats['total'] = (int) ($row['total_orders'] ?? 0);
            $stats['today'] = (int) ($row['today_orders'] ?? 0);
            $stats['revenue'] = (float) ($row['total_revenue'] ?? 0);
        }
        mysqli_stmt_close($stmt);
    }

    $stmt2 = mysqli_prepare($conn, "SELECT status, COUNT(*) AS cnt FROM orders WHERE shop_key = ? GROUP BY status");
    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, "s", $shopKey);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);
        while ($row2 = mysqli_fetch_assoc($result2)) {
            $status = strtoupper((string) $row2['status']);
            if (array_key_exists($status, $stats)) {
                $stats[$status] = (int) $row2['cnt'];
            }
        }
        mysqli_stmt_close($stmt2);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop Dashboard - UniBites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .wrap {
            max-width: 980px;
            margin: 0 auto;
            padding: 20px;
        }
        .head {
            background: #111827;
            color: #fff;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .head h1 {
            margin: 0 0 4px;
            font-size: 24px;
        }
        .sub {
            margin: 0;
            color: #d1d5db;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #16a34a;
            color: #fff;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-left: 8px;
        }
        .btn.secondary {
            background: #374151;
        }
        .btn.logout {
            background: #dc2626;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }
        .label {
            color: #4b5563;
            font-size: 13px;
            margin-bottom: 6px;
        }
        .value {
            font-size: 24px;
            font-weight: 700;
        }
        .status-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
        }
        .pill {
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }
        .pill a {
            color: inherit;
            text-decoration: none;
            display: block;
        }
        .pill b {
            font-size: 20px;
        }
        .warn {
            background: #fff;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="head">
            <div>
                <h1>Shop Dashboard</h1>
                <p class="sub">Welcome, <?= htmlspecialchars($shopName !== '' ? $shopName : 'Shop') ?></p>
            </div>
            <div>
                <a class="btn secondary" href="orders.php">View Orders</a>
                <a class="btn logout" href="../logout.php">Logout</a>
            </div>
        </div>

        <?php if ($shopKey === ''): ?>
            <div class="warn">Shop mapping not found for this account name. Use a shop login name like north, south, tiffany, shalom, heavens, or dreamland.</div>
        <?php else: ?>
            <div class="grid">
                <div class="card">
                    <div class="label">Total Orders</div>
                    <div class="value"><?= (int) $stats['total'] ?></div>
                </div>
                <div class="card">
                    <div class="label">Today's Orders</div>
                    <div class="value"><?= (int) $stats['today'] ?></div>
                </div>
                <div class="card">
                    <div class="label">Total Revenue</div>
                    <div class="value">Rs <?= number_format((float) $stats['revenue'], 2) ?></div>
                </div>
            </div>

            <div class="status-row">
                <div class="pill"><a href="orders.php?status=PLACED">Placed<br><b><?= (int) $stats['PLACED'] ?></b></a></div>
                <div class="pill"><a href="orders.php?status=PREPARING">Preparing<br><b><?= (int) $stats['PREPARING'] ?></b></a></div>
                <div class="pill"><a href="orders.php?status=READY">Ready<br><b><?= (int) $stats['READY'] ?></b></a></div>
                <div class="pill"><a href="orders.php?status=COMPLETED">Completed<br><b><?= (int) $stats['COMPLETED'] ?></b></a></div>
                <div class="pill"><a href="orders.php?status=CANCELLED">Cancelled<br><b><?= (int) $stats['CANCELLED'] ?></b></a></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
