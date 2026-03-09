<?php
session_start();
require "config/db.php";

$email    = $_POST['email'];
$password = $_POST['password'];

// Prepare query
$sql = "SELECT id, name, password, role FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

// Check user exists
if (mysqli_num_rows($result) === 1) {

    $user = mysqli_fetch_assoc($result);

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
    $_SESSION['error'] = "Email not registered";
}

header("Location: login.php");
exit();



?>
