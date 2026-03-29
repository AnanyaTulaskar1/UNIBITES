<?php
session_start();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (empty($_SESSION['cart'])) {
    unset($_SESSION['cart_shop']);
}

$shop = $_GET['shop'] ?? '';
 $shopBackLinks = [
    'tiffany' => 'humanities_shop.php',
    'zamorin_humanities' => 'humanities_shop.php',
    'north' => 'admin_shop.php',
    'south' => 'admin_shop.php',
    'zamorin' => 'admin_shop.php',
    'shalom' => 'pg_shop.php',
    'heavens' => 'pg_shop.php',
    'dreamland' => 'pg_shop.php',
];
$backLink = $shopBackLinks[$shop] ?? 'dashboard.php';
$shopLabels = [
    'tiffany' => 'Tiffany',
    'zamorin_humanities' => 'Zamorin (Humanities)',
    'north' => 'North Delicacies',
    'south' => 'South Delicacies',
    'zamorin' => 'Zamorin (Admin)',
    'shalom' => 'Shalom Cafe',
    'heavens' => 'Heavens Kitchen',
    'dreamland' => 'Dream Land Food Corner',
];
$shopHeading = $shopLabels[$shop] ?? strtoupper($shop);
    $menus = [

/* ================= ADMIN BLOCK ================= */

    "north" => [
    ["name"=>"MOMO Burger","price"=>80,"image"=>"assets/food/burger1.jpg"],
    ["name"=>"Classic Burger","price"=>90,"image"=>"assets/food/burger.jpg"],

    ["name"=>"Bread Omelette","price"=>50,"image"=>"assets/food/egg1.jpg"],
    ["name"=>"Egg Roll","price"=>70,"image"=>"assets/food/egg.jpg"],

    ["name"=>"Butter Pav Bhaji","price"=>90,"image"=>"assets/food/pavbhaji1.jpg"],
    ["name"=>"Cheese Pav Bhaji","price"=>120,"image"=>"assets/food/pavbhaji.jpg"],

    ["name"=>"Aloo Paratha","price"=>60,"image"=>"assets/food/paratha.jpg"],
    ["name"=>"Paneer Paratha","price"=>80,"image"=>"assets/food/paratha1.jpg"],

    ["name"=>"Chole Tikki","price"=>60,"image"=>"assets/food/Street.jpg"],
    ["name"=>"Masala Puri ","price"=>60,"image"=>"assets/food/street1.jpg"],

    ["name"=>"Rajma Chawal","price"=>90,"image"=>"assets/food/meals.jpg"],
    ["name"=>"Chole Chawal","price"=>90,"image"=>"assets/food/meals1.jpg"],

    ["name"=>"Chicken Gravy Meal","price"=>130,"image"=>"assets/food/chicken.jpg"],
    ["name"=>"Butter Chicken with Paratha/Rice","price"=>160,"image"=>"assets/food/chicken1.jpg"],

    ["name"=>"Veg Coleslaw Sandwich","price"=>80,"image"=>"assets/food/sandwich.jpg"],
    ["name"=>"Paneer Tikka Sandwich","price"=>100,"image"=>"assets/food/sandwich1.jpg"],

    ["name"=>"Veg Chinese Combo","price"=>130,"image"=>"assets/food/chinese.jpg"],
    ["name"=>"Chicken Chinese Combo","price"=>150,"image"=>"assets/food/chinese1.jpg"],

    ["name"=>"Cold Drink Glass","price"=>60,"image"=>"assets/food/drinks.jpg"],
    ["name"=>"Butter Milk ","price"=>60,"image"=>"assets/food/drinks1.jpg"],
    ],

/* ================= SOUTH DELICACIES ================= */

    "south" => [

    ["name"=>"Idly Set","price"=>40,"image"=>"assets/food/idly.jpg"],
    ["name"=>"Dosa Set","price"=>40,"image"=>"assets/food/dosa.jpg"],
    ["name"=>"Parotta","price"=>15,"image"=>"assets/food/parotta.jpg"],
    ["name"=>"Egg Curry","price"=>50,"image"=>"assets/food/egg2.jpg"],

    ["name"=>"Puttu + Kadala","price"=>70,"image"=>"assets/food/puttu.jpg"],
    ["name"=>"Appam + Chicken","price"=>90,"image"=>"assets/food/appam.jpg"],

    ["name"=>"Chicken Biriyani","price"=>120,"image"=>"assets/food/biryani.jpg"],
    ["name"=>"Parotta Veg Kurma","price"=>70,"image"=>"assets/food/parotta1.jpg"],
    ["name"=>"Parotta Chicken Curry","price"=>90,"image"=>"assets/food/chicken2.jpg"],

    ["name"=>"Chicken Kabab","price"=>100,"image"=>"assets/food/kabab.jpg"],
    ["name"=>"Chicken Pepper","price"=>110,"image"=>"assets/food/kabab1.jpg"],
    ["name"=>"Chicken Masala","price"=>110,"image"=>"assets/food/chicken3.jpg"],

    ["name"=>"Chicken Kothu Paratha","price"=>80,"image"=>"assets/food/kothu.jpg"],
    ["name"=>"Egg Kothu Paratha","price"=>70,"image"=>"assets/food/kothu1.jpg"],

    ["name"=>"Veg Momo","price"=>70,"image"=>"assets/food/momo.jpg"],
    ["name"=>"Veg Cheese Momo","price"=>100,"image"=>"assets/food/momo1.jpg"],
    ["name"=>"Veg Paneer Momo","price"=>90,"image"=>"assets/food/momo2.jpg"],

    ["name"=>"Chicken Momo","price"=>80,"image"=>"assets/food/momo3.jpg"],
    ["name"=>"Chicken Cheese Momo","price"=>120,"image"=>"assets/food/momo4.jpg"],
    ["name"=>"Chicken Masala Momo","price"=>90,"image"=>"assets/food/momo5.jpg"],

    ["name"=>"Veg Momo Roll","price"=>70,"image"=>"assets/food/roll.jpg"],
    ["name"=>"Paneer Tikka Roll","price"=>100,"image"=>"assets/food/roll1.jpg"],
    ["name"=>"Mushroom Roll","price"=>100,"image"=>"assets/food/roll2.jpg"],

    ["name"=>"Chicken Momo Roll","price"=>90,"image"=>"assets/food/roll3.jpg"],
    ["name"=>"Chicken Nuggets Roll","price"=>90,"image"=>"assets/food/roll4.jpg"],
    ["name"=>"Tawa Chicken Roll","price"=>100,"image"=>"assets/food/roll5.jpg"],

    ["name"=>"Potato Twister","price"=>60,"image"=>"assets/food/fries.jpg"],
    ["name"=>"French Fries","price"=>70,"image"=>"assets/food/fries1.jpg"],
    ["name"=>"Veg Maggi","price"=>50,"image"=>"assets/food/maggi.jpg"],
    ],

    /* ================= ZAMORIN ================= */

    "zamorin" => [

    ["name"=>"Tea","price"=>15,"image"=>"assets/food/tea.jpg"],
    ["name"=>"Coffee","price"=>20,"image"=>"assets/food/coffee.jpg"],
    ["name"=>"Lemon Tea","price"=>20,"image"=>"assets/food/tea1.jpg"],
    ["name"=>"Boost ","price"=>25,"image"=>"assets/food/drinks2.jpg"],

    ["name"=>"Pocket Shawarma","price"=>40,"image"=>"assets/food/shawarma.jpg"],
    ["name"=>"Chicken Samosa","price"=>30,"image"=>"assets/food/snacks.jpg"],
    ["name"=>"Pasta","price"=>70,"image"=>"assets/food/pasta.jpg"],
    ["name"=>"Ulli Vada","price"=>15,"image"=>"assets/food/snacks1.jpg"],

    ["name"=>"Mango Juice","price"=>50,"image"=>"assets/food/juice.jpg"],
    ["name"=>"Orange Juice","price"=>50,"image"=>"assets/food/juice1.jpg"],
    ["name"=>"Lime Juice","price"=>25,"image"=>"assets/food/juice2.jpg"],
    ["name"=>"Mint Lime","price"=>35,"image"=>"assets/food/juice3.jpg"],

    ["name"=>"Lime Soda","price"=>30,"image"=>"assets/food/soda.jpg"],
    ["name"=>"Grape Soda","price"=>40,"image"=>"assets/food/soda1.jpg"],
    ["name"=>"Soda Sarbath","price"=>40,"image"=>"assets/food/soda2.jpg"],

    ["name"=>"Chocolate Milkshake","price"=>60,"image"=>"assets/food/milkshake.jpg"],
    ["name"=>"Butter Fruit Milkshake","price"=>70,"image"=>"assets/food/milkshake1.jpg"],
    ["name"=>"Rose Milk","price"=>70,"image"=>"assets/food/milkshake2.jpg"],
    ["name"=>"Zamorin Special","price"=>80,"image"=>"assets/food/milkshake3.jpg"],
    ],
    /* ================= PG BLOCK ================= */


    "dreamland" => [
    ["name"=>"Veg Momos","price"=>70,"image"=>"assets/food/momo.jpg"],
    ["name"=>"Chicken Momos","price"=>80,"image"=>"assets/food/momo3.jpg"],
    ["name"=>"Paneer Momos","price"=>80,"image"=>"assets/food/momo2.jpg"],
    ["name"=>"Cheese Momos","price"=>80,"image"=>"assets/food/momo1.jpg"],
    ["name"=>"Classic Fries","price"=>60,"image"=>"assets/food/fries2.jpg"],
    ["name"=>"Peri Peri Fries","price"=>70,"image"=>"assets/food/fries3.jpg"],
    ["name"=>"Tandoori Loaded Fries","price"=>70,"image"=>"assets/food/fries4.jpg"],
    
        
    ],

    "heavens" => [
    ["name"=>"Normal Tea","price"=>12,"image"=>"assets/food/tea.jpg"],
    ["name"=>"Coffee","price"=>15,"image"=>"assets/food/coffee.jpg"],
    ["name"=>"Ginger Tea","price"=>15,"image"=>"assets/food/GingerT.jpg"],
    ["name"=>"lemon Tea","price"=>15,"image"=>"assets/food/tea1.jpg"],
    ["name"=>"Lime Juice","price"=>20,"image"=>"assets/food/juice2.jpg"],
    ["name"=>"Lassi","price"=>40,"image"=>"assets/food/drinks1.jpg"],
    ["name"=>"Cold Badam Milk","price"=>40,"image"=>"assets/food/milkshake4.jpg"],
    ["name"=>"Idly","price"=>50,"image"=>"assets/food/idly.jpg"],
    ["name"=>"Plain Dosa","price"=>50,"image"=>"assets/food/dosa.jpg"],
    ["name"=>"Chicken Fried Rice","price"=>80,"image"=>"assets/food/friedrice.jpg"],
    ["name"=>"Egg Fried Rice","price"=>70,"image"=>"assets/food/friedrice1.jpg"],
        ["name"=>"Chicken Noodles","price"=>80,"image"=>"assets/food/chickenN.jpg"],
        ["name"=>"Egg Noodles","price"=>70,"image"=>"assets/food/eggN.jpg"],
        ["name"=>"Veg Noodles","price"=>60,"image"=>"assets/food/vegNoodles.jpg"],
        ["name"=>"Chicken Popcorn (12 ps)","price"=>15,"image"=>"assets/food/snacks.jpg"],
        ["name"=>"Chicken Burger","price"=>80,"image"=>"assets/food/burger1.jpg"],
        ["name"=>"Veg Burger","price"=>70,"image"=>"assets/food/burger.jpg"],

    ],

    "shalom" => [
    ["name"=>"Classic Maggi","price"=>40,"image"=>"assets/food/maggi.jpg"],
    ["name"=>"Spicy Maggi","price"=>50,"image"=>"assets/food/maggi1.jpg"],
    ["name"=>"Corn Masala","price"=>50,"image"=>"assets/food/corn.jpg"],
    ["name"=>"Veg Momos","price"=>70,"image"=>"assets/food/momo.jpg"],
    ["name"=>"Chicken Momos","price"=>80,"image"=>"assets/food/momo3.jpg"],
    ["name"=>"Normal Avil Milk","price"=>60,"image"=>"assets/food/avil1.jpg"],
    ["name"=>"Special Avil Milk","price"=>70,"image"=>"assets/food/avil.jpg"],
    ["name"=>"French Fries","price"=>60,"image"=>"assets/food/fries1.jpg"],
    ["name"=>"Tandoorfi Masala Maggi","price"=>50,"image"=>"assets/food/sandwich.jpg"],
        ["name"=>"BBQ Masala Maggi","price"=>80,"image"=>"assets/food/sandwich1.jpg"],
        ["name"=>"Chicken Sandwich","price"=>90,"image"=>"assets/food/sandwich2.jpg"],
        ["name"=>"Cold Coffee","price"=>70,"image"=>"assets/food/coldcoffee.jpg"],
        ["name"=>"Chocolate Milkshake","price"=>70,"image"=>"assets/food/Chocolate.jpg"],
        ["name"=>"Strawberry Milkshake","price"=>70,"image"=>"assets/food/strawberry.jpg"],
        ["name"=>"Oreo Milkshake","price"=>80,"image"=>"assets/food/Oreo.jpg"],
        ["name"=>"Mango Shakes","price"=>70,"image"=>"assets/food/smoothie.jpg"],
        ["name"=>"Avocado Shakes","price"=>70,"image"=>"assets/food/smoothie1.jpg"],
        ["name"=>"Kitkat shake","price"=>70,"image"=>"assets/food/smoothie2.jpg"],


    ],

    /* ================= HUMANITIES ================= */

    "tiffany" => [
    ["name"=>"Tea","price"=>15,"image"=>"assets/food/tea.jpg"],
    ["name"=>"Filter Coffee","price"=>20,"image"=>"assets/food/coffee.jpg"],
    ["name"=>"Horlicks / Boost","price"=>25,"image"=>"assets/food/drinks2.jpg"],
    ["name"=>"Butter Milk","price"=>25,"image"=>"assets/food/drinks1.jpg"],
    ["name"=>"Samosa","price"=>15,"image"=>"assets/food/snacks.jpg"],
    ["name"=>"Egg Puff","price"=>30,"image"=>"assets/food/snacks2.jpg"],
    ["name"=>"French Fries","price"=>50,"image"=>"assets/food/fries1.jpg"],
    ["name"=>"Veg Sandwich","price"=>40,"image"=>"assets/food/sandwich.jpg"],
    ["name"=>"Thatte Idly","price"=>20,"image"=>"assets/food/idly1.jpg"],
    ["name"=>"Plain Dosa","price"=>40,"image"=>"assets/food/dosa.jpg"],
    ["name"=>"Veg Fried Rice","price"=>100,"image"=>"assets/food/vegfriedrice.jpg"],
    ["name"=>"Chicken Biryani","price"=>99,"image"=>"assets/food/biryani.jpg"],
    ["name"=>"POPcorn","price"=>50,"image"=>"assets/food/popcorn.jpg"],
    ["name"=>"peri peri Popcorn","price"=>65,"image"=>"assets/food/popcorn.jpg"],
    ["name"=>"Caramel Popcorn","price"=>85,"image"=>"assets/food/Caramel-Popcorn-3.jpg"],
    ],

    "zamorin_humanities" => [

    ["name"=>"Tea","price"=>15,"image"=>"assets/food/tea.jpg"],
    ["name"=>"Coffee","price"=>20,"image"=>"assets/food/coffee.jpg"],
    ["name"=>"Lemon Tea","price"=>20,"image"=>"assets/food/tea1.jpg"],
    ["name"=>"Boost / Horlicks","price"=>25,"image"=>"assets/food/drinks2.jpg"],

    ["name"=>"Pocket Shawarma","price"=>40,"image"=>"assets/food/shawarma.jpg"],
    ["name"=>"Chicken Samosa","price"=>30,"image"=>"assets/food/snacks.jpg"],
    ["name"=>"Pasta","price"=>70,"image"=>"assets/food/pasta.jpg"],
    ["name"=>"Ulli Vada","price"=>15,"image"=>"assets/food/snacks1.jpg"],

    ["name"=>"Mango Juice","price"=>50,"image"=>"assets/food/juice.jpg"],
    ["name"=>"Orange Juice","price"=>50,"image"=>"assets/food/juice1.jpg"],
    ["name"=>"Lime Juice","price"=>25,"image"=>"assets/food/juice2.jpg"],
    ["name"=>"Mint Lime","price"=>35,"image"=>"assets/food/juice3.jpg"],

    ["name"=>"Lime Soda","price"=>30,"image"=>"assets/food/soda.jpg"],
    ["name"=>"Grape Soda","price"=>40,"image"=>"assets/food/soda1.jpg"],
    ["name"=>"Soda Sarbath","price"=>40,"image"=>"assets/food/soda2.jpg"],

    ["name"=>"Chocolate Milkshake","price"=>60,"image"=>"assets/food/milkshake.jpg"],
    ["name"=>"Butter Fruit Milkshake","price"=>70,"image"=>"assets/food/milkshake1.jpg"],
    ["name"=>"Rose Milk","price"=>70,"image"=>"assets/food/milkshake2.jpg"],
    ["name"=>"Zamorin Special","price"=>80,"image"=>"assets/food/milkshake3.jpg"],
    ],
    ];
