<?php
session_start();
require "config/db.php";

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($token === '') {
        $_SESSION['error'] = "Invalid or missing token";
        header("Location: forgot_password.php");
        exit();
    }

    if ($password === '' || $confirm === '') {
        $_SESSION['error'] = "Please enter and confirm your new password";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    $stmt = mysqli_prepare($conn, "SELECT user_id, expires_at FROM user_tokens WHERE token = ? LIMIT 1");
    if (!$stmt) {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) !== 1) {
        $_SESSION['error'] = "Reset link is invalid or expired";
        header("Location: forgot_password.php");
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    $userId = (int) $row['user_id'];
    $expiresAt = $row['expires_at'];

    if (strtotime($expiresAt) < time()) {
        $del = mysqli_prepare($conn, "DELETE FROM user_tokens WHERE token = ?");
        if ($del) {
            mysqli_stmt_bind_param($del, "s", $token);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);
        }
        $_SESSION['error'] = "Reset link is expired";
        header("Location: forgot_password.php");
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $upd = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
    if ($upd) {
        mysqli_stmt_bind_param($upd, "si", $hashed, $userId);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);
    }

    $delAll = mysqli_prepare($conn, "DELETE FROM user_tokens WHERE user_id = ?");
    if ($delAll) {
        mysqli_stmt_bind_param($delAll, "i", $userId);
        mysqli_stmt_execute($delAll);
        mysqli_stmt_close($delAll);
    }

    $_SESSION['success'] = "Password updated successfully. Please login.";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UniBites | Reset Password</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="signup-body">

<div class="signup-container">
    <h2>Reset Password</h2>
    <p class="signup-sub">Enter a new password</p>

    <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error-msg'>".$_SESSION['error']."</p>";
            unset($_SESSION['error']);
        }

        if ($token === '') {
            echo "<p class='error-msg'>Missing reset token. Please request a new link.</p>";
        }
    ?>

    <?php if ($token !== ''): ?>
    <form method="POST" action="reset_password.php">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Update Password</button>
    </form>
    <?php endif; ?>

    <p class="login-link">
        <a href="forgot_password.php">Back to Forgot Password</a>
    </p>
</div>

</body>
</html>
