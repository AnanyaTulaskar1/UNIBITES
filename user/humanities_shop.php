<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Humanities Canteen</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;

    /* ✅ Food Background */
    background: url('../assets/image/humanbg.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* Optional dark overlay for better visibility */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: -1;
}
.title {
    text-align: center;
    font-size: 48px;
    font-weight: bold;
    color: white;

    text-shadow:
        -3px -3px 0 black,
         3px -3px 0 black,
        -3px  3px 0 black,
         3px  3px 0 black,
         5px  5px 8px rgba(0,0,0,0.5);

    margin-bottom: 50px;
}
.shop-grid {
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 25px;
    padding: 40px;
}
.shop {
    background: white;
    border-radius: 18px;
    text-align: center;
    padding: 20px;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
.shop img {
    width: 100%;
    height: 190px;
    object-fit: cover;
    border-radius: 12px;
}
.back-link {
    position: absolute;
    top: 18px;
    left: 18px;
    display: inline-block;
    padding: 8px 14px;
    background: rgba(0, 0, 0, 0.72);
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}
</style>
</head>
<body>

<a class="back-link" href="dashboard.php">Back</a>

<h2 class="title">HUMANITIES CANTEEN</h2>

<div class="shop-grid">
    <div class="shop" onclick="location.href='menu.php?shop=tiffany'">
        <img src="../assets/image/tifin.jpeg">
        <h3>Tiffany</h3>
    </div>

    <div class="shop" onclick="location.href='menu.php?shop=zamorin_humanities'">
        <img src="../assets/image/zamorin.jpeg">
        <h3>Zamorin</h3>
    </div>
</div>

</body>
</html>