$menu = $menus[$shop] ?? [];
$cart_error = '';
/* ADD / REMOVE CART */
if (isset($_POST['action'], $_POST['id'])) {

    $id = (int) $_POST['id'];
    if (!isset($menu[$id])) {
        $id = -1;
    }
    $item = $menu[$id] ?? null;
    $cart_key = $shop . '_' . $id;

    if ($_POST['action'] === "add" && $item !== null) {
        $active_shop = $_SESSION['cart_shop'] ?? null;
        if (!empty($_SESSION['cart']) && $active_shop !== null && $active_shop !== $shop) {
            $cart_error = "You can order from only one shop at a time. Clear cart to change shop.";
        } else {
            $_SESSION['cart_shop'] = $shop;

            if (isset($_SESSION['cart'][$cart_key])) {
                $_SESSION['cart'][$cart_key]['qty']++;
            } else {
                $_SESSION['cart'][$cart_key] = [
                    'shop' => $shop,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => 1
                ];
            }
        }
    }

    if ($_POST['action'] === "remove") {
        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['qty']--;

            if ($_SESSION['cart'][$cart_key]['qty'] <= 0) {
                unset($_SESSION['cart'][$cart_key]);
                if (empty($_SESSION['cart'])) {
                    unset($_SESSION['cart_shop']);
                }
            }
        }
    }
}

