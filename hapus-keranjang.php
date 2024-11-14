<?php
include 'db.php';
function hapusKeranjang($cart_id)
{

    global $conn;

    // Siapkan dan jalankan query untuk menghapus item dari keranjang
    $stmt = $conn->prepare("DELETE FROM tb_cart WHERE cart_id = ?");
    $stmt->bind_param("i", $cart_id);

    if ($stmt->execute()) {
        // Redirect to keranjang.php after successful deletion
        header("Location: keranjang-user.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Tutup koneksi
    $stmt->close();
    $conn->close();
}

// Cek apakah ada cart_id yang diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);
    hapusKeranjang($cart_id);
} else {
    echo "Tidak ada item yang dipilih untuk dihapus.";
}
