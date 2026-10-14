<?php
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id  = intval($_GET['id']);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi = $id"));

if (!$row) {
    header("Location: index.php?msg=Data tidak ditemukan");
    exit;
}

mysqli_begin_transaction($conn);
try {
    $del = mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = $id");
    if (!$del) throw new Exception(mysqli_error($conn));

    // Kembalikan efek transaksi (rollback stok & saldo)
    if ($row['Tipe'] === 'Setor') {
        $u1 = mysqli_query($conn, "UPDATE sampah SET Stok_kg = Stok_kg - {$row['berat_kg']} WHERE Id_sampah = {$row['id_sampah']}");
        $u2 = mysqli_query($conn, "UPDATE users SET Saldo = Saldo - {$row['total_rp']} WHERE id_user = {$row['id_user']}");
    } else {
        $u1 = mysqli_query($conn, "UPDATE sampah SET Stok_kg = Stok_kg + {$row['berat_kg']} WHERE Id_sampah = {$row['id_sampah']}");
        $u2 = true;
    }
    if (!$u1 || !$u2) throw new Exception(mysqli_error($conn));

    mysqli_commit($conn);
    header("Location: index.php?msg=Transaksi dihapus dan data terkait dikembalikan");
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: index.php?msg=" . urlencode("Gagal menghapus: " . $e->getMessage()));
}
exit;
?>
