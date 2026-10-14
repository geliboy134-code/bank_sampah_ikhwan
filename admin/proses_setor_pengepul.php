<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_admin    = $_SESSION['user']['id_user'];
    $id_pengepul = intval($_POST['id_pengepul']);
    $id_sampah   = intval($_POST['id_sampah']);
    $berat_kg    = floatval($_POST['berat_kg']);
    $tanggal     = date('Y-m-d');

    $q_sampah = mysqli_query($koneksi,
        "SELECT Harga_per_kg, Stok_kg FROM sampah WHERE Id_sampah = '$id_sampah'");
    $sampah = mysqli_fetch_assoc($q_sampah);

    if ($sampah && $sampah['Stok_kg'] >= $berat_kg && $berat_kg > 0) {
        $total_rp = $berat_kg * $sampah['Harga_per_kg'];

        mysqli_query($koneksi,
            "INSERT INTO setor_pengepul (id_sampah, id_pengepul, id_admin, berat_kg, total_rp, tanggal)
             VALUES ('$id_sampah', '$id_pengepul', '$id_admin', '$berat_kg', '$total_rp', '$tanggal')");

        mysqli_query($koneksi,
            "UPDATE sampah SET Stok_kg = Stok_kg - $berat_kg WHERE Id_sampah = '$id_sampah'");

        header("Location: dashboard.php?status=pengepul_success");
        exit;
    } else {
        header("Location: dashboard.php?status=stok_kurang");
        exit;
    }
}

header("Location: dashboard.php");
exit;
