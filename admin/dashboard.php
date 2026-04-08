<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --admin-bg: #f4f6f8;
            --admin-card: #ffffff;
            --admin-accent: #e09d44;
            --admin-accent-2: #924b22;
            --admin-text: #111827;
            --admin-muted: #6b7280;
            --admin-border: #e5e7eb;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: radial-gradient(circle at 10% 10%, rgba(224,157,68,0.15), transparent 45%),
                        radial-gradient(circle at 90% 20%, rgba(146,75,34,0.15), transparent 45%),
                        var(--admin-bg);
            color: var(--admin-text);
            min-height: 100vh;
        }
        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }
        .topbar {
            background: linear-gradient(90deg, #924b22, #e09d44);
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.35);
        }
        .title h1 {
            margin: 0 0 6px;
            font-size: 26px;
            letter-spacing: 0.3px;
        }
        .title p {
            margin: 0;
            color: rgba(255,255,255,0.85);
            font-size: 14px;
        }
        .badge {
            background: rgba(255,255,255,0.2);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.35);
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-top: 18px;
        }
        .card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            padding: 16px;
            text-decoration: none;
            color: var(--admin-text);
            box-shadow: 0 10px 22px rgba(0,0,0,0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(0,0,0,0.35);
            border-color: rgba(224,157,68,0.6);
        }
        .card h3 {
            margin: 0 0 6px;
            font-size: 16px;
        }
        .card p {
            margin: 0;
            color: var(--admin-muted);
            font-size: 13px;
        }
        .logout {
            margin-top: 18px;
            display: inline-block;
            background: #ef4444;
            color: #fff;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(0,0,0,0.25);
        }
        .logout:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>

<div class="wrap">
    <div class="topbar">
        <div class="title">
            <h1>Admin Dashboard</h1>
            <p>Manage sales, orders, users, and shops from one place.</p>
        </div>
        <div class="badge">ADMIN ACCESS</div>
    </div>

    <div class="grid">
        <a class="card" href="sales.php">
            <h3>Sales Overview</h3>
            <p>Track revenue, trends, and totals.</p>
        </a>
        <a class="card" href="orders.php">
            <h3>All Orders</h3>
            <p>Review and monitor every order.</p>
        </a>
        <a class="card" href="users.php">
            <h3>Users</h3>
            <p>Manage and verify user accounts.</p>
        </a>
        <a class="card" href="shops.php">
            <h3>Shop Accounts</h3>
            <p>Manage canteen and shop access.</p>
        </a>
    </div>

    <a class="logout" href="../logout.php">Logout</a>
</div>

</body>
</html>
