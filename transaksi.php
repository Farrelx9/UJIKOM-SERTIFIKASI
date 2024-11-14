<?php
session_start();
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login-user.php"</script>';
}

// Include file koneksi database
include 'db.php';

// Ambil data transaksi
$sql = "SELECT * FROM tb_transaction WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // Menggunakan user_id dari session
$stmt->execute();
$result = $stmt->get_result();

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
            <h1><a href="transaksi.php">HaloWin</a></h1>
            <ul>
                <li><a href="keranjang-user.php">Kembali ke Keranjang</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>
    <!-- content -->
    <div class="section">
        <div class="container">
            <h3>Pembayaran</h3>
            <div class="box">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User ID</th>
                            <th>Transaction Date</th>
                            <th>Bank</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['transaction_id']; ?></td>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['transaction_date']; ?></td>
                                <td><?php echo $row['bank']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Tidak ada transaksi yang ditemukan.</p>
                <?php endif; ?>
            </div>
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

<?php
$stmt->close();
$conn->close();
?>