<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Offers</title>
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
            padding: 16px;
            margin-bottom: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        }
        .title {
            font-weight: 700;
            margin-bottom: 6px;
        }
        .muted {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="top">
        <a href="dashboard.php">Back</a>
    </div>
    <h2>Offers</h2>
    <div class="card">
        <div class="title">Welcome Offer</div>
        <div class="muted">Get 10% off on your first order above Rs 100.</div>
    </div>
    <div class="card">
        <div class="title">Combo Deal</div>
        <div class="muted">Buy 2 beverages and get Rs 10 off.</div>
    </div>
</body>
</html>
