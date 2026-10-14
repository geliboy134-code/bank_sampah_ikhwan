<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query  = "SELECT * FROM users WHERE Username = '$username' AND Password = '$password' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Validasi: Role tidak boleh kosong
        if (empty($user['Role'])) {
            header("Location: index.php?pesan=gagal");
            exit;
        }

        $_SESSION['user'] = [
            'id_user'  => $user['id_user'],
            'Nama'     => $user['Nama'],
            'Username' => $user['Username'],
            'Role'     => $user['Role'],
            'Saldo'    => $user['Saldo']
        ];

        switch ($user['Role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'guru':
            case 'siswa':
                header("Location: nasabah/dashboard.php");
                break;
            case 'pengepul':
                header("Location: pengepul/dashboard.php");
                break;
            default:
                header("Location: index.php?pesan=gagal");
                break;
        }
        exit;
    } else {
        header("Location: index.php?pesan=gagal");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