$total_items = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $total_items += $cart_item['qty'];
}?>
<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
<style>
body{
    font-family:Arial;
    background:#f5f5f5;
    margin:0;
    padding:15px
}
.top-nav {
    margin-bottom: 12px;
}
.back-link {
    display: inline-block;
    background: #111827;
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
}

.food-card{
    display:flex;
    background:#fff;
    margin:12px;
    padding:10px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

.food-card img{
    width:90px;
    height:90px;
    border-radius:10px;
    object-fit:cover;
}

.food-info{
    flex:1;
    padding-left:12px;
}

.food-info h4{
    margin:0;
}

.food-info p{
    margin:6px 0;
    font-weight:bold;
}

.qty-box{
    display:flex;
    align-items:center;
    border:1px solid #ccc;
    border-radius:6px;
    width:90px;
    justify-content:space-around;
}

.qty-box button{
     border:none;
    background:none;
    font-size:18px;
    cursor:pointer;
    color:#000;
}
.qty-box span{
    color:#000;              /* quantity also black */
}
.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px
}
.card{
 background:#fff;
 border-radius:12px;
 padding:10px;
 box-shadow:0 5px 10px rgba(0,0,0,.1)
}
.card img{
 width:100%;
 height:120px;
 object-fit:cover;
 border-radius:10px
}
.card h4{
    margin:8px 0 4px
}
.price{
    color:#27ae60;
    font-weight:bold
}
button{
 background:#fc8019;
 color:white;
 border:none;
 padding:8px 12px;
 border-radius:8px;
 cursor:pointer
}
</style>
</head>
<body>
    <div class="top-nav">
        <a class="back-link" href="<?= htmlspecialchars($backLink) ?>">Back</a>
    </div>
    <h2><?= htmlspecialchars($shopHeading) ?> MENU</h2>
