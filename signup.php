<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UniBites | Signup</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="signup-body">

<div class="signup-container">

    <h2>Sign Up</h2>
    <p class="signup-sub"></p>
         <?php
            if (isset($_SESSION['error'])) {
                 echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<p style='color:green'>" . $_SESSION['success'] . "</p>";
                unset($_SESSION['success']);
            }
        ?>

    <form method="POST" action="signup_process.php">

        <input type="text" name="name" placeholder="Your First Name" required>
        <input type="text" name="last_name" placeholder="Your Last Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
           <?php if (isset($_GET['error']) && $_GET['error'] == 'password_mismatch')
               { 
            ?>
            <p style="color:red;">Passwords do not match</p>
            <?php } ?>
           
        
        <label class="terms">
            <input type="checkbox" required> 
            <span>
                I agree to the <a href="#">Terms of Use</a>
            </span> 
        </label>

        <button type="submit">Submit</button>

        <p class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </p>

    </form>

</div>

</body>
</html>
