<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

if (!in_array($_SESSION['user']['Role'], ['siswa', 'guru'])) {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$nominal = floatval($_POST['nominal']);
$tanggal = date('Y-m-d');

if ($nominal <= 0) {
    header("Location: dashboard.php?status=error");
    exit;
}

$q_user = mysqli_query($koneksi, "SELECT Saldo FROM users WHERE id_user = '$id_user'");
$user   = mysqli_fetch_assoc($q_user);

if ($user['Saldo'] >= $nominal) {
    $query_insert = "INSERT INTO tarik_saldo (id_user, Nominal, Status, Tanggal)
                     VALUES ('$id_user', '$nominal', 'Diproses', '$tanggal')";

    if (mysqli_query($koneksi, $query_insert)) {
        header("Location: dashboard.php?status=success");
        exit;
    }
    header("Location: dashboard.php?status=error");
    exit;
} else {
    header("Location: dashboard.php?status=saldo_kurang");
    exit;
}
