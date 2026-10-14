<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'pengepul') {
    header("Location: ../index.php");
    exit;
}

$id_pengepul = $_SESSION['user']['id_user'];

$q_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_pengepul'");
$data_pengepul = mysqli_fetch_assoc($q_user);

$q_riwayat = mysqli_query($koneksi,
    "SELECT sp.*, s.Jenis_sampah, u.Nama as Nama_Admin
     FROM setor_pengepul sp
     JOIN sampah s ON sp.id_sampah = s.Id_sampah
     JOIN users u ON sp.id_admin = u.id_user
     WHERE sp.id_pengepul = '$id_pengepul'
     ORDER BY sp.id_setor DESC");

$q_stat = mysqli_query($koneksi,
    "SELECT SUM(berat_kg) as total_berat, SUM(total_rp) as total_bayar
     FROM setor_pengepul WHERE id_pengepul = '$id_pengepul'");
$stat = mysqli_fetch_assoc($q_stat);

include __DIR__ . '/../layout/header.php';
?>

<!-- Header Pengepul -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-futuristic p-4"
             style="background: linear-gradient(135deg, #059669 0%, #064e3b 100%); color: white; border-radius: 16px;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-white text-success rounded-pill px-3 py-2 mb-2">
                        <i class="bi bi-truck me-1"></i> PENGEPUL
                    </span>
                    <h2 class="fw-bold mb-1">Selamat Datang, <?= htmlspecialchars($data_pengepul['Nama']); ?>!</h2>
                    <p class="mb-0 opacity-75">Pantau riwayat penerimaan pasokan sampah dari Bank Sampah.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <small class="d-block opacity-75">Total Pengeluaran / Pembayaran</small>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($stat['total_bayar'] ?? 0, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-futuristic p-3 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Total Sampah Diterima</small>
                    <h3 class="fw-bold text-success mb-0"><?= number_format($stat['total_berat'] ?? 0, 2, ',', '.'); ?> kg</h3>
                </div>
                <i class="bi bi-box-seam fs-1 text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-futuristic p-3 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Total Transaksi Masuk</small>
                    <h3 class="fw-bold text-primary mb-0"><?= mysqli_num_rows($q_riwayat); ?> Transaksi</h3>
                </div>
                <i class="bi bi-receipt fs-1 text-primary opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="card card-futuristic p-4">
    <h5 class="fw-bold text-eco mb-3">
        <i class="bi bi-journal-text me-2"></i>Riwayat Penerimaan Sampah dari Bank Sampah
    </h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Admin Petugas</th><th>Jenis Sampah</th>
                    <th>Berat (Kg)</th><th>Total Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($q_riwayat, 0);
                if (mysqli_num_rows($q_riwayat) > 0):
                    while ($row = mysqli_fetch_assoc($q_riwayat)):
                ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="fw-semibold text-secondary"><?= htmlspecialchars($row['Nama_Admin']); ?></td>
                        <td class="fw-bold text-success"><?= htmlspecialchars($row['Jenis_sampah']); ?></td>
                        <td>
                            <span class="badge bg-light text-dark border fs-6">
                                <?= number_format($row['berat_kg'], 2, ',', '.'); ?> kg
                            </span>
                        </td>
                        <td class="fw-bold text-success">Rp <?= number_format($row['total_rp'], 0, ',', '.'); ?></td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat penyetoran sampah dari admin.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
