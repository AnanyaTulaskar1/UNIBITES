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
:root {
    
    --brand-watermark: url('../assets/image/your-logo.jpeg');
}
        body {
            margin: 0;
            font-family: "Segoe UI", "Poppins", Arial, sans-serif;
            background: linear-gradient(135deg, #f4f6f8 0%, #e8f4f8 100%);
            color: #111827;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 10% 20%, rgba(224, 157, 68, 0.18), transparent 45%),
                        radial-gradient(circle at 90% 10%, rgba(146, 75, 34, 0.18), transparent 45%);
            pointer-events: none;
            z-index: -1;
        }
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background-image: var(--brand-watermark);
            background-repeat: no-repeat;
            background-position: center;
            background-size: 520px;
            opacity: 0.2;
            pointer-events: none;
            z-index: -1;
        }
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px 18px 28px;
        }
        .topbar {
            background: linear-gradient(90deg, #924b22, #e09d44);
            color: #fff;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }
        .brand h1 {
            margin: 0 0 4px;
            font-size: 26px;
            letter-spacing: 0.2px;
        }
        .sub {
            margin: 0;
            color: rgba(255,255,255,0.85);
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #111827;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            margin-left: 6px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn.secondary {
            background: rgba(17, 24, 39, 0.9);
        }
        .btn.logout {
            background: #dc2626;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 12px 22px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, #924b22, #e09d44);
        }
        .stat-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #fff2e7;
            color: #924b22;
            font-size: 18px;
        }
        .label {
            color: #4b5563;
            font-size: 13px;
        }
        .value {
            font-size: 26px;
            font-weight: 700;
        }
        .section-title {
            font-size: 18px;
            margin: 6px 0 10px;
            color: #1f2937;
        }
        .status-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
        }
        .pill {
            border-radius: 14px;
            padding: 12px;
            color: #111827;
            box-shadow: 0 10px 18px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .pill a {
            color: inherit;
            text-decoration: none;
            display: block;
        }
        .pill b {
            font-size: 20px;
        }
        .pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(0,0,0,0.12);
        }
        .pill.placed { background: #fff3e8; }
        .pill.preparing { background: #fff7d6; }
        .pill.ready { background: #e9f8ef; }
        .pill.completed { background: #e8f1ff; }
        .pill.cancelled { background: #fee2e2; }
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
        <div class="topbar">
            <div class="brand">
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
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">📦</div>
                        <div class="label">Total Orders</div>
                    </div>
                    <div class="value"><?= (int) $stats['total'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">📅</div>
                        <div class="label">Today's Orders</div>
                    </div>
                    <div class="value"><?= (int) $stats['today'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">💰</div>
                        <div class="label">Total Revenue</div>
                    </div>
                    <div class="value">Rs <?= number_format((float) $stats['revenue'], 2) ?></div>
                </div>
            </div>

            <div class="section-title">Order Status</div>
            <div class="status-row">
                <div class="pill placed"><a href="orders.php?status=PLACED">Placed<br><b><?= (int) $stats['PLACED'] ?></b></a></div>
                <div class="pill preparing"><a href="orders.php?status=PREPARING">Preparing<br><b><?= (int) $stats['PREPARING'] ?></b></a></div>
                <div class="pill ready"><a href="orders.php?status=READY">Ready<br><b><?= (int) $stats['READY'] ?></b></a></div>
                <div class="pill completed"><a href="orders.php?status=COMPLETED">Completed<br><b><?= (int) $stats['COMPLETED'] ?></b></a></div>
                <div class="pill cancelled"><a href="orders.php?status=CANCELLED">Cancelled<br><b><?= (int) $stats['CANCELLED'] ?></b></a></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
