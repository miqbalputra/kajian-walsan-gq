-- --------------------------------------------------------
-- SIMPLIFIED IMPORT FILE
-- --------------------------------------------------------

-- Pastikan menggunakan database yang benar
USE `kajian-walsan-db`;

-- Matikan cek foreign key sementara agar tidak error saat import
SET FOREIGN_KEY_CHECKS=0;

-- Hapus tabel lama jika sudah ada agar bisa dibuat ulang dengan struktur baru
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `migrations`;

-- Tabel Users
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data Admin (Ganti password ini segera setelah login!)
REPLACE INTO `users` (`id`, `name`, `email`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Admin Kajian', 'admin@kajian.id', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Tabel Migrations
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kembalikan cek foreign key
SET FOREIGN_KEY_CHECKS=1;
