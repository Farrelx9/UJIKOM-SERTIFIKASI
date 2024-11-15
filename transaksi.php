<?php
session_start();

// Debug: Cek nilai user_id dalam session
if (isset($_SESSION['id'])) {
    $_SESSION['id'];
} else {
    echo "User ID tidak ditemukan dalam session.";
    exit; // Hentikan eksekusi jika user_id tidak ada
}

// Include file koneksi database
include 'db.php';

// Ambil data dari tb_cart
$sql_cart = "SELECT * FROM tb_cart WHERE user_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("i", $_SESSION['id']);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

// Proses transaksi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bank = 'Default Bank'; // Set a default bank name or handle as needed
    $total_price = 0; // Inisialisasi total harga

    // Hitung total harga dari keranjang
    while ($row_cart = $result_cart->fetch_assoc()) {
        // Debug: Tampilkan nilai yang diambil
        echo "Price: " . $row_cart['price'] . " Total: " . $row_cart['total'] . "<br>";
        $total_price += $row_cart['price'] * $row_cart['total'];
    }

    // Masukkan data ke tb_transaction
    $sql_transaction = "INSERT INTO tb_transaction (user_id, transaction_date, bank, total_price) VALUES (?, NOW(), ?, ?)"; // Removed transaction_id
    $stmt_transaction = $conn->prepare($sql_transaction);
    $stmt_transaction->bind_param("isd", $_SESSION['id'], $bank, $total_price); // Pastikan total_price sesuai tipe data


    // Eksekusi dan periksa kesalahan
    if (!$stmt_transaction->execute()) {
        echo "Error inserting transaction: " . $stmt_transaction->error;
    } else {
        // Kosongkan keranjang setelah transaksi
        $sql_empty_cart = "DELETE FROM tb_cart WHERE user_id = ?";
        $stmt_empty_cart = $conn->prepare($sql_empty_cart);
        $stmt_empty_cart->bind_param("i", $_SESSION['id']);
        $stmt_empty_cart->execute();

        // Redirect atau tampilkan pesan sukses
        echo '<script>alert("Transaksi berhasil!"); window.location="transaksi.php";</script>';
    }

    $_SESSION['total_price'] = $total_price; // Simpan total belanja di session
}

// Ambil data transaksi
$sql_transaction = "SELECT * FROM tb_transaction WHERE user_id = ?";
$stmt_transaction = $conn->prepare($sql_transaction);
$stmt_transaction->bind_param("i", $_SESSION['id']);
$stmt_transaction->execute();
$result_transaction = $stmt_transaction->get_result();
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
                <?php if ($result_transaction->num_rows > 0): ?>
                    <table class="summary">
                        <tr>
                            <th>Transaction ID</th>
                            <th>User ID</th>
                            <th>Transaction Date</th>
                            <th>Bank</th>
                            <th>Total Price</th>
                        </tr>
                        <?php while ($row = $result_transaction->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['transaction_id']; ?></td>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['transaction_date']; ?></td>
                                <td><?php echo $row['bank']; ?></td>
                                <td><?php echo 'Rp ' . number_format((float)$row['total_price'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                    <form method="POST" action="summary.php">
                        <input type="submit" value="Cetak Bukti Transaksi" class="button-summary">
                    </form>
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
$stmt_cart->close();
$stmt_transaction->close();
$conn->close();
?>