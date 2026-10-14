<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$isAdmin = $_SESSION['user']['Role'] === 'admin';

$result = mysqli_query($koneksi, "SELECT * FROM sampah ORDER BY Id_sampah ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jenis Sampah — Bank Sampah Ikhwan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .bg-eco { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
        .text-eco { color: #047857; }
        .btn-eco { background-color: #10b981; color: white; border-radius: 10px; border: none; }
        .btn-eco:hover { background-color: #059669; color: white; }
        .card-futuristic { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-eco mb-4 shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php"><i class="bi bi-recycle me-2"></i>Bank Sampah Ikhwan</a>
        <div class="ms-auto d-flex align-items-center gap-3 text-white small">
            <span><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']['Nama']); ?> (<strong><?= ucfirst($_SESSION['user']['Role']); ?></strong>)</span>
            <a href="../logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../<?= $_SESSION['user']['Role'] === 'admin' ? 'admin' : 'nasabah'; ?>/dashboard.php" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <h4 class="fw-bold text-dark mt-1 mb-0">Data Jenis Sampah</h4>
        </div>
        <?php if ($isAdmin): ?>
            <a href="tambah.php" class="btn btn-eco px-4"><i class="bi bi-plus-lg me-1"></i> Tambah Sampah</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card card-futuristic p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Sampah</th>
                        <th>Harga per Kg</th>
                        <th>Stok (Kg)</th>
                        <?php if ($isAdmin): ?><th class="text-center">Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($row['Jenis_sampah']); ?></td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['Harga_per_kg'], 0, ',', '.'); ?></td>
                                <td><?= number_format($row['Stok_kg'], 2, ',', '.'); ?> kg</td>
                                <?php if ($isAdmin): ?>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?= $row['Id_sampah']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                            <i class="bi bi-pencil"></i> Ubah
                                        </a>
                                        <a href="hapus.php?id=<?= $row['Id_sampah']; ?>"
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                           onclick="return confirm('Hapus jenis sampah ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= $isAdmin ? 4 : 3; ?>" class="text-center text-muted py-4">Belum ada jenis sampah yang tercatat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
