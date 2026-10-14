<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['Role'], ['siswa', 'guru'])) {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];

$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_user'");
$data_user  = mysqli_fetch_assoc($query_user);

$query_sampah    = mysqli_query($koneksi, "SELECT * FROM sampah ORDER BY Jenis_sampah ASC");
$query_transaksi = mysqli_query($koneksi,
    "SELECT t.*, s.Jenis_sampah FROM transaksi t
     JOIN sampah s ON t.id_sampah = s.Id_sampah
     WHERE t.id_user = '$id_user'
     ORDER BY t.id_transaksi DESC");
$query_tarik = mysqli_query($koneksi,
    "SELECT * FROM tarik_saldo WHERE id_user = '$id_user' ORDER BY id_tarik DESC");

include __DIR__ . '/../layout/header.php';
?>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Pengajuan berhasil dikirim! Menunggu verifikasi admin.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] == 'saldo_kurang'): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Saldo Anda tidak mencukupi untuk melakukan penarikan ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] == 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan. Silakan coba lagi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Header Saldo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-futuristic p-4 position-relative overflow-hidden"
             style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; border-radius: 16px;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-white text-success rounded-pill px-3 py-2 mb-2">
                        <i class="bi bi-person-badge me-1"></i> <?= strtoupper($data_user['Role']); ?>
                    </span>
                    <h2 class="fw-bold mb-1">Halo, <?= htmlspecialchars($data_user['Nama']); ?>!</h2>
                    <p class="mb-0 opacity-75">Mari berkontribusi menjaga lingkungan dan kumpulkan saldumu.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <small class="d-block opacity-75">Saldo Aktif Anda</small>
                    <h1 class="display-5 fw-bold mb-0">Rp <?= number_format($data_user['Saldo'], 0, ',', '.'); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Setor & Tarik -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card card-futuristic p-4 h-100">
            <h5 class="fw-bold text-eco mb-3"><i class="bi bi-box-seam me-2"></i>Setor Sampah Baru</h5>
            <!-- FIX: action="proses_setor.php" (bukan prosess_setor.php) -->
            <form action="proses_setor.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Pilih Jenis Sampah</label>
                    <select name="id_sampah" id="select_sampah" class="form-select rounded-3" required onchange="hitungTotal()">
                        <option value="" data-harga="0">-- Pilih Jenis Sampah --</option>
                        <?php while ($sampah = mysqli_fetch_assoc($query_sampah)): ?>
                            <option value="<?= $sampah['Id_sampah']; ?>" data-harga="<?= $sampah['Harga_per_kg']; ?>">
                                <?= htmlspecialchars($sampah['Jenis_sampah']); ?>
                                (Rp <?= number_format($sampah['Harga_per_kg'], 0, ',', '.'); ?> / kg)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold text-secondary">Berat Sampah (Kg)</label>
                        <input type="number" step="0.01" min="0.01" name="berat_kg" id="input_berat"
                               class="form-control rounded-3" placeholder="0.00" required oninput="hitungTotal()">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold text-secondary">Estimasi Pendapatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-success fw-bold">Rp</span>
                            <input type="text" id="estimasi_total" class="form-control bg-light fw-bold text-success" value="0" readonly>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-eco w-100 mt-2">
                    <i class="bi bi-send me-1"></i> Ajukan Setoran Sampah
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-futuristic p-4 h-100">
            <h5 class="fw-bold text-eco mb-3"><i class="bi bi-wallet2 me-2"></i>Tarik Saldo</h5>
            <form action="proses_tarik.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Nominal Penarikan (Rp)</label>
                    <input type="number" name="nominal" min="1000" max="<?= $data_user['Saldo']; ?>"
                           class="form-control rounded-3" placeholder="Masukkan nominal" required>
                    <small class="text-muted d-block mt-1">
                        Maksimal: Rp <?= number_format($data_user['Saldo'], 0, ',', '.'); ?>
                    </small>
                </div>
                <button type="submit" class="btn btn-outline-success rounded-3 w-100 py-2 mt-4"
                        <?= $data_user['Saldo'] <= 0 ? 'disabled' : ''; ?>>
                    <i class="bi bi-arrow-down-circle me-1"></i> Ajukan Penarikan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Riwayat Transaksi -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-futuristic p-4">
            <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-clock-history me-2"></i>Riwayat Setoran Sampah</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th><th>Jenis Sampah</th>
                            <th>Berat</th><th>Total (Rp)</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_transaksi) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query_transaksi)): ?>
                                <tr>
                                    <td><small><?= date('d/m/Y', strtotime($row['tanggal'])); ?></small></td>
                                    <td><?= htmlspecialchars($row['Jenis_sampah']); ?></td>
                                    <td><?= number_format($row['berat_kg'], 2, ',', '.'); ?> kg</td>
                                    <td class="fw-bold text-success">Rp <?= number_format($row['total_rp'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($row['Status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Pending</span>
                                        <?php elseif ($row['Status'] == 'diverifikasi'): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-all"></i> Diverifikasi</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada riwayat setoran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-futuristic p-4">
            <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-arrow-up-right-circle me-2"></i>Riwayat Penarikan Saldo</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Tanggal</th><th>Nominal</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_tarik) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query_tarik)): ?>
                                <tr>
                                    <td><small><?= date('d/m/Y', strtotime($row['Tanggal'])); ?></small></td>
                                    <td class="fw-bold text-danger">Rp <?= number_format($row['Nominal'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if (in_array($row['Status'], ['Diproses', 'pending'])): ?>
                                            <span class="badge bg-warning text-dark">Diproses</span>
                                        <?php elseif (in_array($row['Status'], ['Selesai', 'diverifikasi'])): ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada riwayat penarikan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function hitungTotal() {
    const select = document.getElementById('select_sampah');
    const harga  = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
    const berat  = parseFloat(document.getElementById('input_berat').value) || 0;
    const total  = Math.round(harga * berat);
    document.getElementById('estimasi_total').value = new Intl.NumberFormat('id-ID').format(total);
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
