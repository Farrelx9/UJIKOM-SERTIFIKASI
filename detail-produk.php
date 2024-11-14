<?php
error_reporting(0);
include 'db.php';
$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_addres FROM tb_admin
WHERE admin_id =1");
$a = mysqli_fetch_object($kontak);

$produk = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_id = '" .  $_GET['id']  . "'");
$p = mysqli_fetch_object($produk);

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
            <h1><a href="index.php">HaloWin</a></h1>
            <ul>
                <li><a href="produk.php">Produk</a></li>
            </ul>
        </div>
    </header>
    <!--search-->
    <div class="search">
        <div class="container">
            <form action="produk.php">
                <input type="text" name="search" placeholder="Cari Produk" value="<?php echo $_GET['search'] ?>">
                <input type="hidden" name="kat" value="<?php echo $_GET['search'] ?>">
                <input type="submit" name="cari" value="Cari Produk">
            </form>
        </div>
    </div>
    <!--detail produk-->
    <div class="section">
        <div class="container">
            <h3> Produk</h3>
            <div class="box">
                <div class="col-2">
                    <img src="produk/<?php echo $p->product_image ?>" width="100%">
                </div>
                <div class="col-2">
                    <h3><?php echo $p->product_name ?></h3>
                    <h4>Rp. <?php echo number_format($p->product_price) ?></h4>
                    <p>Deskripsi : <br>
                        <?php echo $p->product_description ?>
                    </p>
                    <div class="product-details">

                        <!-- New Add to Cart Form -->
                        <form action="keranjang-user.php" method="POST" class="add-cart">
                            <input type="hidden" name="product_id" value="<?php echo $p->product_id; ?>">
                            <input type="number" name="quantity" value="1" min="1">
                            <input type="submit" name="add_to_cart" value="Add to Cart">
                        </form>
                        <?php
                        session_start(); // Ensure session is started
                        if (isset($_POST['add_to_cart'])) {
                            $product_id = $_POST['product_id']; // Ambil ID produk dari form
                            $quantity = $_POST['quantity']; // Ambil jumlah dari form
                            $username = $_SESSION['username']; // Ambil username dari session

                            // Check if username is set
                            if (!isset($username)) {
                                echo "User is not logged in.";
                                exit;
                            }

                            // Insert ke dalam tb_cart
                            $sql = "INSERT INTO tb_cart (username, product_id, total) VALUES ('$username', '$product_id', '$quantity')";
                            if ($conn->query($sql) === TRUE) {
                                echo "Product added to cart successfully.";
                            } else {
                                echo "Error: " . $sql . "<br>" . mysqli_error($conn); // Display error if query fails
                            }
                        }
                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--footer-->
    <div class="footer">
        <div class="container">
            <h4>Alamat</h4>
            <p><?php echo $a->admin_addres ?></p>
            <h4>Email</h4>
            <p><?php echo $a->admin_email ?></p>
            <h4>No HP</h4>
            <p><?php echo $a->admin_telp ?></p>
            <small>Copyright &copy; 2024 - HaloWin.</small>
        </div>
    </div>
</body>

</html>