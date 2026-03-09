<?php
session_start();
require "config/db.php";

$name       = $_POST['name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$confirm    = $_POST['confirm_password'];

// 1️⃣ Password check
if ($password !== $confirm) {
    $_SESSION['error'] = "Passwords do not match";
    header("Location: signup.php");
    exit();
}

// 2️⃣ Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 3️⃣ Default role (IMPORTANT)
$role = 'user';

// 4️⃣ Correct SQL
$sql = "INSERT INTO users (name, last_name, email, password, role)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $name, $last_name, $email, $hashed, $role);

// 5️⃣ Execute
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Account created successfully! Please login.";
    header("Location: login.php");
} else {
    $_SESSION['error'] = "Email already exists";
    header("Location: signup.php");
}
exit();
