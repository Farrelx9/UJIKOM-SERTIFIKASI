<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi | HaloWin</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
</head>

<body id="bg-login">
    <div class="box-login">
        <h2>Registrasi</h2>
        <form action="" method="POST">
            <input type="text" name="new_user" placeholder="Username" class="input-control" required>
            <input type="password" name="new_pass" placeholder="Password" class="input-control" required>
            <input type="text" name="user_email" placeholder="Email" class="input-control" required>
            <input type="text" name="user_telp" placeholder="Telepon" class="input-control" required>
            <input type="text" name="user_alamat" placeholder="Alamat" class="input-control" required>
            <input type="submit" name="register" value="Registrasi" class="btn">
        </form>
        <?php
        if (isset($_POST['register'])) {
            include 'db.php';
            $new_user = mysqli_real_escape_string($conn, $_POST['new_user']);
            $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass']);
            $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
            $user_telp = mysqli_real_escape_string($conn, $_POST['user_telp']);
            $user_alamat = mysqli_real_escape_string($conn, $_POST['user_alamat']);
            $hashed_pass = MD5($new_pass);

            // Cek apakah username sudah ada
            $check_user = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$new_user'");
            if (mysqli_num_rows($check_user) == 0) {
                // Insert user baru
                $insert = mysqli_query($conn, "INSERT INTO tb_user (username, password, user_email, user_telp, user_addres) VALUES ('$new_user', '$hashed_pass', '$user_email', '$user_telp', '$user_addres')");
                if ($insert) {
                    echo '<script>alert("Registrasi berhasil!"); window.location.href="index.php";</script>';
                } else {
                    echo '<script>alert("Registrasi gagal!")</script>';
                }
            } else {
                echo '<script>alert("Username sudah terdaftar!")</script>';
            }
        }
        ?>
    </div>
</body>

</html>