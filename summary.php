<?php
session_start();
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
}

// Include database connection
include 'db.php'; // Ensure this file contains the connection setup

// Fetch summary from tb_transaction
$query = "SELECT transaction_id, transaction_date, bank, total_price FROM tb_transaction"; // Adjusted query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HaloWin</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <style>
        @media print {

            header,
            footer,
            .button-summary {
                display: none;
                /* Sembunyikan header, footer, dan tombol Checkout saat mencetak */
            }
        }
    </style>
</head>

<body>
    <!-- header -->
    <header>
        <div class="container">
            <h1><a href="summary.php">HaloWin</a></h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>
    <!-- content -->
    <div class="section-summary">
        <div class="container">
            <h3>Summary</h3>
            <div class="box-summary">
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "Transaction ID: " . $row["transaction_id"] .
                            " - Date: " . $row["transaction_date"] .
                            " - Bank: " . $row["bank"] .
                            " - Total Price: " . $row["total_price"] . "<br>"; // Adjusted output
                    }
                } else {
                    echo "No transactions found.";
                }
                ?>
            </div>
        </div>
        <form method="POST" action="summary.php" style="text-align: center ; ">
            <input type="submit" value="Checkout" class="button-summary" onclick="printPage(); return false;">
        </form>
        <script>
            function printPage() {
                window.print(); // Menjalankan perintah cetak
                return true; // Melanjutkan pengiriman formulir
            }
        </script>
    </div>
    <!-- footer -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2024 - HaloWin.</small>
        </div>
    </footer>
</body>

</html>