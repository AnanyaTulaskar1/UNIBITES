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
</head>
<body>

<h1>Welcome Admin 👋</h1>
<p>You are successfully logged in.</p>

<a href="sales.php">View Sales Overview</a>
<br><br>
<a href="orders.php">View All Orders</a>
<br><br>
<a href="users.php">View All Users</a>
<br><br>
<a href="shops.php">View Shop Accounts</a>
<br><br>
<a href="../logout.php">Logout</a>

</body>
</html>
