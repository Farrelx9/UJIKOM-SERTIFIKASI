<?php
session_start();
include 'db.php';

// Check if the session variable is set
if (!isset($_SESSION['id'])) {
    echo "<p>Session variable is not set. Please log in.</p>";
    exit();
}

$user_id = $_SESSION['id']; // Pastikan ini hanya diakses setelah pengecekan

// Check if the form has been submitted
if (isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details from the database
    $sql = "SELECT product_name, product_price FROM tb_product WHERE product_id = '$product_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product_name = $product['product_name'];
        $price = $product['product_price']; // Pastikan ini didefinisikan

        // Insert into the cart
        $sql = "INSERT INTO tb_cart (user_id, product_id, price, total) VALUES ('$user_id', '$product_id', '$price', '$quantity')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to the same page to prevent resubmission
            header("Location: keranjang-user.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Product not found.";
    }
}

// Fetch cart items for the user
$sql = "SELECT c.*, p.product_name FROM tb_cart c JOIN tb_product p ON c.product_id = p.product_id WHERE c.user_id = '$user_id'";
$result = $conn->query($sql);

if ($result === false) {
    "<p>Error fetching cart items: " . $conn->error . "</p>";
} else {
    "<p>Number of rows: " . $result->num_rows . "</p>";
}

$totalbelanja = 0; // Initialize totalbelanja before using it
$_SESSION['total_price'] = $totalbelanja;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HaloWin</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- header -->
    <header>
        <div class="container">
            <h1><a href="keranjang-user.php">HaloWin</a></h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="profile-user.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>
    <!-- content -->
    <div class="section">
        <div class="container">
            <h3>Keranjang</h3>
            <div class="box">
                <?php
                // Check if username is set in session
                if (isset($_SESSION['id'])) {
                    $username = $_SESSION['id'];

                    // Fetch cart items for the user
                    $sql = "SELECT c.*, p.product_name FROM tb_cart c JOIN tb_product p ON c.product_id = p.product_id WHERE c.user_id = '$username'";
                    $result = $conn->query($sql);


                    if ($result === false) {
                        echo "<p>Error fetching cart items: " . $conn->error . "</p>";
                    } else {
                        $totalbelanja = 0; // Inisialisasi total belanja
                        echo "<p>Produk: " . $result->num_rows . "</p>";
                        if ($result->num_rows > 0) {
                            // Loop through all rows of data
                            while ($row = $result->fetch_assoc()) {
                                $totalbelanja += $row['price'] * $row['total']; // Hitung total belanja
                                echo "<div class='cart-table'>";
                                echo "<table>"; // Menambahkan elemen tabel
                                echo "<tr><th>User ID</th><td>" . $row['user_id'] . "</td></tr>";
                                echo "<tr><th>Product Name</th><td>" . $row['product_name'] . "</td></tr>";
                                echo "<tr><th>Quantity</th><td>" . $row['total'] . "</td></tr>";
                                echo "<tr><th>Price</th><td>" . $row['price'] . "</td></tr>";
                                echo "<tr><th>Status</th><td>" . $row['status'] . "</td></tr>";
                                echo "<tr><td colspan='2'><form method='POST' action='hapus-keranjang.php'>
                                          <input type='hidden' name='cart_id' value='" . $row['cart_id'] . "'>
                                          <input type='submit' value='Hapus' >
                                          
                                      </form></td></tr>"; // Menambahkan tombol hapus
                                echo "</table>"; // Menutup elemen tabel
                                echo "</div>";
                            }
                            $_SESSION['total_price'] = $totalbelanja; // Simpan total belanja di session
                            echo "<p>Total Belanja: Rp " . number_format($totalbelanja, 2, ',', '.') . "</p>"; // Menampilkan total belanja
                        } else {
                            echo "<p>Your cart is empty.</p>";
                        }
                    }
                } else {
                    echo "<p>Please log in to view your cart.</p>";
                }
                ?>
            </div>
            <form method="POST" action="transaksi.php" style="text-align: center;">
                <input type="submit" value="Checkout" class="button-checkout">
            </form>
        </div>
    </div>
    <!-- footer -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2024 - HaloWin.</small>
        </div>
    </footer>
</body>

</html>