<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
    header("Location: dashboard.php");
    exit;
}

$id_tarik = intval($_GET['id']);
$aksi     = $_GET['aksi'];

if ($aksi === 'terima') {
    $q_tarik = mysqli_query($koneksi,
        "SELECT * FROM tarik_saldo WHERE id_tarik = '$id_tarik' AND Status IN ('pending', 'Diproses')");

    if ($t = mysqli_fetch_assoc($q_tarik)) {
        $id_user = $t['id_user'];
        $nominal = $t['Nominal'];

        $q_user = mysqli_query($koneksi,
            "SELECT Saldo FROM users WHERE id_user = '$id_user'");
        $u = mysqli_fetch_assoc($q_user);

        if ($u['Saldo'] >= $nominal) {
            mysqli_query($koneksi,
                "UPDATE tarik_saldo SET Status = 'Selesai' WHERE id_tarik = '$id_tarik'");
            mysqli_query($koneksi,
                "UPDATE users SET Saldo = Saldo - $nominal WHERE id_user = '$id_user'");
        }
    }
} elseif ($aksi === 'tolak') {
    mysqli_query($koneksi,
        "UPDATE tarik_saldo SET Status = 'Ditolak' WHERE id_tarik = '$id_tarik'");
}

header("Location: dashboard.php?status=verif_success");
exit;
