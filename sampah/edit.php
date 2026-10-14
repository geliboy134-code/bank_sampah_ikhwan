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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis_sampah']);
    $harga = floatval($_POST['harga_per_kg']);
    $stok  = floatval($_POST['stok_kg']);

    $query = "UPDATE sampah SET Jenis_sampah = '$jenis', Harga_per_kg = '$harga', Stok_kg = '$stok'
              WHERE Id_sampah = $id";

    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?msg=Data berhasil diperbarui");
        exit;
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}

$result = mysqli_query($koneksi, "SELECT * FROM sampah WHERE Id_sampah = $id");
$data   = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Jenis Sampah — Bank Sampah Ikhwan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .btn-eco { background-color: #10b981; color: white; border-radius: 10px; border: none; }
        .btn-eco:hover { background-color: #059669; color: white; }
        .card-futuristic { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 0.25rem rgba(16,185,129,0.25); }
    </style>
</head>
<body>
<nav class="navbar navbar-dark mb-4 shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php"><i class="bi bi-recycle me-2"></i>Bank Sampah Ikhwan</a>
        <a href="../logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
    </div>
</nav>

<div class="container pb-5" style="max-width: 520px;">
    <a href="index.php" class="text-muted text-decoration-none small d-block mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Sampah
    </a>

    <div class="card card-futuristic p-4">
        <h5 class="fw-bold mb-1">Ubah Jenis Sampah</h5>
        <p class="text-muted small mb-4">Perbarui harga atau stok untuk <strong><?= htmlspecialchars($data['Jenis_sampah']); ?></strong>.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Jenis Sampah</label>
                <input type="text" name="jenis_sampah" class="form-control"
                       value="<?= htmlspecialchars($data['Jenis_sampah']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Harga per Kg (Rp)</label>
                <input type="number" step="0.01" name="harga_per_kg" class="form-control"
                       value="<?= $data['Harga_per_kg']; ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Stok (Kg)</label>
                <input type="number" step="0.01" name="stok_kg" class="form-control"
                       value="<?= $data['Stok_kg']; ?>">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-eco px-4">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
