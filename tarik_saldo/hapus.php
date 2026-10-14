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

$id = intval($_GET['id']);

$query = "DELETE FROM tarik_saldo WHERE id_tarik = $id";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?msg=Data berhasil dihapus");
} else {
    header("Location: index.php?msg=Gagal menghapus data: " . urlencode(mysqli_error($conn)));
}
exit;
?>
