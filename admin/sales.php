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

$sales = [];
$salesByShop = [];
$totalRevenue = 0.0;
$totalItems = 0;

$mode = (string) ($_GET['mode'] ?? 'daily');
$dateInput = (string) ($_GET['date'] ?? date('Y-m-d'));
$monthInput = (string) ($_GET['month'] ?? date('Y-m'));

$startDate = '';
$endDate = '';
if ($mode === 'monthly') {
    $startDate = $monthInput . '-01';
    $endDate = date('Y-m-d', strtotime($startDate . ' +1 month'));
} else {
    $startDate = $dateInput;
    $endDate = date('Y-m-d', strtotime($startDate . ' +1 day'));
}

$sql = "SELECT shop_label, items_json, total_amount, status, created_at 
        FROM orders 
        WHERE status <> 'CANCELLED' AND created_at >= ? AND created_at < ?
        ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = false;
}

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $shopLabel = (string) ($row['shop_label'] ?? 'Shop');
        $items = json_decode((string) ($row['items_json'] ?? ''), true);
        if (!is_array($items)) {
            continue;
        }
        foreach ($items as $item) {
            $name = (string) ($item['name'] ?? 'Item');
            $qty = (int) ($item['qty'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            if (!isset($sales[$name])) {
                $sales[$name] = ['qty' => 0, 'revenue' => 0.0];
            }
            $sales[$name]['qty'] += $qty;
            $sales[$name]['revenue'] += $price * $qty;

            if (!isset($salesByShop[$shopLabel])) {
                $salesByShop[$shopLabel] = [];
            }
            if (!isset($salesByShop[$shopLabel][$name])) {
                $salesByShop[$shopLabel][$name] = 0;
            }
            $salesByShop[$shopLabel][$name] += $qty;
            $totalItems += $qty;
        }
        $totalRevenue += (float) ($row['total_amount'] ?? 0);
    }
    mysqli_free_result($result);
}
if (!empty($stmt)) {
    mysqli_stmt_close($stmt);
}

uasort($sales, function(array $a, array $b): int {
    return ($b['qty'] ?? 0) <=> ($a['qty'] ?? 0);
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Overview - UniBites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; padding: 18px; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .top a { text-decoration: none; background: #111827; color: #fff; padding: 8px 12px; border-radius: 8px; display: inline-block; }
        .card { background: #fff; border-radius: 10px; padding: 14px; margin-top: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f3f4f6; }
        .summary { display: flex; gap: 12px; flex-wrap: wrap; }
        .summary .pill { background: #111827; color: #fff; padding: 8px 12px; border-radius: 999px; font-weight: 700; }
        .muted { color: #6b7280; font-size: 12px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <a href="dashboard.php">Back</a>
        </div>
        <h2>Sales Overview</h2>
    </div>

    <div class="card summary">
        <div class="pill">Items Sold: <?= (int) $totalItems ?></div>
        <div class="pill">Revenue: Rs <?= number_format($totalRevenue, 2) ?></div>
    </div>

    <div class="card">
        <h3>Filter</h3>
        <form method="get" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div>
                <label>Mode</label>
                <select name="mode">
                    <option value="daily" <?= $mode === 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="monthly" <?= $mode === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                </select>
            </div>
            <div>
                <label>Date (Daily)</label>
                <input type="date" name="date" value="<?= htmlspecialchars($dateInput) ?>">
            </div>
            <div>
                <label>Month (Monthly)</label>
                <input type="month" name="month" value="<?= htmlspecialchars($monthInput) ?>">
            </div>
            <button type="submit">Apply</button>
        </form>
        <div class="muted">Current range: <?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars(date('Y-m-d', strtotime($endDate . ' -1 day'))) ?></div>
    </div>

    <div class="card">
        <h3>Products Sold (All Shops)</h3>
        <?php if (empty($sales)): ?>
            <div class="muted">No sales data yet.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Revenue (Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $name => $data): ?>
                        <tr>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td><?= (int) $data['qty'] ?></td>
                            <td><?= number_format((float) $data['revenue'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Shop-wise Product Count</h3>
        <?php if (empty($salesByShop)): ?>
            <div class="muted">No shop-wise data yet.</div>
        <?php else: ?>
            <?php foreach ($salesByShop as $shopLabel => $items): ?>
                <h4><?= htmlspecialchars($shopLabel) ?></h4>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $name => $qty): ?>
                            <tr>
                                <td><?= htmlspecialchars($name) ?></td>
                                <td><?= (int) $qty ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
