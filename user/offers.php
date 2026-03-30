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
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #f4f6f8 0%, #e8f4f8 100%);
            padding: 16px;
            color: #111827;
        }
        .topbar {
            background: linear-gradient(90deg, #924b22, #e09d44);
            color: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        .back-link {
            text-decoration: none;
            background: rgba(17, 24, 39, 0.9);
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
        }
        .card {
            background: #fff4e8;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 10px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        }
        .title {
            font-weight: 700;
            margin-bottom: 6px;
        }
        .muted {
            color: #4b5563;
        }
        .page-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a class="back-link" href="dashboard.php">Back</a>
        <div class="page-title">Offers</div>
        <span></span>
    </div>
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
