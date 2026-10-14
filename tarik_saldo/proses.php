<?php
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
    header("Location: index.php");
    exit;
}

$id   = intval($_GET['id']);
$aksi = $_GET['aksi'];

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tarik_saldo WHERE id_tarik = $id"));

if (!$row || $row['Status'] !== 'Diproses') {
    header("Location: index.php?msg=Data tidak ditemukan atau sudah diproses");
    exit;
}

mysqli_begin_transaction($conn);

try {
    if ($aksi === 'setuju') {
        // Kurangi saldo nasabah
        $update1 = mysqli_query($conn,
            "UPDATE users SET Saldo = Saldo - {$row['Nominal']} WHERE id_user = {$row['id_user']}");

        $update2 = mysqli_query($conn,
            "UPDATE tarik_saldo SET Status = 'Disetujui' WHERE id_tarik = $id");

        if (!$update1 || !$update2) {
            throw new Exception(mysqli_error($conn));
        }
        $msg = "Penarikan disetujui dan saldo telah dipotong";

    } elseif ($aksi === 'tolak') {
        $update = mysqli_query($conn,
            "UPDATE tarik_saldo SET Status = 'Ditolak' WHERE id_tarik = $id");

        if (!$update) {
            throw new Exception(mysqli_error($conn));
        }
        $msg = "Penarikan ditolak";

    } else {
        throw new Exception("Aksi tidak dikenal");
    }

    mysqli_commit($conn);
    header("Location: index.php?msg=" . urlencode($msg));

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: index.php?msg=" . urlencode("Gagal memproses: " . $e->getMessage()));
}
exit;
?>
