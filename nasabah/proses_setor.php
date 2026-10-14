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

$id_user   = $_SESSION['user']['id_user'];
$id_sampah = intval($_POST['id_sampah']);
$berat_kg  = floatval($_POST['berat_kg']);
$tanggal   = date('Y-m-d');

if ($berat_kg <= 0 || $id_sampah <= 0) {
    header("Location: dashboard.php?status=error");
    exit;
}

$q_harga = mysqli_query($koneksi, "SELECT Harga_per_kg FROM sampah WHERE Id_sampah = '$id_sampah'");
if ($res = mysqli_fetch_assoc($q_harga)) {
    $harga_per_kg = $res['Harga_per_kg'];
    $total_rp     = $berat_kg * $harga_per_kg;

    $query_insert = "INSERT INTO transaksi (id_user, id_sampah, Tipe, berat_kg, total_rp, tanggal, Status)
                     VALUES ('$id_user', '$id_sampah', 'setor', '$berat_kg', '$total_rp', '$tanggal', 'pending')";

    if (mysqli_query($koneksi, $query_insert)) {
        header("Location: dashboard.php?status=success");
        exit;
    }
}

header("Location: dashboard.php?status=error");
exit;