<?php if ($cart_error !== ''): ?>
<div style="background:#fee2e2;color:#991b1b;padding:10px 12px;border-radius:8px;margin:0 12px 12px;">
    <?= htmlspecialchars($cart_error) ?>
</div>
<?php endif; ?>

<div class="grid">
<?php foreach($menu as $id => $item):
  $cart_key = $shop . '_' . $id;
  $qty = isset($_SESSION['cart'][$cart_key])
     ? $_SESSION['cart'][$cart_key]['qty']
     : 0;
?>

<div class="food-card">
    <img src="../<?= $item['image'] ?>" alt="<?= $item['name'] ?>">

    <div class="food-info">
        <h4><?= $item['name'] ?></h4>
        <p>₹<?= $item['price'] ?></p>

        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">

           <?php if ($qty == 0): ?>
                <button type="submit" name="action" value="add"
                    style="background:#fc8019;
                    color:black;
                    border:none;
                    padding:8px 16px;
                    border-radius:8px;
                    font-weight:bold;">
                    ADD
                </button>
              <?php else: ?>
            <div class="qty-box">
                 <button type="submit" name="action" value="remove">−</button>
                <span><?= $qty ?></span>
                <button type="submit" name="action" value="add">+</button>
            </div>
            <?php endif; ?>

        </form>
    </div>
</div>
<?php endforeach; ?>
<?php if ($total_items > 0): ?>
<div style="
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#fc8019;
    color:white;
    padding:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-weight:bold;
">
    <?= $total_items ?> Items Added
   <a href="cart.php?shop=<?= $shop ?>">
        View Cart →
    </a>

</div>
<?php endif; ?>


</body>
</html>
