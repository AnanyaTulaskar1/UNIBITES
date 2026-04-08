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

.payment-note {
    margin-top: 8px;
    font-size: 12px;
    color: #6b7280;
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

.qr-box {
    margin-top: 10px;
    display: inline-block;
    background: #f9fafb;
    border: 1px dashed #d1d5db;
    padding: 10px;
    border-radius: 10px;
}

.qr-box img {
    width: 140px;
    height: 140px;
    display: block;
}

.stepper {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin: 10px 0;
}

.step {
    background: #f3f4f6;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
}

.step.active {
    background: #16a34a;
    color: #fff;
}

.upi-apps {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.upi-apps button {
    text-decoration: none;
    background: #111827;
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 600;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.payment-methods {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.method-btn {
    border: none;
    background: #111827;
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    opacity: 0.6;
}

.method-btn.active {
    opacity: 1;
    background: #16a34a;
}

.nav-row form {
    margin: 0;
}

.nav-row button:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.hidden {
    display: none;
}

.pin-row {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}

.pin-input {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    text-align: center;
    font-size: 18px;
    font-weight: 700;
}

.pin-section {
    margin-top: 8px;
}

.overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999;
}

.overlay-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    width: min(360px, 90vw);
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.spinner {
    width: 36px;
    height: 36px;
    border: 4px solid #e5e7eb;
    border-top-color: #16a34a;
    border-radius: 50%;
    margin: 10px auto 6px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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

    <?php
        $upiVpa = 'unibites@upi';
        $upiName = 'UniBites';
        $upiAmount = number_format($total, 2, '.', '');
        $upiNote = 'UniBites Order';
        $upiUrl = 'upi://pay?pa=' . urlencode($upiVpa)
            . '&pn=' . urlencode($upiName)
            . '&am=' . urlencode($upiAmount)
            . '&cu=INR'
            . '&tn=' . urlencode($upiNote);
    ?>
    <form id="paymentForm" action="place_order.php" method="post">
        <input type="hidden" id="payment_method" name="payment_method" value="UPI">
        <input type="hidden" id="payment_ref" name="payment_ref" value="">

        <div id="paymentFlow" class="hidden">
            <div class="payment-card">
                <h4>Payment Method</h4>
                <div class="payment-note">Do not enter real card or UPI details.</div>
                <div class="payment-methods">
                    <button type="button" class="method-btn active" data-method="UPI">UPI</button>
                    <button type="button" class="method-btn" data-method="CARD">Card</button>
                </div>
            </div>

            <div id="upiCard" class="payment-card">
                <h4>UPI</h4>
                <div>Pay to UPI ID: <span class="upi-id">unibites@upi</span></div>

                <div class="stepper">
                    <div class="step active">1. Proceed</div>
                    <div class="step">2. Pay in App</div>
                    <div class="step">3. Receipt</div>
                </div>

                <div id="upiOptions" class="hidden">
                    <label for="payer_upi">Your UPI ID</label>
                    <input id="payer_upi" type="text" maxlength="80" placeholder="example@okaxis">

                    <div class="upi-apps">
                        <button type="button" data-upi="gpay">Google Pay</button>
                        <button type="button" data-upi="phonepe">PhonePe</button>
                        <button type="button" data-upi="paytm">Paytm</button>
                        <button type="button" data-upi="other">Other UPI</button>
                    </div>

                    <div class="qr-box">
                    <img alt="UPI QR" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='140' height='140'><rect width='100%25' height='100%25' fill='%23ffffff'/><rect x='8' y='8' width='34' height='34' fill='%23111827'/><rect x='98' y='8' width='34' height='34' fill='%23111827'/><rect x='8' y='98' width='34' height='34' fill='%23111827'/><rect x='52' y='52' width='36' height='36' fill='%23111827'/><text x='70' y='78' font-size='10' text-anchor='middle' fill='%23ffffff'>UPI</text></svg>">
                    </div>

                    <div id="pinSection" class="pin-section hidden">
                        <div class="payment-note">Enter UPI PIN (any 4 digits)</div>
                        <div class="pin-row">
                            <input class="pin-input" type="password" inputmode="numeric" maxlength="1" aria-label="PIN digit 1">
                            <input class="pin-input" type="password" inputmode="numeric" maxlength="1" aria-label="PIN digit 2">
                            <input class="pin-input" type="password" inputmode="numeric" maxlength="1" aria-label="PIN digit 3">
                            <input class="pin-input" type="password" inputmode="numeric" maxlength="1" aria-label="PIN digit 4">
                        </div>
                        <div class="payment-note" id="pinNote" style="color:#dc2626; display:none;">Enter 4 digits to continue.</div>
                        <div style="margin-top:8px;">
                            <button id="payNowBtn" type="button">Pay Now</button>
                        </div>
                    </div>

                    <label for="upi_ref">UPI Transaction ID (optional)</label>
                    <input id="upi_ref" type="text" maxlength="60" placeholder="Example: 1234567890">
                </div>
            </div>

            <div id="cardOptions" class="payment-card hidden">
                <h4>Card Payment</h4>
                <label for="card_name">Cardholder Name</label>
                <input id="card_name" type="text" maxlength="40" placeholder="Name on card">

                <label for="card_number">Card Number</label>
                <input id="card_number" type="text" inputmode="numeric" maxlength="19" placeholder="1234 5678 9012 3456">

                <label for="card_expiry">Expiry (MM/YY)</label>
                <input id="card_expiry" type="text" inputmode="numeric" maxlength="5" placeholder="08/29">

                <label for="card_cvv">CVV (3/4 digits)</label>
                <input id="card_cvv" type="password" inputmode="numeric" maxlength="4" placeholder="123">

                <label for="card_pin">Debit Card PIN (4 digits)</label>
                <input id="card_pin" type="password" inputmode="numeric" maxlength="4" placeholder="0000">

                <div class="payment-note" id="cardNote" style="color:#dc2626; display:none;">Please fill all card fields correctly.</div>
                <div style="margin-top:8px;">
                    <button id="cardPayNowBtn" type="button">Pay Now</button>
                </div>
            </div>
        </div>

        <div class="pay-bar">
            <div><?= $count ?> Item(s) | Total Bill: Rs <?= $total ?></div>
            <button id="proceedBtn" type="button">Proceed to Pay Rs <?= $total ?></button>
        </div>
    </form>
<?php endif; ?>

    <div id="overlay" class="overlay">
        <div class="overlay-card">
            <div class="spinner"></div>
            <div id="overlayText">Redirecting to UPI app...</div>
            <div class="payment-note">No real verification</div>
        </div>
    </div>

    <script>
    (function() {
        var proceedBtn = document.getElementById('proceedBtn');
        var paymentFlow = document.getElementById('paymentFlow');
        var upiOptions = document.getElementById('upiOptions');
        var overlay = document.getElementById('overlay');
        var overlayText = document.getElementById('overlayText');
        var form = document.getElementById('paymentForm');
        var paymentMethodInput = document.getElementById('payment_method');
        var paymentRefInput = document.getElementById('payment_ref');
        var upiRefInput = document.getElementById('upi_ref');
        var pinSection = document.getElementById('pinSection');
        var payNowBtn = document.getElementById('payNowBtn');
        var pinNote = document.getElementById('pinNote');
        var pinInputs = pinSection ? pinSection.querySelectorAll('.pin-input') : [];
        var methodButtons = document.querySelectorAll('[data-method]');
        var upiCard = document.getElementById('upiCard');
        var cardOptions = document.getElementById('cardOptions');
        var cardPayNowBtn = document.getElementById('cardPayNowBtn');
        var cardNumber = document.getElementById('card_number');
        var cardExpiry = document.getElementById('card_expiry');
        var cardCvv = document.getElementById('card_cvv');
        var cardPin = document.getElementById('card_pin');
        var cardNote = document.getElementById('cardNote');

        if (proceedBtn && paymentFlow) {
            proceedBtn.addEventListener('click', function() {
                paymentFlow.classList.remove('hidden');
                upiOptions.classList.remove('hidden');
                proceedBtn.textContent = 'Choose Payment Method';
            });
        }

        function setMethod(method) {
            if (paymentMethodInput) {
                paymentMethodInput.value = method;
            }
            methodButtons.forEach(function(btn) {
                btn.classList.toggle('active', btn.getAttribute('data-method') === method);
            });
            if (method === 'UPI') {
                if (upiCard) upiCard.classList.remove('hidden');
                if (cardOptions) cardOptions.classList.add('hidden');
            } else {
                if (upiCard) upiCard.classList.add('hidden');
                if (cardOptions) cardOptions.classList.remove('hidden');
            }
        }

        methodButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                setMethod(btn.getAttribute('data-method'));
            });
        });

        document.querySelectorAll('[data-upi]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                overlayText.textContent = 'Redirecting to UPI app...';
                overlay.style.display = 'flex';
                setTimeout(function() {
                    overlay.style.display = 'none';
                    if (pinSection) {
                        pinSection.classList.remove('hidden');
                    }
                    if (pinInputs.length) {
                        pinInputs[0].focus();
                    }
                }, 1200);
            });
        });

        function pinFilled() {
            var count = 0;
            pinInputs.forEach(function(input) {
                if (input.value.trim().length === 1) count += 1;
            });
            return count === 4;
        }

        pinInputs.forEach(function(input, idx) {
            input.addEventListener('input', function() {
                var v = input.value.replace(/[^0-9]/g, '');
                input.value = v.substring(0, 1);
                if (v && idx < pinInputs.length - 1) {
                    pinInputs[idx + 1].focus();
                }
                if (pinNote) {
                    pinNote.style.display = pinFilled() ? 'none' : 'block';
                }
            });
        });

        if (payNowBtn) {
            payNowBtn.addEventListener('click', function() {
                if (!pinFilled()) {
                    if (pinNote) pinNote.style.display = 'block';
                    return;
                }
                if (paymentMethodInput) {
                    paymentMethodInput.value = 'UPI';
                }
                if (paymentRefInput) {
                    var ref = upiRefInput ? upiRefInput.value.trim() : '';
                    paymentRefInput.value = ref !== '' ? ref : ('UPI-' + Date.now());
                }
                overlayText.textContent = 'Processing payment...';
                overlay.style.display = 'flex';
                setTimeout(function() {
                    form.submit();
                }, 1500);
            });
        }

        if (cardNumber) {
            cardNumber.addEventListener('input', function() {
                var digits = cardNumber.value.replace(/[^0-9]/g, '').substring(0, 16);
                var parts = digits.match(/.{1,4}/g);
                cardNumber.value = parts ? parts.join(' ') : '';
            });
        }

        if (cardExpiry) {
            cardExpiry.addEventListener('input', function() {
                var digits = cardExpiry.value.replace(/[^0-9]/g, '').substring(0, 4);
                if (digits.length >= 3) {
                    cardExpiry.value = digits.substring(0, 2) + '/' + digits.substring(2);
                } else {
                    cardExpiry.value = digits;
                }
            });
        }

        function cardValid() {
            var number = cardNumber ? cardNumber.value.replace(/\s+/g, '') : '';
            var expiry = cardExpiry ? cardExpiry.value.trim() : '';
            var cvv = cardCvv ? cardCvv.value.trim() : '';
            var pin = cardPin ? cardPin.value.trim() : '';
            var expiryOk = /^[0-1][0-9]\/[0-9]{2}$/.test(expiry) && expiry.substring(0, 2) !== '00';
            return number.length === 16 && expiryOk && (cvv.length === 3 || cvv.length === 4) && pin.length === 4;
        }

        if (cardPayNowBtn) {
            cardPayNowBtn.addEventListener('click', function() {
                if (!cardValid()) {
                    if (cardNote) cardNote.style.display = 'block';
                    return;
                }
                if (cardNote) cardNote.style.display = 'none';
                if (paymentMethodInput) {
                    paymentMethodInput.value = 'CARD';
                }
                if (paymentRefInput) {
                    paymentRefInput.value = 'CARD-' + Date.now();
                }
                overlayText.textContent = 'Processing card payment...';
                overlay.style.display = 'flex';
                setTimeout(function() {
                    form.submit();
                }, 1500);
            });
        }
    })();
</script>

</body>
</html>
</html>
