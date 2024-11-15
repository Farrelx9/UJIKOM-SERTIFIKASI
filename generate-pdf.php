<?php
require 'vendor/autoload.php'; // Pastikan path ini sesuai dengan instalasi Dompdf Anda

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inisialisasi Dompdf
$dompdf = new Dompdf();

// Mulai output buffering
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Summary PDF</title>
    <style>
        /* Tambahkan gaya CSS yang diperlukan untuk PDF */
        body {
            font-family: 'Poppins', sans-serif;
        }

        h3 {
            text-align: center;
        }

        .box-summary {
            margin: 20px;
        }
    </style>
</head>

<body>
    <h3>Summary</h3>
    <div class="box-summary">
        <?php
        // Include database connection
        include 'db.php'; // Pastikan ini mengarah ke file koneksi database
        
        // Fetch summary from tb_transaction
        $query = "SELECT transaction_id, transaction_date, bank, total_price FROM tb_transaction";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "Transaction ID: " . $row["transaction_id"] . "<br>" .
                    "Date: " . $row["transaction_date"] . "<br>" .
                    "Bank: " . $row["bank"] . "<br>" .
                    "Total Price: " . $row["total_price"] . "<br><br>";
            }
        } else {
            echo "No transactions found.";
        }
        ?>
    </div>
</body>

</html>

<?php
// Ambil konten dan hapus buffer
$html = ob_get_clean();

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// (Opsional) Set ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF ke browser
$dompdf->stream("summary.pdf", array("Attachment" => false));

// Setelah PDF dihasilkan, kirim email
$mail = new PHPMailer(true);

try {
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Ganti dengan host SMTP Anda
    $mail->SMTPAuth = true;
    $mail->Username = 'farrelfarhan902@example.com'; // Ganti dengan email Anda
    $mail->Password = 'qxlycxwqwxlnkjut'; // Ganti dengan password email Anda
    $mail->SMTPSecure = 'ssl'; // atau 'ssl'
    $mail->Port = 465; // Ganti dengan port yang sesuai

    // Pengaturan email
    $mail->setFrom('farrelfarhan902@gmail.com', 'HaloWin'); // Ganti dengan email dan nama Anda

    // Fetch email dari database
    $emailQuery = "SELECT user_email FROM tb_user WHERE user_id = $userId"; // Ganti dengan kondisi yang sesuai
    $emailResult = $conn->query($emailQuery);
    $recipientEmail = $tb_user['user_email'];

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

    $mail->addAddress($recipientEmail); // Menggunakan email yang diambil dari database
    $mail->Subject = 'PDF Document';
    $mail->Body = 'Please find the attached PDF document.';

    // Lampirkan PDF yang dihasilkan
    $mail->addAttachment('summary.pdf'); // Ganti dengan path ke file PDF yang dihasilkan

    $mail->send();
    echo 'PDF has been generated and email has been sent';
} catch (Exception $e) {
    echo "PDF could not be generated or email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>