<?php
ob_start(); // Mulai output buffering

session_start(); // Memulai sesi
require 'vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi vendor Anda

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

// Include database connection
include 'db.php'; // Pastikan ini mengarah ke file koneksi database

// Ambil user ID dari session
if (!isset($_SESSION['id'])) {
    echo "User ID is not set in session.";
    exit; // Hentikan eksekusi jika user_id tidak ada
}

$userId = $_SESSION['id']; // Ambil user_id dari session

// Fetch email dari database
$emailQuery = "SELECT user_email FROM tb_user WHERE user_id = $userId"; // Pastikan $userId sudah didefinisikan
$emailResult = $conn->query($emailQuery);

if ($emailResult) { // Periksa apakah query berhasil
    if ($emailResult->num_rows > 0) {
        $row = $emailResult->fetch_assoc();
        $recipientEmail = $row["user_email"];
    } else {
        echo "No recipient email found.";
        exit; // Hentikan eksekusi jika tidak ada email
    }
} else {
    echo "Error in email query: " . $conn->error; // Tampilkan kesalahan query
}

// Debugging recipient email
if (empty($recipientEmail)) {
    echo "Recipient email is empty.";
    exit; // Hentikan eksekusi jika email kosong
}

// Setelah PDF dihasilkan, kirim email
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Cek apakah permintaan adalah POST
    $mail = new PHPMailer(true);

    try {
        // Ambil data transaksi terbaru dan simpan di variabel global
        $result = generatePDF($userId);

        // Debugging: Check if result is empty
        if (!$result || $result->num_rows === 0) {
            echo "No transactions found. PDF will not be generated.";
            exit; // Hentikan eksekusi jika tidak ada transaksi
        }

        // Generate PDF if there are transactions
        if (!file_exists('summary.pdf')) {
            // Generate PDF
            if (!$result) {
                throw new Exception("PDF could not be generated.");
            }
        }

        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->SMTPDebug = 2; // Aktifkan debugging
        $mail->Host = 'smtp.gmail.com'; // Ganti dengan host SMTP Anda
        $mail->SMTPAuth = true;
        $mail->Username = 'farrelfarhan902@gmail.com'; // Ganti dengan email Anda
        $mail->Password = 'qxlycxwqwxlnkjut'; // Ganti dengan password aplikasi Anda
        $mail->SMTPSecure = 'tls'; // Gunakan 'tls' untuk Gmail
        $mail->Port = 587; // Port untuk TLS

        // Pengaturan email
        $mail->setFrom('farrelfarhan902@gmail.com', 'HaloWin'); // Ganti dengan email dan nama Anda
        $mail->addAddress($recipientEmail); // Menggunakan email yang diambil dari database
        $mail->Subject = 'PDF Document';
        $mail->Body    = 'Please find the attached PDF document.';

        // Lampirkan PDF yang dihasilkan
        if (file_exists('summary.pdf')) {
            $mail->addAttachment('summary.pdf'); // Ganti dengan path ke file PDF yang dihasilkan
        } else {
            echo "File summary.pdf does not exist.";
            exit; // Hentikan eksekusi jika file tidak ada
        }

        $mail->send();
        // echo 'PDF has been generated and email has been sent';
    } catch (Exception $e) {
        echo "PDF could not be generated or email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        error_log("Mailer Error: {$mail->ErrorInfo}"); // Log kesalahan ke file log
    }
}

function generatePDF($userId)
{
    // Inisialisasi Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Courier');
    $dompdf = new Dompdf($options);

    // Ambil data transaksi dari database
    global $conn; // Pastikan koneksi database dapat diakses
    $transactionQuery = "SELECT transaction_id, transaction_date, bank, total_price FROM tb_transaction WHERE user_id = $userId";
    $result = $conn->query($transactionQuery); // Ambil data transaksi terbaru

    // Debugging: Check the query and result
    if (!$result) {
        echo "Error in transaction query: " . $conn->error; // Tampilkan kesalahan query
        return null; // Return null if query fails
    }

    // Buat konten HTML untuk PDF
    $html = '<h1>Transaction Summary</h1>';
    if ($result && $result->num_rows > 0) {
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>Transaction ID</th><th>Date</th><th>Bank</th><th>Total Price</th></tr>';
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $row["transaction_id"] . '</td>';
            $html .= '<td>' . $row["transaction_date"] . '</td>';
            $html .= '<td>' . $row["bank"] . '</td>';
            $html .= '<td>' . $row["total_price"] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
    } else {
        $html .= '<p>No transactions found.</p>';
    }

    // Load konten HTML ke Dompdf
    $dompdf->loadHtml($html);

    // Set ukuran dan orientasi kertas
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Simpan PDF ke file
    $output = $dompdf->output();
    file_put_contents('summary.pdf', $output); // Simpan ke file

    return $result; // Kembalikan hasil query untuk digunakan di luar
}

// Fetch transactions and store in $result
$result = generatePDF($userId); // Call the function to generate PDF and fetch transactions

// Check if the result is valid
if ($result && $result->num_rows > 0) {
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

ob_end_clean(); // Hapus output yang ditangkap
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
                // Fetch transactions and store in $result
                $result = generatePDF($userId); // Call the function to generate PDF and fetch transactions

                // Check if the result is valid
                if ($result && $result->num_rows > 0) {
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
        <form method="POST" action="generate-pdf.php" style="text-align: center;">
            <input type="submit" value="Print PDF" class="button-summary">
        </form>
    </div>
    <!-- footer -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2024 - HaloWin.</small>
        </div>
    </footer>
</body>

</html>