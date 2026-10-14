<?php
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$isAdmin = $_SESSION['role'] === 'admin';

if ($isAdmin) {
    $query = "SELECT t.*, u.Nama, u.Username
              FROM tarik_saldo t
              JOIN users u ON u.id_user = t.id_user
              ORDER BY t.Tanggal DESC, t.id_tarik DESC";
} else {
    $idUser = intval($_SESSION['id_user']);
    $query = "SELECT t.*, u.Nama, u.Username
              FROM tarik_saldo t
              JOIN users u ON u.id_user = t.id_user
              WHERE t.id_user = $idUser
              ORDER BY t.Tanggal DESC, t.id_tarik DESC";
}

$result = mysqli_query($conn, $query);

function badgeClass($status) {
    return match ($status) {
        'Disetujui' => 'badge-moss',
        'Ditolak'   => 'badge-clay',
        default     => 'badge-rupiah',
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penarikan Saldo — Bank Sampah Ikhwan</title>
    <style>
/* ==========================================================================
   Bank Sampah Ikhwan — Design System
   Tema: kertas kraft, daun, dan nilai dari sampah yang ditabung.
   ========================================================================== */

@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap');

:root {
    --canopy: #223828;
    --canopy-dark: #16211A;
    --moss: #6B8E4E;
    --moss-dark: #55703D;
    --husk: #F2ECDC;
    --husk-light: #FAF7EE;
    --rupiah: #C68A33;
    --rupiah-dark: #A8721F;
    --clay: #A64B3C;
    --clay-dark: #8A3C2F;
    --ink: #16211A;
    --ink-soft: #4B584A;
    --line: #DED4B8;

    --font-display: 'Fraunces', Georgia, serif;
    --font-body: 'Work Sans', -apple-system, sans-serif;

    --radius: 10px;
    --radius-lg: 16px;
}

* { box-sizing: border-box; }

html, body {
    margin: 0;
    padding: 0;
}

body {
    background: var(--husk);
    color: var(--ink);
    font-family: var(--font-body);
    font-size: 16px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

h1, h2, h3, h4, h5 {
    font-family: var(--font-display);
    color: var(--canopy-dark);
    font-weight: 600;
    line-height: 1.15;
    margin: 0 0 0.4em;
}

h3 { font-size: 1.7rem; }
h4 { font-size: 1.3rem; }

p { margin: 0 0 1em; color: var(--ink-soft); }

a { color: var(--moss-dark); }

:focus-visible {
    outline: 3px solid var(--rupiah);
    outline-offset: 2px;
}

/* ---------- Layout shells ---------- */

.page {
    max-width: 1040px;
    margin: 0 auto;
    padding: 0 24px 64px;
}

.topbar {
    background: var(--canopy);
    color: #fff;
    padding: 18px 0;
    margin-bottom: 40px;
}

.topbar-inner {
    max-width: 1040px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.brand {
    font-family: var(--font-display);
    font-size: 1.35rem;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    letter-spacing: 0.2px;
}

.brand-sub {
    display: block;
    font-family: var(--font-body);
    font-size: 0.72rem;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    margin-top: -2px;
}

.who {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 0.92rem;
    color: rgba(255,255,255,0.9);
}

.who .role-pill {
    background: rgba(255,255,255,0.12);
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 0.78rem;
}

.page-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
}

.back-link {
    display: inline-block;
    margin-bottom: 18px;
    font-size: 0.9rem;
    color: var(--ink-soft);
    text-decoration: none;
    border-bottom: 1px solid var(--line);
    padding-bottom: 1px;
}
.back-link:hover { color: var(--canopy); border-color: var(--canopy); }

/* ---------- Buttons ---------- */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-family: var(--font-body);
    font-weight: 600;
    font-size: 0.94rem;
    padding: 10px 20px;
    border-radius: var(--radius);
    border: 1.5px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    line-height: 1.2;
}

.btn-primary { background: var(--rupiah); color: #fff; }
.btn-primary:hover { background: var(--rupiah-dark); }

.btn-secondary { background: transparent; color: var(--ink); border-color: var(--line); }
.btn-secondary:hover { border-color: var(--ink-soft); }

.btn-success { background: var(--moss); color: #fff; }
.btn-success:hover { background: var(--moss-dark); }

.btn-danger { background: transparent; color: var(--clay); border-color: var(--clay); }
.btn-danger:hover { background: var(--clay); color: #fff; }

.btn-ghost-light { background: transparent; color: #fff; border-color: rgba(255,255,255,0.5); }
.btn-ghost-light:hover { background: rgba(255,255,255,0.12); }

.btn-sm { padding: 6px 14px; font-size: 0.83rem; }

.btn-block { width: 100%; }

/* ---------- Cards / tiles ---------- */

.tile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 18px;
    margin-top: 8px;
}

.tile {
    background: var(--husk-light);
    border: 1px solid var(--line);
    border-left: 4px solid var(--moss);
    border-radius: var(--radius);
    padding: 22px;
}

.tile.tile-rupiah { border-left-color: var(--rupiah); }

.tile h4 { margin-bottom: 6px; }
.tile p { font-size: 0.92rem; margin-bottom: 16px; }

.card {
    background: var(--husk-light);
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 32px;
}

/* ---------- Hero / stat (dashboard saldo) ---------- */

.hero {
    margin-bottom: 32px;
}

.hero-eyebrow {
    color: var(--ink-soft);
    font-size: 1.05rem;
    margin-bottom: 4px;
}

.hero h1 {
    font-size: 2.1rem;
    margin-bottom: 18px;
}

.stat-block {
    background: var(--canopy);
    color: #fff;
    border-radius: var(--radius-lg);
    padding: 26px 30px;
    display: flex;
    align-items: baseline;
    gap: 14px;
    flex-wrap: wrap;
}

.stat-label {
    font-size: 0.92rem;
    color: rgba(255,255,255,0.7);
}

.stat-value {
    font-family: var(--font-display);
    font-size: 2.6rem;
    font-weight: 600;
    color: #fff;
}

/* ---------- Alerts ---------- */

.alert {
    padding: 13px 18px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    font-size: 0.94rem;
    border: 1px solid transparent;
}

.alert-success { background: #EAF1E1; border-color: #CFE0BD; color: var(--moss-dark); }
.alert-danger  { background: #F5E7E3; border-color: #E3C1B8; color: var(--clay-dark); }

/* ---------- Table ---------- */

.table-wrap {
    background: var(--husk-light);
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

table.data {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.94rem;
}

table.data thead th {
    text-align: left;
    background: var(--canopy);
    color: #fff;
    font-weight: 600;
    font-size: 0.82rem;
    padding: 13px 18px;
}

table.data tbody td {
    padding: 13px 18px;
    border-top: 1px solid var(--line);
    color: var(--ink);
}

table.data tbody tr:hover { background: rgba(107, 142, 78, 0.06); }

.money { color: var(--rupiah-dark); font-weight: 600; }

.empty-row td {
    text-align: center;
    padding: 34px 18px;
    color: var(--ink-soft);
}

/* ---------- Badges (status pills) ---------- */

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.78rem;
    font-weight: 600;
}

.badge-moss   { background: #E2EBD8; color: var(--moss-dark); }
.badge-rupiah { background: #F1E3C8; color: var(--rupiah-dark); }
.badge-clay   { background: #F0DAD4; color: var(--clay-dark); }
.badge-muted  { background: #E7E2D2; color: var(--ink-soft); }

/* ---------- Forms ---------- */

.form-shell {
    max-width: 460px;
    margin: 0 auto;
}

.field { margin-bottom: 18px; }

.field label {
    display: block;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 6px;
}

.field input,
.field select {
    width: 100%;
    font-family: var(--font-body);
    font-size: 0.96rem;
    padding: 11px 14px;
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    background: #fff;
    color: var(--ink);
}

.field input:focus,
.field select:focus {
    border-color: var(--moss);
    outline: none;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 26px;
}

.helper-text {
    font-size: 0.85rem;
    color: var(--ink-soft);
    margin-top: -8px;
    margin-bottom: 18px;
}

/* ---------- Login split screen ---------- */

.login-shell {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
}

.login-side {
    background: var(--canopy);
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
}

.login-side .brand { font-size: 1.6rem; margin-bottom: 28px; display: inline-block; }

.login-side h1 {
    color: #fff;
    font-size: 2.4rem;
    max-width: 380px;
    margin-bottom: 16px;
}

.login-side p {
    color: rgba(255,255,255,0.72);
    max-width: 340px;
}

.leaf-mark { margin-bottom: 20px; }

.login-form-side {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    background: var(--husk);
}

.login-card {
    width: 100%;
    max-width: 380px;
}

.login-card h3 { margin-bottom: 4px; }
.login-card .helper-text { margin-bottom: 26px; margin-top: 2px; }

@media (max-width: 820px) {
    .login-shell { grid-template-columns: 1fr; }
    .login-side { padding: 40px 30px; min-height: 240px; }
    .login-side h1 { font-size: 1.7rem; }
}

@media (max-width: 600px) {
    .topbar-inner { flex-direction: column; align-items: flex-start; }
    .stat-value { font-size: 2rem; }
    .page { padding: 0 16px 48px; }
    table.data { font-size: 0.86rem; }
    table.data thead th, table.data tbody td { padding: 10px 12px; }
}

</style>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a href="../index.php" class="brand">Bank Sampah Ikhwan</a>
        <div class="who">
            <span><?= htmlspecialchars($_SESSION['Nama']) ?> <span class="role-pill"><?= htmlspecialchars($_SESSION['role']) ?></span></span>
            <a href="../logout.php" class="btn btn-ghost-light btn-sm">Keluar</a>
        </div>
    </div>
</header>

<div class="page">
    <a href="../index.php" class="back-link">&larr; Kembali ke beranda</a>

    <div class="page-head">
        <h3>Penarikan Saldo</h3>
        <?php if (!$isAdmin): ?>
            <a href="tambah.php" class="btn btn-primary">Ajukan penarikan</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?php if ($isAdmin): ?><th>Nasabah</th><?php endif; ?>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <?php if ($isAdmin): ?><th>Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <?php if ($isAdmin): ?>
                        <td><?= htmlspecialchars($row['Nama']) ?> <span style="color:var(--ink-soft)">(<?= htmlspecialchars($row['Username']) ?>)</span></td>
                    <?php endif; ?>
                    <td class="money">Rp <?= number_format($row['Nominal'], 2, ',', '.') ?></td>
                    <td><span class="badge <?= badgeClass($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></span></td>
                    <td><?= $row['Tanggal'] ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <?php if ($row['Status'] === 'Diproses'): ?>
                            <a href="proses.php?id=<?= $row['id_tarik'] ?>&aksi=setuju"
                               class="btn btn-success btn-sm"
                               onclick="return confirm('Setujui penarikan ini?')">Setujui</a>
                            <a href="proses.php?id=<?= $row['id_tarik'] ?>&aksi=tolak"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tolak penarikan ini?')">Tolak</a>
                        <?php else: ?>
                            <a href="hapus.php?id=<?= $row['id_tarik'] ?>"
                               class="btn btn-secondary btn-sm"
                               onclick="return confirm('Hapus data ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($result) === 0): ?>
                <tr class="empty-row"><td colspan="<?= $isAdmin ? 5 : 3 ?>">Belum ada pengajuan penarikan saldo.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
