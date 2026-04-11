-- MariaDB/MySQL Migration Script for App Kajian Walsan v2
-- Generated to be compatible with MariaDB for Coolify deployment

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Roles Table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) UNIQUE NOT NULL,
    `display_name` VARCHAR(100),
    `description` TEXT,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` BIGINT UNSIGNED,
    `name` VARCHAR(255) NOT NULL,
    `username` VARCHAR(255) UNIQUE,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `is_active` BOOLEAN DEFAULT TRUE,
    `google_id` VARCHAR(255),
    `avatar` VARCHAR(255),
    `remember_token` VARCHAR(100),
    `two_factor_secret` TEXT,
    `two_factor_recovery_codes` TEXT,
    `two_factor_confirmed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password Reset Tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` VARCHAR(255) PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions Table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) PRIMARY KEY,
    `user_id` BIGINT UNSIGNED,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache Table
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs Table
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED DEFAULT NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Academic Years
CREATE TABLE IF NOT EXISTS `academic_years` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_active` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Classes
CREATE TABLE IF NOT EXISTS `classes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `level` INT,
    `capacity` INT DEFAULT 30,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students
CREATE TABLE IF NOT EXISTS `students` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nis` VARCHAR(20) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `class_id` BIGINT UNSIGNED,
    `gender` ENUM('L', 'P'),
    `birth_date` DATE,
    `birth_place` VARCHAR(100),
    `address` TEXT,
    `photo` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Parents
CREATE TABLE IF NOT EXISTS `parents` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED,
    `type` ENUM('father', 'mother', 'guardian'),
    `occupation` VARCHAR(100),
    `education` VARCHAR(100),
    `income` VARCHAR(100),
    `address` TEXT,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Parent Student (Pivot)
CREATE TABLE IF NOT EXISTS `parent_student` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id` BIGINT UNSIGNED,
    `student_id` BIGINT UNSIGNED,
    `relationship` VARCHAR(20) DEFAULT 'biological',
    `is_primary_contact` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE(`parent_id`, `student_id`),
    FOREIGN KEY (`parent_id`) REFERENCES `parents`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kajian Events
CREATE TABLE IF NOT EXISTS `kajian_events` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `academic_year_id` BIGINT UNSIGNED,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `speaker` VARCHAR(100),
    `location` VARCHAR(200),
    `date` DATE NOT NULL,
    `time_start` TIME NOT NULL,
    `time_end` TIME NOT NULL,
    `status` VARCHAR(10) DEFAULT 'draft',
    `qr_code_image` VARCHAR(255),
    `attendance_count` INT DEFAULT 0,
    `created_by` BIGINT UNSIGNED,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendances
CREATE TABLE IF NOT EXISTS `attendances` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kajian_event_id` BIGINT UNSIGNED,
    `student_id` BIGINT UNSIGNED,
    `scanned_by` BIGINT UNSIGNED,
    `scanned_at` TIMESTAMP NOT NULL,
    `status` VARCHAR(10) DEFAULT 'hadir',
    `notes` TEXT,
    `proof_image` TEXT,
    `latitude` DECIMAL(10, 8),
    `longitude` DECIMAL(11, 8),
    `is_valid` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`kajian_event_id`) REFERENCES `kajian_events`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`scanned_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings
CREATE TABLE IF NOT EXISTS `settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) UNIQUE NOT NULL,
    `value` TEXT,
    `type` VARCHAR(20) DEFAULT 'string',
    `group` VARCHAR(50) DEFAULT 'general',
    `label` VARCHAR(255),
    `description` TEXT,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kajian Feedback
CREATE TABLE IF NOT EXISTS `kajian_feedback` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kajian_event_id` BIGINT UNSIGNED,
    `parent_id` BIGINT UNSIGNED,
    `rating` TINYINT,
    `comment` TEXT,
    `is_anonymous` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`kajian_event_id`) REFERENCES `kajian_events`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `parents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Push Subscriptions
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED,
    `endpoint` TEXT NOT NULL,
    `public_key` VARCHAR(255),
    `auth_token` VARCHAR(255),
    `content_encoding` VARCHAR(255),
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

---------------------------------------------------------
-- SEED DATA (INITIAL SETUP)
---------------------------------------------------------

-- 1. Insert Roles
INSERT INTO `roles` (`name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES 
('admin', 'Administrator', 'Full access to system', NOW(), NOW()),
('panitia', 'Panitia', 'Event committee member', NOW(), NOW()),
('kepsek', 'Kepala Sekolah', 'School principal with view access', NOW(), NOW()),
('wali_santri', 'Wali Santri', 'Student guardian/parent', NOW(), NOW());

-- 2. Insert Admin User (Password: password)
INSERT INTO `users` (`role_id`, `name`, `username`, `email`, `password`, `is_active`, `created_at`, `updated_at`) VALUES 
(1, 'Administrator', 'admin', 'admin@kajianwalsan.com', '$2y$12$EGtvPn/CmtaGp3r+HXVZn5hgBoRwcSPd0DRAogX8vPg=', TRUE, NOW(), NOW());

-- 3. Insert Default Settings
INSERT INTO `settings` (`key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES 
('admin_whatsapp', '6281234567890', 'string', 'whatsapp', 'Nomor WhatsApp Admin', 'Nomor WhatsApp admin untuk menerima permintaan reset password.', NOW(), NOW()),
('institution_name', 'Kelompok Tahfidz Griya Qur\'an "Tunas Ilmu"', 'string', 'general', 'Nama Lembaga', 'Nama lembaga yang ditampilkan di aplikasi', NOW(), NOW()),
('app_name', 'Kajian Walsan', 'string', 'general', 'Nama Aplikasi', 'Nama aplikasi yang ditampilkan di header', NOW(), NOW());

-- 4. Initial Academic Year
INSERT INTO `academic_years` (`name`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES 
('2025/2026', '2025-07-01', '2026-06-30', TRUE, NOW(), NOW());
