<?php
include 'db.php';
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart with quantity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } elseif ($_SESSION['cart'][$product_id] < 20) {
        $_SESSION['cart'][$product_id]++;
    } else {
        echo "<script>alert('⚠ Max 20 items allowed per product.');</script>";
    }
}

// Update quantity
if (isset($_GET['update']) && isset($_GET['id'])) {
    $update_id = $_GET['id'];
    $new_qty = (int)$_GET['qty'];

    if ($new_qty >= 1 && $new_qty <= 20) {
        $_SESSION['cart'][$update_id] = $new_qty;
    }
}

// Remove product from cart
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
}

// Fetch cart items
$cart_items = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        h1 { text-align: center; }
        .cart-container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px gray; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .cart-item img { width: 50px; height: 50px; border-radius: 5px; }
        .cart-item span { font-size: 16px; }
        .cart-item input { width: 50px; text-align: center; font-size: 16px; }
        .cart-item button { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .cart-total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 15px; }
        .checkout-btn { display: block; text-align: center; background: green; color: white; padding: 10px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .checkout-btn:hover { background: darkgreen; }
    </style>
</head>
<body>

<h1>🛒 Your Shopping Cart</h1>
<button><a href="index.php" style="display:block; text-align:center; margin-bottom:10px;">🏠 Back to Store</a></button>

<div class="cart-container">
<?php if (empty($cart_items)) { ?>
    <p style="text-align:center;">🛒 Your cart is empty.</p>
<?php } else { ?>
    <?php
    $total = 0;
    foreach ($cart_items as $id => $qty) {
        $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
        $product = mysqli_fetch_assoc($product_query);
        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
    ?>
    <div class="cart-item">
        <img src="<?php echo $product['image']; ?>" alt="Product">
        <span><?php echo $product['name']; ?> - ₹<?php echo $product['price']; ?></span>
        
        <!-- Quantity Update -->
        <form method="get" style="display:flex; align-items:center;">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="number" name="qty" min="1" max="20" value="<?php echo $qty; ?>">
            <button type="submit" style="margin-left:5px;">🔄</button>
        </form>

        <span>= ₹<?php echo $subtotal; ?></span>

        <!-- Remove Button -->
        <a href="cart.php?remove=<?php echo $id; ?>" style="color:red; text-decoration:none;">❌ Remove</a>
    </div>
    <?php } ?>

    <p class="cart-total">Total: ₹<?php echo $total; ?></p>
    <a href="checkout.php" class="checkout-btn">🛍 Proceed to Checkout</a>
<?php } ?>
</div>

</body>
</html>
