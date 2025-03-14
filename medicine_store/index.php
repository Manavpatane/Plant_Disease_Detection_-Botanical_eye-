<?php
include 'db.php';

$result = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Medicine Store</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="navbar.css">
    
</head>
<body>

 <!-- Navigation Bar -->
 <nav class="navbar">
    <div class="logo">Botanical Eye</div>
    <ul class="nav-links">
        <li><a href="http://localhost:8080/home/home.html">Home</a></li>
        <li><a href="http://127.0.0.1:5000/">Prediction</a></li>
        <li><a href="http://127.0.0.1:5000/disease-prediction">Plot Graph</a></li>
        <li><a href="http://localhost:8080/fetch_plant_details/search_disease.html">Plant Info</a></li>
        <li><a href="http://localhost:8080/medicine_store/">Plant Medicine Store</a></li>
        <li><a href="http://localhost:8080/login_signup_page/index.html" class="logout-btn">Logout</a></li>
    </ul>
</nav>

<h1>Plant Medicine Store</h1>
<button><a href="cart.php">🛒 View Cart</a></button>
<hr>

<div class="product-container">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="product">
            <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="150">
            <h2><?php echo $row['name']; ?></h2>
            <p><?php echo $row['description']; ?></p>
            <p>Price: ₹<?php echo $row['price']; ?></p>
            <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    <?php } ?>
</div>

</body>
</html>