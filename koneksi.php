<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "bank_sampah_ikhwan";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>