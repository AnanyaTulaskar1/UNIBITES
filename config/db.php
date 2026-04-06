<?php
$conn = mysqli_connect("10.158.185.197","unibites_user", "StrongPassword123", "unibites", 3307);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
