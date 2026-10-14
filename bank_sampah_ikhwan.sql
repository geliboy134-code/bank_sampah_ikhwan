-- phpMyAdmin SQL Dump - FIXED VERSION
-- Bank Sampah Ikhwan

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- Database: `bank_sampah_ikhwan`

CREATE TABLE `sampah` (
  `Id_sampah` int(11) NOT NULL,
  `Jenis_sampah` varchar(100) NOT NULL,
  `Harga_per_kg` decimal(10,2) NOT NULL,
  `Stok_kg` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `setor_pengepul` (
  `id_setor` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `id_pengepul` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `berat_kg` decimal(10,2) NOT NULL,
  `total_rp` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tarik_saldo` (
  `id_tarik` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `Nominal` decimal(15,2) NOT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Diproses',
  `Tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `Tipe` varchar(50) NOT NULL,
  `berat_kg` decimal(10,2) NOT NULL,
  `total_rp` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `Status` enum('pending','diverifikasi','ditolak') NOT NULL DEFAULT 'pending',
  `id_admin_verifikasi` int(11) DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `Nama` varchar(100) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('admin','guru','siswa','pengepul') NOT NULL,
  `Saldo` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- !! FIX UTAMA: Role sebelumnya kosong ('') untuk semua user !!
INSERT INTO `users` (`id_user`, `Nama`, `Username`, `Password`, `Role`, `Saldo`) VALUES
(1, 'adminnistrator', 'admin',    'admin123', 'admin',    1000000.00),
(2, 'ikhwan',         'ikhwan',   'ikhwan',   'siswa',    500000.00),
(3, 'guru',           'guru',     'guru',     'guru',     200000.00),
(4, 'siswa',          'siswa',    'siswa',    'siswa',    0.00),
(5, 'pengepul',       'pengepul', 'pengepul', 'pengepul', 60000.00);

INSERT INTO `tarik_saldo` (`id_tarik`, `id_user`, `Nominal`, `Status`, `Tanggal`) VALUES
(1, 2, 100.00, 'Diproses', '2026-09-23');

-- Indexes
ALTER TABLE `sampah`       ADD PRIMARY KEY (`Id_sampah`);
ALTER TABLE `setor_pengepul` ADD PRIMARY KEY (`id_setor`),
  ADD KEY `id_sampah` (`id_sampah`), ADD KEY `id_pengepul` (`id_pengepul`), ADD KEY `id_admin` (`id_admin`);
ALTER TABLE `tarik_saldo`  ADD PRIMARY KEY (`id_tarik`), ADD KEY `id_user` (`id_user`);
ALTER TABLE `transaksi`    ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`), ADD KEY `id_sampah` (`id_sampah`), ADD KEY `transaksi_ibfk_3` (`id_admin_verifikasi`);
ALTER TABLE `users`        ADD PRIMARY KEY (`id_user`), ADD UNIQUE KEY `Username` (`Username`);

-- AUTO_INCREMENT
ALTER TABLE `sampah`         MODIFY `Id_sampah`    int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `setor_pengepul` MODIFY `id_setor`     int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tarik_saldo`    MODIFY `id_tarik`     int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `transaksi`      MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`          MODIFY `id_user`      int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- Foreign Keys
ALTER TABLE `setor_pengepul`
  ADD CONSTRAINT `setor_pengepul_ibfk_1` FOREIGN KEY (`id_sampah`)   REFERENCES `sampah` (`Id_sampah`) ON DELETE CASCADE,
  ADD CONSTRAINT `setor_pengepul_ibfk_2` FOREIGN KEY (`id_pengepul`) REFERENCES `users`  (`id_user`)   ON DELETE CASCADE,
  ADD CONSTRAINT `setor_pengepul_ibfk_3` FOREIGN KEY (`id_admin`)    REFERENCES `users`  (`id_user`)   ON DELETE CASCADE;

ALTER TABLE `tarik_saldo`
  ADD CONSTRAINT `tarik_saldo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`)              REFERENCES `users`  (`id_user`)   ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_sampah`)            REFERENCES `sampah` (`Id_sampah`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`id_admin_verifikasi`)  REFERENCES `users`  (`id_user`)   ON DELETE SET NULL;

COMMIT;
