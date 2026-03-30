<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$name = (string) ($_SESSION['name'] ?? '');
$email = '';

$stmt = mysqli_prepare($conn, "SELECT name, email FROM users WHERE id = ? LIMIT 1");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $name = (string) ($row['name'] ?? $name);
        $email = (string) ($row['email'] ?? '');
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
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
            margin-bottom: 16px;
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
        .title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .card {
            background: #fff4e8;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        }
        .row {
            margin: 6px 0;
            color: #374151;
        }
        .logout {
            display: inline-block;
            margin-top: 14px;
            text-decoration: none;
            background: #dc2626;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a class="back-link" href="dashboard.php">Back</a>
        <div class="title">Account</div>
        <span></span>
    </div>
    <div class="card">
        <div class="row"><b>Name:</b> <?= htmlspecialchars($name) ?></div>
        <div class="row"><b>Email:</b> <?= htmlspecialchars($email) ?></div>
        <div class="row"><b>Role:</b> User</div>
        <a class="logout" href="../logout.php">Logout</a>
    </div>
</body>
</html>
