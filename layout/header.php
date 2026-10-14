<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
$currentUser = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah Ikhwan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .bg-eco { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
        .text-eco { color: #047857; }
        .btn-eco { background-color: #10b981; color: white; border-radius: 10px; border: none; }
        .btn-eco:hover { background-color: #059669; color: white; }
        .card-futuristic {
            background: white; border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-eco mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-recycle me-2"></i>Bank Sampah Ikhwan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3 text-white small">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($currentUser['Nama']); ?>
                    (<strong><?= ucfirst($currentUser['Role']); ?></strong>)
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container pb-5">
