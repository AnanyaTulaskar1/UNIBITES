<?php
session_start();
require "config/db.php";

$loginId  = trim($_POST['login_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($loginId === '' || $password === '') {
    $_SESSION['error'] = "Please enter your login details";
    header("Location: login.php");
    exit();
}

// First try email login (all roles)
$sql = "SELECT id, name, password, role, status FROM users WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $loginId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = false;
}

// If not found by email, try shop id (name) for shop role
if (!$result || mysqli_num_rows($result) === 0) {
    if ($stmt) {
        mysqli_stmt_close($stmt);
    }
    $sqlShop = "SELECT id, name, password, role, status FROM users WHERE role = 'shop' AND name = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sqlShop);
    if (!$stmt) {
        die("Query prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $loginId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

// Check user exists
if (mysqli_num_rows($result) === 1) {

    $user = mysqli_fetch_assoc($result);

    if (($user['status'] ?? 'approved') !== 'approved') {
        $_SESSION['error'] = "Account not approved";
        header("Location: login.php");
        exit();
    }

    // Verify password
    if (password_verify($password, $user['password'])) {

        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['role'] === 'shop') {
            $_SESSION['shop_name'] = $user['name'];
            header("Location: shop/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();

    } else {
        $_SESSION['error'] = "Incorrect password";
    }

} else {
    $_SESSION['error'] = "Account not found";
}

header("Location: login.php");
exit();



?>
