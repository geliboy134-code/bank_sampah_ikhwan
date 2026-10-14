<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id_transaksi = intval($_GET['id']);
$aksi         = $_GET['aksi'];
$id_admin     = $_SESSION['user']['id_user'];
$now          = date('Y-m-d H:i:s');

if ($aksi === 'terima') {
    // Ambil data transaksi
    $q_trans = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi' AND Status = 'pending'");
    if ($t = mysqli_fetch_assoc($q_trans)) {
        $id_user  = $t['id_user'];
        $id_sampah = $t['id_sampah'];
        $berat    = $t['berat_kg'];
        $total    = $t['total_rp'];

        // 1. Update status transaksi
        mysqli_query($koneksi, "UPDATE transaksi SET Status = 'diverifikasi', id_admin_verifikasi = '$id_admin', tanggal_verifikasi = '$now' WHERE id_transaksi = '$id_transaksi'");

        // 2. Tambah saldo user
        mysqli_query($koneksi, "UPDATE users SET Saldo = Saldo + $total WHERE id_user = '$id_user'");

        // 3. Tambah stok sampah
        mysqli_query($koneksi, "UPDATE sampah SET Stok_kg = Stok_kg + $berat WHERE Id_sampah = '$id_sampah'");
    }
} elseif ($aksi === 'tolak') {
    mysqli_query($koneksi, "UPDATE transaksi SET Status = 'ditolak', id_admin_verifikasi = '$id_admin', tanggal_verifikasi = '$now' WHERE id_transaksi = '$id_transaksi'");
}

header("Location: dashboard.php?status=verif_success");
exit;
?>