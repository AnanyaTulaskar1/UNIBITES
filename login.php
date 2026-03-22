<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UniBites | Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">

    <!-- LEFT IMAGE SECTION -->
    <div class="login-left">
        <img src="assets/canteen.jpeg" alt="UniBites">
        
    </div>

    <!-- RIGHT LOGIN FORM -->
    <div class="login-right">
        <div class="overlay-text">
            <h2>UniBites</h2>
            <p> A Smart Canteen </p>
        </div>
        <h1>Login</h1>
        <p class="sub-text">Welcome back! Please login</p>
               
        <?php
            if (isset($_SESSION['error'])) {
                 echo"<p class='error-msg'>".$_SESSION['error']."</p>";
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['success'])) {
                echo "<p class='success-msg'>".$_SESSION['success']."</p>";
                unset($_SESSION['success']);
            }
        ?>

        
        
        <form method="POST" action="login_process.php" >
            <input type="text" name="login_id" placeholder="Email or Shop ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>


        <p class="footer-text">
            © 2026 UniBites
        </p>
    </div>

</div>

</body>
</html>
