<?php
session_start();
include 'db.php';

if (isset($_POST['product_id'], $_POST['quantity'])) {
    $username = $_SESSION['username']; // Ensure the user is logged in
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details from the database
    $sql = "SELECT product_name, price FROM tb_products WHERE id = '$product_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product_name = $product['product_name'];
        $price = $product['price'];

        // Insert into the cart
        $sql = "INSERT INTO tb_cart (username, product_id, product_name, price, total) VALUES ('$username', '$product_id', '$product_name', '$price', '$quantity')";

        if ($conn->query($sql) === TRUE) {
            echo "Product added to cart successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Product not found.";
    }
} else {
    echo "Invalid product data.";
}
