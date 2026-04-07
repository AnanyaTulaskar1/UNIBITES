<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_cart') {
    $_SESSION['cart'] = [];
    unset($_SESSION['cart_shop']);
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$activeShop = $_SESSION['cart_shop'] ?? '';
$total = 0;
$count = 0;

foreach ($cart as $item) {
    $total += ((int) $item['price']) * ((int) $item['qty']);
    $count += (int) $item['qty'];
}

$shopBackLinks = [
    // Humanities canteen shops
    'tiffany' => 'humanities_shop.php',
    'zamorin_humanities' => 'humanities_shop.php',
    // Admin canteen shops
    'north' => 'admin_shop.php',
    'south' => 'admin_shop.php',
    'zamorin' => 'admin_shop.php',
    // PG canteen shops
    'shalom' => 'pg_shop.php',
    'heavens' => 'pg_shop.php',
    'dreamland' => 'pg_shop.php',
];

$menuBackLink = $shopBackLinks[$activeShop] ?? ($activeShop !== '' ? 'menu.php?shop=' . urlencode($activeShop) : 'dashboard.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>Your Cart</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    padding: 15px 15px 110px;
}

h2 {
    margin: 0 0 14px;
}

.nav-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
}

.nav-row a,
.nav-row button {
    border: none;
    background: #111827;
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.nav-row .clear-btn {
    background: #dc2626;
}

.item {
    background: #fff;
    margin-bottom: 10px;
    padding: 14px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
}

.item-name {
    font-weight: 700;
    margin-bottom: 4px;
}

.item-price {
    color: #374151;
}

.empty {
    background: #fff;
    padding: 14px;
    border-radius: 10px;
}

.pay-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    background: #16a34a;
    color: #fff;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.pay-bar button {
    border: none;
    background: #fff;
    color: #16a34a;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}

.summary-box {
    background: #fff;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 10px;
    font-weight: 700;
}

.payment-card {
    background: #fff;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
}

.payment-card h4 {
    margin: 0 0 8px;
}

.payment-card label {
    display: block;
    font-size: 13px;
    color: #6b7280;
    margin-top: 8px;
}

.payment-card input {
    width: 100%;
    padding: 8px 10px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    margin-top: 4px;
}

.payment-card .upi-id {
    font-weight: 700;
    color: #111827;
}

.nav-row form {
    margin: 0;
}

.nav-row button:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}
</style>
</head>
<body>

<h2>Your Cart</h2>

<div class="nav-row">
    <a href="<?= htmlspecialchars($menuBackLink) ?>">Back</a>
    <a href="humanities_shop.php">Humanities</a>
    <a href="admin_shop.php">Admin</a>
    <a href="pg_shop.php">PG</a>
    <form method="post" style="display:inline;">
        <input type="hidden" name="action" value="clear_cart">
        <button type="submit" class="clear-btn" <?= empty($cart) ? 'disabled' : '' ?>>Clear Cart</button>
    </form>
</div>

<?php if (empty($cart)): ?>
<div class="empty">Your cart is empty.</div>
<?php else: ?>
<div class="summary-box">Total Bill: Rs <?= $total ?></div>
<?php foreach ($cart as $item):
    $price = (int) $item['price'];
    $qty = (int) $item['qty'];
    $subtotal = $price * $qty;
?>
    <div class="item">
        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
        <div class="item-price">Rs <?= $price ?> x <?= $qty ?> = Rs <?= $subtotal ?></div>
    </div>
<?php endforeach; ?>

    <form action="place_order.php" method="post">
        <div class="payment-card">
            <h4>Payment Method: UPI</h4>
            <div>Pay to UPI ID: <span class="upi-id">unibites@upi</span></div>
            <label for="payment_ref">UPI Transaction ID (optional)</label>
            <input id="payment_ref" name="payment_ref" type="text" maxlength="60" placeholder="Example: 1234567890">
            <input type="hidden" name="payment_method" value="UPI">
        </div>

        <div class="pay-bar">
            <div><?= $count ?> Item(s) | Total Bill: Rs <?= $total ?></div>
            <button type="submit">Pay via UPI Rs <?= $total ?></button>
        </div>
    </form>
<?php endif; ?>

</body>
</html>
