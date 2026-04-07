<?php
session_start();
require "../config/db.php";
require "../config/schema.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'shop') {
    header("Location: ../login.php");
    exit();
}

ensure_menu_items_schema($conn);

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
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $shopKey !== '') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'update' && isset($_POST['item_id'])) {
        $itemId = (int) $_POST['item_id'];
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $image = trim((string) ($_POST['image'] ?? ''));
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $availableFrom = trim((string) ($_POST['available_from'] ?? ''));
        $availableTo = trim((string) ($_POST['available_to'] ?? ''));
        $qualityNote = trim((string) ($_POST['quality_note'] ?? ''));
        $autoReady = isset($_POST['auto_ready']) ? 1 : 0;

        if ($name === '' || $image === '') {
            $error = 'Name and image are required for each item.';
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE menu_items SET name = ?, price = ?, image = ?, is_available = ?, available_from = NULLIF(?, ''), available_to = NULLIF(?, ''), quality_note = ?, auto_ready = ? WHERE id = ? AND shop_key = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sdsisssiis", $name, $price, $image, $isAvailable, $availableFrom, $availableTo, $qualityNote, $autoReady, $itemId, $shopKey);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $message = 'Item updated successfully.';
            } else {
                $error = 'Failed to update item.';
            }
        }
    }

    if ($action === 'add') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $image = trim((string) ($_POST['image'] ?? ''));
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $availableFrom = trim((string) ($_POST['available_from'] ?? ''));
        $availableTo = trim((string) ($_POST['available_to'] ?? ''));
        $qualityNote = trim((string) ($_POST['quality_note'] ?? ''));
        $autoReady = isset($_POST['auto_ready']) ? 1 : 0;

        if ($name === '' || $image === '') {
            $error = 'Name and image are required for a new item.';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO menu_items (shop_key, name, price, image, is_available, available_from, available_to, quality_note, auto_ready) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), NULLIF(?, ''), ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssdsisssi", $shopKey, $name, $price, $image, $isAvailable, $availableFrom, $availableTo, $qualityNote, $autoReady);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $message = 'New item added.';
            } else {
                $error = 'Failed to add item.';
            }
        }
    }
}

$items = [];
if ($shopKey !== '') {
    $stmt = mysqli_prepare($conn, "SELECT id, name, price, image, is_available, available_from, available_to, quality_note, auto_ready FROM menu_items WHERE shop_key = ? ORDER BY id ASC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $shopKey);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu - UniBites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; padding: 18px; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .top a { text-decoration: none; background: #111827; color: #fff; padding: 8px 12px; border-radius: 8px; display: inline-block; }
        .card { background: #fff; border-radius: 10px; padding: 14px; margin-top: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 8px; }
        label { font-size: 12px; color: #374151; display: block; margin-bottom: 4px; }
        input[type="text"], input[type="number"], input[type="time"] { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 8px; }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        button { background: #16a34a; color: #fff; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; }
        .muted { color: #6b7280; font-size: 12px; }
        .msg { margin-top: 12px; padding: 10px; border-radius: 8px; }
        .ok { background: #dcfce7; color: #166534; }
        .err { background: #fee2e2; color: #991b1b; }
        .section-title { font-size: 18px; margin: 18px 0 8px; }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <a href="dashboard.php">Back</a>
        </div>
        <h2>Manage Menu</h2>
    </div>

    <?php if ($shopKey === ''): ?>
        <div class="card">Shop mapping not found for this account.</div>
    <?php else: ?>
        <div class="card">
            <div class="section-title">Add New Item</div>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="row">
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div>
                        <label>Price</label>
                        <input type="number" name="price" min="0" step="0.01" required>
                    </div>
                    <div>
                        <label>Image Path</label>
                        <input type="text" name="image" placeholder="assets/food/example.jpg" required>
                    </div>
                    <div>
                        <label>Quality Note</label>
                        <input type="text" name="quality_note" placeholder="Fresh / Hot / Limited">
                    </div>
                    <div>
                        <label>Available From</label>
                        <input type="time" name="available_from">
                    </div>
                    <div>
                        <label>Available To</label>
                        <input type="time" name="available_to">
                    </div>
                </div>
                <div class="actions" style="margin-top:10px;">
                    <label><input type="checkbox" name="is_available" checked> Available Now</label>
                    <label><input type="checkbox" name="auto_ready" checked> Auto-Ready (No Manual Update)</label>
                    <button type="submit">Add Item</button>
                </div>
            </form>
        </div>

        <?php if ($message !== ''): ?><div class="msg ok"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="section-title">Edit Items</div>
        <?php if (empty($items)): ?>
            <div class="card">No items found for this shop yet.</div>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="card">
                    <form method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                        <div class="row">
                            <div>
                                <label>Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars((string) $item['name']) ?>" required>
                            </div>
                            <div>
                                <label>Price</label>
                                <input type="number" name="price" min="0" step="0.01" value="<?= htmlspecialchars((string) $item['price']) ?>" required>
                            </div>
                            <div>
                                <label>Image Path</label>
                                <input type="text" name="image" value="<?= htmlspecialchars((string) $item['image']) ?>" required>
                            </div>
                            <div>
                                <label>Quality Note</label>
                                <input type="text" name="quality_note" value="<?= htmlspecialchars((string) ($item['quality_note'] ?? '')) ?>">
                            </div>
                            <div>
                                <label>Available From</label>
                                <input type="time" name="available_from" value="<?= htmlspecialchars((string) ($item['available_from'] ?? '')) ?>">
                            </div>
                            <div>
                                <label>Available To</label>
                                <input type="time" name="available_to" value="<?= htmlspecialchars((string) ($item['available_to'] ?? '')) ?>">
                            </div>
                        </div>
                        <div class="actions" style="margin-top:10px;">
                            <label><input type="checkbox" name="is_available" <?= ((int) ($item['is_available'] ?? 0)) === 1 ? 'checked' : '' ?>> Available Now</label>
                            <label><input type="checkbox" name="auto_ready" <?= ((int) ($item['auto_ready'] ?? 0)) === 1 ? 'checked' : '' ?>> Auto-Ready (No Manual Update)</label>
                            <button type="submit">Save Changes</button>
                        </div>
                        <div class="muted">Auto-ready items will be marked ready immediately after payment.</div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
