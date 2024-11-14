<?php
$hostname= '127.0.0.1';
$username= 'root';
$password= '';
$dbname= 'db_kesehatan';

$conn = mysqli_connect($hostname, $username, $password, $dbname) or die ('Gagal terhubung ke database')
?>