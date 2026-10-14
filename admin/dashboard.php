<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$total_pending_setor = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE Status = 'pending'"))['total'];
$total_pending_tarik = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tarik_saldo WHERE Status IN ('pending', 'Diproses')"))['total'];
$total_user          = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE Role IN ('siswa', 'guru')"))['total'];

$q_setor_pending = mysqli_query($koneksi,
    "SELECT t.*, u.Nama, s.Jenis_sampah
     FROM transaksi t
     JOIN users u ON t.id_user = u.id_user
     JOIN sampah s ON t.id_sampah = s.Id_sampah
     WHERE t.Status = 'pending'
     ORDER BY t.id_transaksi ASC");

$q_tarik_pending = mysqli_query($koneksi,
    "SELECT ts.*, u.Nama, u.Saldo as Saldo_Sekarang
     FROM tarik_saldo ts
     JOIN users u ON ts.id_user = u.id_user
     WHERE ts.Status IN ('pending', 'Diproses')
     ORDER BY ts.id_tarik ASC");

$q_sampah  = mysqli_query($koneksi, "SELECT * FROM sampah ORDER BY Jenis_sampah ASC");
$q_pengepul = mysqli_query($koneksi, "SELECT * FROM users WHERE Role = 'pengepul'");

include __DIR__ . '/../layout/header.php';
?>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'verif_success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Transaksi berhasil diverifikasi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] == 'pengepul_success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-truck me-2"></i> Penyetoran ke pengepul berhasil dicatat!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] == 'stok_kurang'): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Stok sampah tidak mencukupi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Cards Dashboard -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-futuristic p-3 border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Setoran Sampah Pending</small>
                    <h3 class="fw-bold text-warning mb-0"><?= $total_pending_setor; ?> Transaksi</h3>
                </div>
                <i class="bi bi-hourglass-split fs-1 text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-futuristic p-3 border-start border-info border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Tarik Saldo Pending</small>
                    <h3 class="fw-bold text-info mb-0"><?= $total_pending_tarik; ?> Pengajuan</h3>
                </div>
                <i class="bi bi-wallet2 fs-1 text-info opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-futuristic p-3 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Total Nasabah (Siswa/Guru)</small>
                    <h3 class="fw-bold text-success mb-0"><?= $total_user; ?> User</h3>
                </div>
                <i class="bi bi-people fs-1 text-success opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Verifikasi Setor Sampah -->
<div class="card card-futuristic p-4 mb-4">
    <h5 class="fw-bold text-eco mb-3"><i class="bi bi-check2-square me-2"></i>Verifikasi Setoran Sampah Siswa/Guru</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Nama User</th><th>Jenis Sampah</th>
                    <th>Berat</th><th>Total (Rp)</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($q_setor_pending) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($q_setor_pending)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['Nama']); ?></td>
                            <td><?= htmlspecialchars($row['Jenis_sampah']); ?></td>
                            <td><?= number_format($row['berat_kg'], 2, ',', '.'); ?> kg</td>
                            <td class="fw-bold text-success">Rp <?= number_format($row['total_rp'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <a href="verifikasi_setor.php?id=<?= $row['id_transaksi']; ?>&aksi=terima"
                                   class="btn btn-success btn-sm rounded-pill px-3 me-1"
                                   onclick="return confirm('Verifikasi setoran ini?')">
                                    <i class="bi bi-check-lg"></i> Terima
                                </a>
                                <a href="verifikasi_setor.php?id=<?= $row['id_transaksi']; ?>&aksi=tolak"
                                   class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                   onclick="return confirm('Tolak setoran ini?')">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada antrean verifikasi setoran sampah.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Verifikasi Tarik Saldo -->
<div class="card card-futuristic p-4 mb-4">
    <h5 class="fw-bold text-eco mb-3"><i class="bi bi-cash-stack me-2"></i>Verifikasi Penarikan Saldo</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Nama User</th><th>Saldo Saat Ini</th>
                    <th>Nominal Penarikan</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($q_tarik_pending) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($q_tarik_pending)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['Tanggal'])); ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['Nama']); ?></td>
                            <td>Rp <?= number_format($row['Saldo_Sekarang'], 0, ',', '.'); ?></td>
                            <td class="fw-bold text-danger">Rp <?= number_format($row['Nominal'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <a href="verifikasi_tarik.php?id=<?= $row['id_tarik']; ?>&aksi=terima"
                                   class="btn btn-success btn-sm rounded-pill px-3 me-1"
                                   onclick="return confirm('Cairkan saldo ini?')">
                                    <i class="bi bi-check-lg"></i> Setujui
                                </a>
                                <a href="verifikasi_tarik.php?id=<?= $row['id_tarik']; ?>&aksi=tolak"
                                   class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                   onclick="return confirm('Tolak penarikan saldo ini?')">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada antrean penarikan saldo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Setor ke Pengepul & Stok -->
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-futuristic p-4 h-100">
            <h5 class="fw-bold text-eco mb-3"><i class="bi bi-truck me-2"></i>Setor Sampah ke Pengepul</h5>
            <form action="proses_setor_pengepul.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Pilih Pengepul</label>
                    <select name="id_pengepul" class="form-select rounded-3" required>
                        <?php while ($p = mysqli_fetch_assoc($q_pengepul)): ?>
                            <option value="<?= $p['id_user']; ?>"><?= htmlspecialchars($p['Nama']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Pilih Jenis Sampah</label>
                    <select name="id_sampah" class="form-select rounded-3" required>
                        <?php
                        mysqli_data_seek($q_sampah, 0);
                        while ($s = mysqli_fetch_assoc($q_sampah)):
                        ?>
                            <option value="<?= $s['Id_sampah']; ?>">
                                <?= htmlspecialchars($s['Jenis_sampah']); ?>
                                (Stok: <?= number_format($s['Stok_kg'], 2, ',', '.'); ?> kg)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Berat yang Disetor (Kg)</label>
                    <input type="number" step="0.01" min="0.01" name="berat_kg" class="form-control rounded-3" placeholder="0.00" required>
                </div>
                <button type="submit" class="btn btn-eco w-100 mt-2">
                    <i class="bi bi-send-check me-1"></i> Kirim ke Pengepul
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card card-futuristic p-4 h-100">
            <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-box-seam me-2"></i>Stok Sampah di Gudang</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Jenis Sampah</th><th>Harga / Kg</th><th>Stok Tersedia</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        mysqli_data_seek($q_sampah, 0);
                        if (mysqli_num_rows($q_sampah) > 0):
                            while ($s = mysqli_fetch_assoc($q_sampah)):
                        ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($s['Jenis_sampah']); ?></td>
                                <td>Rp <?= number_format($s['Harga_per_kg'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border fs-6">
                                        <?= number_format($s['Stok_kg'], 2, ',', '.'); ?> kg
                                    </span>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data master sampah.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
