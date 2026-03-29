 <!DOCTYPE html>
<html>
<head>
<title>PG Canteen</title>
<style>
body {
    margin: 0;
    padding: 30px;
    font-family: 'Segoe UI', Arial, sans-serif;

    background: url('../assets/image/backgroungpg.jpg') no-repeat center center fixed;
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
    grid-template-columns: repeat(2,1fr);
    gap: 25px;
    padding: 40px;
}
.shop {
    background: white;
    border-radius: 16px;
    text-align: center;
    padding: 25px;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
.shop img {
    width: 100%;
    height: 150px;
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

<h2 class="title">PG CANTEEN</h2>

<div class="shop-grid">
    <div class="shop" onclick="location.href='menu.php?shop=shalom'">
        <img src="../assets/image/shalom.jpeg" alt="shalom">
        Shalom Cafe
    </div>

    <div class="shop" onclick="location.href='menu.php?shop=heavens'">
        <img src="../assets/image/heaven.jpeg" alt="heaven">
        Heaven’s Kitchen
    </div>

    <div class="shop" onclick="location.href='menu.php?shop=dreamland'">
        <img src="../assets/image/dream.jpeg" alt="dream">
        Dream Land Food Corner
    </div>
</div>

</body>
</html>
