<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

if (mysqli_query($koneksi, "DELETE FROM sampah WHERE Id_sampah = $id")) {
    header("Location: index.php?msg=Data berhasil dihapus");
} else {
    header("Location: index.php?msg=" . urlencode("Gagal menghapus (sampah masih dipakai di transaksi)"));
}
exit;
