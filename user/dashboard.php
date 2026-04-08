<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
$user_name = $_SESSION['name'];
$userId = (int) $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
<title>UniBites</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #f4f6f8 0%, #e8f4f8 100%);
    height: 100vh;
    overflow: hidden;  
}
/* Top Bar */
.topbar {
    background: linear-gradient(90deg, #924b22, #e09d44);
    padding: 10px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.logo {
    font-size: 28px;
    font-weight: bold;
}
.location {
    font-size: 13px;
    opacity: 0.9;
}
/* Icons row */
.icons {
   display: flex;
    gap: 20px; /* Space between icons */
    align-items: center;
    padding: 4px 1px;
    background: #140f02;
}
.icon-box {
    text-align: center;
    font-size: 13px;
    color: #fbf3f3;
    cursor: pointer;
    transition: transform 0.2s ease;
    text-decoration: none;
    display: inline-block;
}
.icon-box:hover {
    transform: scale(1.05);
}
.icon {
    background: #fff2e7;
    padding: 8px;
    border-radius: 50%;
    font-size: 19px;
    color: #fc8019;
    margin-bottom: 6px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    transition: box-shadow 0.2s ease;
}
.icon-box:hover .icon {
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
/* Canteen Section - Swapped: Image on left, options on right */
.canteen-section {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 60px;
    padding: 10px 40px;
    flex-wrap: wrap; /* For responsiveness */
}
/* LEFT SIDE - Image */
.canteen-left img {
    width: 420px;
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    transition: transform 0.4s ease;
}
.canteen-left img:hover {
    transform: scale(1.05);
}
/* RIGHT SIDE - Section */
.section {
    width: 420px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}
.section h3 {
    font-size: 30px;
    margin-bottom: 10px;
    color: #333;
    text-align: center;
    background: linear-gradient(90deg, #924b22, #e09d44);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
/* Cards - Fixed to use grid properly */
.cards {
    display: grid;
    grid-template-columns: 1fr; /* Single column for better layout with 3 items */
    gap: 15px;
    max-width: 350px;
    width: 100%;
}
.card {
    background: linear-gradient(135deg, #f2b35e, #e2902f);
    padding: 12px  16px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}
.card:hover::before {
    left: 100%;
}
.card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
}
.card h4 {
    margin-bottom: 4px;/* Reduced margin */
    font-size: 16px; /* Smaller font size */
    color: black;
}
.card p {
   font-size: 12px; /* Smaller font size */
    margin-top: 4px; /* Reduced margin */
    color: rgba(255,255,255,0.9);
}
/* Bottom Nav */
.bottom-nav {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: #ffffff;
    display: flex;
    justify-content: space-around;
    padding: 12px 0;
    z-index: 9999;
    box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
}
.bottom-nav a {
    text-decoration: none;
    font-size: 12px;
    color: #777;
    text-align: center;
    transition: color 0.2s ease;
}
.bottom-nav a:hover {
    color: #f6bc3f;
}
.bottom-nav a.active {
    color: #f6bc3f;
    font-weight: bold;
}
.bottom-nav i {
    font-size: 18px;
    margin-bottom: 4px;
}
.canteen-left {
    flex: 1.3;          /* ⬅️ makes image side BIGGER */
    display: flex;
    justify-content: center;
}
.canteen-left img {
    width: 100%;        /* fill container */
    height: auto;
    max-width: none;    /* 🔥 REMOVE LIMIT */
    border-radius: 28px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.18);
}
/* Disable all hover effects on images */
img {
    transition: none !important;
    transform: none !important;
}
img:hover {
    transform: none !important;
    filter: none !important;
    box-shadow: none !important;
}
/* Responsive adjustments */
@media (max-width: 768px) {
    .canteen-section {
        flex-direction: column;
        gap: 30px;
        padding: 20px 30px;
    }
    .canteen-left img, .section {
       width: 520px;        /* ⬅️ increase this (try 480–600) */
        max-width: none;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .cards {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>
<!-- Top Bar -->
<div class="topbar">
    <div>
        <div class="logo">UniBites 🍽️</div>
        <div class="location">📍 College Campus</div>
    </div>
    <div>
        👤 <?php echo htmlspecialchars($user_name); ?>
    </div>
</div>
<!-- Icons -->
<div class="icons">
    <a class="icon-box" href="offers.php">
        <div class="icon">🎁</div>
        Offers
    </a>
</div>
<div class="canteen-section">
    <!-- LEFT SIDE - Image -->
    <div class="canteen-left">
        <img src="../assets/image/students.avif" alt="UniBites" />
    </div>
    <!-- RIGHT SIDE - Canteens -->
    <div class="section">
        <h3>Select Your Canteen</h3>
        <div class="cards">
            <div class="card" onclick="location.href='humanities_shop.php'">
               <h4> Humanities Canteen</h4>
                <p>Fast food • Tea • Snacks</p>
            </div>
            <div class="card" onclick="location.href='admin_shop.php'">
                <h4>Admin Canteen</h4>
                <p>Main meals • Beverages</p>
            </div>
            <div class="card" onclick="location.href='pg_shop.php'">
                <h4> PG Block Canteen</h4>
                <p>Late night food</p>
            </div>
        </div>
    </div>
</div>
<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="#" class="active">🏠<br>Home</a>
    <a href="reorder.php">🛒<br>Reorder</a>
    <a href="mytoken.php">📦<br>Orders</a>
    <a href="account.php">👤<br>Account</a>
</div>
</body>
</html>
