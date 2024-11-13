<?php
session_start();
include 'db.php';
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
}
$query = mysqli_query($conn, "SELECT * FROM tb_user WHERE user_id = '" . $_SESSION['id'] . "'");
$o = mysqli_fetch_array($query);
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
            <h1><a href="profile-user.php">HaloWin</a></h1>
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
            <h3>Profile</h3>
            <div class="box">
                <form action=" " method="POST">
                    <input type="text" name="nama" placeholder="Nama Lengkap" class="input-control"
                        value="<?php echo $o['user_name'] ?>" required>
                    <input type="text" name="user" placeholder="username" class="input-control"
                        value="<?php echo $o['username'] ?>" required>
                    <input type="text" name="hp" placeholder="No HP" class="input-control"
                        value="<?php echo $o['user_telp'] ?>" required>
                    <input type="email" name="email" placeholder="Email" class="input-control"
                        value="<?php echo $o['user_email'] ?>" required>
                    <input type="text" name="alamat" placeholder="Alamat" class="input-control"
                        value="<?php echo $o['user_addres'] ?>" required>
                    <input type="submit" name="submit" value="Update Profile" class="btn">
                </form>
                <?php
                if (isset($_POST['submit'])) {
                    $nama = ucwords($_POST['nama']);
                    $user = $_POST['user'];
                    $hp = $_POST['hp'];
                    $email = $_POST['email'];
                    $alamat = ucwords($_POST['alamat']);

                    $update = mysqli_query($conn, "UPDATE tb_user SET
                    user_name = '" . $nama . "',
                    username = '" . $user . "',
                    user_telp = '" . $hp . "', 
                    user_email = '" . $email . "',         
                    user_addres = '" . $alamat . "' WHERE user_id = '" . $_SESSION['id'] . "'");
                    if ($update) {
                        echo "<script>alert('Sukses update profile');window.location='profile-user.php'</script>";
                    } else {
                        echo "gagal";
                    }
                }
                ?>
            </div>
            <h3>Ubah Password</h3>
            <div class="box">
                <form action=" " method="POST">
                    <input type="password" name="pass1" placeholder="Password Baru" class="input-control" required>
                    <input type="password" name="pass2" placeholder="Konfirmasi Password Baru" class="input-control"
                        required>
                    <input type="submit" name="ubah_password" value="Ubah Password" class="btn">
                </form>
                <?php
                if (isset($_POST['ubah_password'])) {
                    $pass1 = $_POST['pass1'];
                    $pass2 = $_POST['pass2'];

                    if ($pass1 != $pass2) {
                        echo '<script>alert("Konfirmasi Password Baru Tidak Sesuai")</script>';
                    } else {
                        $u_pass = mysqli_query($conn, "UPDATE tb_user SET
                    password = '" . MD5($pass1) . "'         
                    WHERE user_id = '" . $o['user_id'] . "'");
                        if ($u_pass) {
                            echo '<script>alert("Sukses Update Password")</script>';
                            echo '<script>window.location="profile-user.php"</script>';
                        } else {
                            echo 'gagal' . mysqli_error($conn);
                        }
                    }
                }
                ?>
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