<?php
session_start();
require "config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginId = trim($_POST['email'] ?? '');

    if ($loginId === '') {
        $_SESSION['error'] = "Please enter your email or shop ID";
        header("Location: forgot_password.php");
        exit();
    }

    // Try email first (all roles)
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $loginId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If not found by email, try shop name (shop ID)
    if (!$result || mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE role = 'shop' AND name = ? LIMIT 1");
        if (!$stmt) {
            $_SESSION['error'] = "Something went wrong. Please try again.";
            header("Location: forgot_password.php");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $loginId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $userId = (int) $row['id'];
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $del = mysqli_prepare($conn, "DELETE FROM user_tokens WHERE user_id = ?");
        if ($del) {
            mysqli_stmt_bind_param($del, "i", $userId);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);
        }

        $ins = mysqli_prepare($conn, "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        if ($ins) {
            mysqli_stmt_bind_param($ins, "iss", $userId, $token, $expiresAt);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        if ($basePath === '') {
            $basePath = '';
        }
        $resetLink = $scheme . '://' . $host . $basePath . "/reset_password.php?token=" . $token;
        $_SESSION['success'] = "Reset link generated (valid 1 hour).";
        $_SESSION['reset_link'] = $resetLink;
        header("Location: forgot_password.php");
        exit();
    }

    $_SESSION['error'] = "Account not found";
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UniBites | Forgot Password</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="signup-body">

<div class="signup-container">
    <h2>Forgot Password</h2>
    <p class="signup-sub">Enter your email to reset your password</p>

    <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error-msg'>".$_SESSION['error']."</p>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "<p class='success-msg'>".$_SESSION['success']."</p>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['reset_link'])) {
            $link = htmlspecialchars($_SESSION['reset_link']);
            echo "<p class='success-msg'><a href=\"".$link."\">Click here to reset your password</a></p>";
            unset($_SESSION['reset_link']);
        }
    ?>

    <form method="POST" action="forgot_password.php">
        <input type="text" name="email" placeholder="Email or Shop ID" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <p class="login-link">
        <a href="login.php">Back to Login</a>
    </p>
</div>

</body>
</html>
