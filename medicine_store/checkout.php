<?php
require('fpdf.php'); // Include FPDF library for PDF generation
include 'db.php';
session_start();

$orderPlaced = false; // Flag to check if the order is placed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);

    $full_address = "$street, $city, $state, $zip, $country";
    $tracking_id = "TRK" . strtoupper(uniqid()); // Unique order tracking ID
    $total_price = 0;

    foreach ($_SESSION['cart'] as $id => $qty) {
        $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
        $product = mysqli_fetch_assoc($product_query);
        $total_price += $product['price'] * $qty;
    }

    // Insert order
    $query = "INSERT INTO orders (customer_name, email, phone, address, total_price, tracking_id) 
              VALUES ('$name', '$email', '$phone', '$full_address', '$total_price', '$tracking_id')";
    mysqli_query($conn, $query);

    // Generate PDF Invoice
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, "Order Invoice", 1, 1, 'C');

    // Customer details
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(190, 10, "Name: $name", 1, 1);
    $pdf->Cell(190, 10, "Email: $email", 1, 1);
    $pdf->Cell(190, 10, "Phone: $phone", 1, 1);
    $pdf->Cell(190, 10, "Address: $full_address", 1, 1);
    $pdf->Cell(190, 10, "Tracking ID: $tracking_id", 1, 1);
    $pdf->Ln(10);

    // Table header
    $pdf->Cell(100, 10, "Product", 1);
    $pdf->Cell(30, 10, "Qty", 1);
    $pdf->Cell(30, 10, "Price", 1);
    $pdf->Cell(30, 10, "Total", 1);
    $pdf->Ln();

    // Order details
    foreach ($_SESSION['cart'] as $id => $qty) {
        $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
        $product = mysqli_fetch_assoc($product_query);
        $subtotal = $product['price'] * $qty;

        $pdf->Cell(100, 10, $product['name'], 1);
        $pdf->Cell(30, 10, $qty, 1);
        $pdf->Cell(30, 10, "₹" . $product['price'], 1);
        $pdf->Cell(30, 10, "₹" . $subtotal, 1);
        $pdf->Ln();
    }

    // Total price
    $pdf->Cell(160, 10, "Total Price", 1);
    $pdf->Cell(30, 10, "₹" . $total_price, 1);
    $pdf->Ln(20);

    // Order confirmation message
    $_SESSION['cart'] = []; // Clear cart after order placement
    $invoice_name = "invoice_" . $tracking_id . ".pdf";
    $pdf->Output("invoices/$invoice_name", "F");

    $orderPlaced = true; // Set flag to true after successful order

    echo "<div style='text-align: center; margin-top: 50px; font-family: Arial, sans-serif;'>";
    echo "<p style='font-size: 18px; margin-bottom: 10px;'>✅ <strong>Order placed successfully!</strong></p>";
    echo "<p style='font-size: 16px; margin-bottom: 10px;'>💰 Total: <strong>₹$total_price</strong></p>";
    echo "<p style='font-size: 16px; margin-bottom: 10px;'>📦 Tracking ID: <strong>$tracking_id</strong></p>";
    echo "<p style='font-size: 16px; margin-bottom: 10px;'><a href='invoices/$invoice_name' download style='text-decoration: none; color: #007bff; font-weight: bold;'>📥 Download Invoice</a></p>";
    echo "<p style='font-size: 16px;'><a href='index.php' style='text-decoration: none; color: #007bff; font-weight: bold;'>🏪 Back to Store</a></p>";
    echo "</div>";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 40px ;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        a {
            text-decoration: none;
            color: #007bff;
            display: inline-block;
            margin-bottom: 10px;
        }

        hr {
            border: 0;
            height: 1px;
            background: #ccc;
            margin: 10px 0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            text-align: left;
            font-weight: bold;
            margin-top: 5px;
        }

        input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Checkout</h1>
    <a href="cart.php">🛒 Back to Cart</a>
    <hr>

    <?php if (!$orderPlaced): // Show form only if order is NOT placed ?>
        <form method="post">
            <label>Full Name:</label>
            <input type="text" name="name" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone Number:</label>
            <input type="tel" name="phone" required>

            <label>Address:</label>
            <input type="text" name="street" required placeholder="Street Address">
            <input type="text" name="city" required placeholder="City">
            <input type="text" name="state" required placeholder="State">
            <input type="text" name="zip" required placeholder="ZIP Code">
            <input type="text" name="country" required value="India">

            <button type="submit">Place Order</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
