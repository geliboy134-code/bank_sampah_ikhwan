<?php
session_start();

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['Role'];
    if ($role === 'admin') {
        header("Location: admin/dashboard.php");
    } elseif ($role === 'pengepul') {
        header("Location: pengepul/dashboard.php");
    } else {
        // guru & siswa
        header("Location: nasabah/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Sampah Ikhwan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e6f4ea 0%, #f8fafc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }
        .btn-eco {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-eco:hover { opacity: 0.95; box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3); color: #fff; }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 1px solid #cbd5e1; }
        .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25); }
        .brand-icon {
            width: 60px; height: 60px;
            background: #d1fae5; color: #10b981;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; font-size: 1.8rem;
        }
        .input-group-text { border-radius: 10px 0 0 10px; }
        .input-group .form-control { border-radius: 0 10px 10px 0; }
    </style>
</head>
<body>
<div class="card-login">
    <div class="brand-icon"><i class="bi bi-recycle"></i></div>
    <h4 class="fw-bold text-center mb-1" style="color: #0f172a;">Bank Sampah</h4>
    <p class="text-muted text-center small mb-4">Sistem Pengelolaan Sampah Sekolah</p>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
        <div class="alert alert-danger alert-dismissible fade show text-center py-2 small mb-3" role="alert">
            Username atau Password salah!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-semibold text-secondary">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-eco">Masuk ke Akun</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
