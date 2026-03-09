<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Canteen</title>
<link rel="stylesheet" href="../assets/style.css">

<style>
body {
    margin: 0;
    padding: 30px;
    font-family: 'Segoe UI', Arial, sans-serif;

    background: url('../assets/image/adminbgg.jpg') no-repeat center center fixed;
    background-size: cover;
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
    grid-template-columns: repeat(2, 350px);
    justify-content: center;
    gap: 40px;
}

.shop-card:nth-child(2) {
    grid-column: 1  / span 2;
    justify-self: center;
}

/* Card */
.shop-card {
    width: 300px;
    text-align:center;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15 px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.18);
    transition: transform 0.3s ease;
    cursor: pointer;

}

.shop-card:hover {
    transform: translateY(-8px);
}

/* Image */
.shop-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
   
}

/* Shop name section */
.shop-name {
    background: linear-gradient(136deg, #f2b35e, #e2902f);
    padding: 25px;
    text-align: center;
}

.shop-name h3 {
    margin: 15px 0;
    font-size: 26px;
    color: #000;
}
.page-wrapper {
    max-width: 800px;
    margin: 0 auto;   /* THIS centers everything */
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

<div class="shop-grid">
      <h1 class="title">ADMIN CANTEEN </h1>

    <div class="shop-card" onclick="location.href='menu.php?shop=north'">
        <img src="../assets/image/north.jpeg" alt="North">
        <h3>North Delicacies</h3>
    </div>

    <div class="shop-card" onclick="location.href='menu.php?shop=south'">
        <img src="../assets/image/south.jpeg" alt="South">
        <h3>South Delicacies</h3>
    </div>

    <div class="shop-card" onclick="location.href='menu.php?shop=zamorin'">
        <img src="../assets/image/zamorin.jpeg" alt="Zamorin">
        <h3>Zamorin</h3>
    </div>

</div>

</body>
</html>
