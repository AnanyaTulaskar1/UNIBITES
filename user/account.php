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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
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
    <div class="top">
        <a href="dashboard.php">Back</a>
    </div>
    <h2>Account</h2>
    <div class="card">
        <div class="row"><b>Name:</b> <?= htmlspecialchars($name) ?></div>
        <div class="row"><b>Email:</b> <?= htmlspecialchars($email) ?></div>
        <div class="row"><b>Role:</b> User</div>
        <a class="logout" href="../logout.php">Logout</a>
    </div>
</body>
</html>
