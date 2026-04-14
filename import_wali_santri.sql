-- ================================================================
-- IMPORT DATA WALI SANTRI - Griya Qur'an
-- Generated: 2026-04-14 01:57:23
-- Total Santri: 226
-- Total Keluarga: 188
-- ================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ========== STEP 1: KELAS ==========
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 1 Ikhwan', '1', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 1 Akhwat', '1', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 2 Ikhwan', '2', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 2 Akhwat', '2', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 3 Ikhwan', '3', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 3 Akhwat', '3', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 4 Ikhwan', '4', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 4 Akhwat', '4', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 5 Ikhwan', '5', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 5 Akhwat', '5', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 6 Ikhwan', '6', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `classes` (`name`, `level`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustawa 6 Akhwat', '6', 25, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- ========== STEP 2: SANTRI (226 records) ==========
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460290', 'Abdullah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-09-13', 'Babakan rt 14/04 kec. Kalimanah kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460291', 'Abdullah Qays', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-10-22', 'Klapasaeit Rt 02 Rw 01 Kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460292', 'Abdulloh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Padang', '2018-04-25', 'Desa Lemberang Rt 1 Rw 1, Kecamatan Sokaraja, Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460293', 'Faatih', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-02-22', 'Kedungbenda RT 02 RW 12 kecamatan Kemangkon,Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460294', 'Haikal Omar Alshehab', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Cirebon', '2018-05-18', 'Desa banjaranyar RT/RW 03/08 kec sokaraja kab Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460295', 'Hisyam Abdurrahman Amri', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-04-02', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460296', 'Mohamad Al Fatih', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-02-19', 'Klapasawit RT 03 RW 06 Kec Kalimanah Kab Purbalingga Jawa Tengah', '-', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460297', 'Muadz Ar-Rasyid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2019-03-02', 'Komplek Kavling Tunas Ilmu Blok B1 no.01, Dukuhcakra RT.04/04 Desa Kedungwuluh, Kec. Kalimanah, Kab. Purbalingga, Jawa Tengah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460298', 'Muhammad Abdurrahman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2019-03-29', 'Desa karang lewas RT 10 RW 05 kecamatan Kutasari,kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460299', 'Muhammad Alfarizy Rasyid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-11-01', 'Jalan Mandalika RT 5/5 Selabaya, Kalimanah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460300', 'Muhammad Hamzah Al-Hafizh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-12-12', 'Wirasana RT 001/RW002 Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460301', 'Muhammad Tsaqib Fawwaz', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2018-06-13', 'Tambaksogra RT 09 RW 02, Kecamatan Sumbang, Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460302', 'Muhammad Uwais Alqarny', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-05-30', 'Adoarsa,rt 8,rw 4,kecamatan kertanegara,kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460303', 'Musyaffa Abdullah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-09-07', 'Banjaran RT 8 RW 4 Kecamatan Bojongaari Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460304', 'Sufyan Ats Tsauri', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-08-10', 'Bojong, RT 01 RW 03 Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460305', 'Usamah Abdurrahman Ayyub', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-10-29', 'Dusun Melung RT 04 RW 05 Desa Larangan Kecamatan Pengadegan Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460306', 'Utsman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2017-12-08', 'RT 1 RW 7 kecamatan Kalimanah, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460307', 'Uwaies Abdul Lathief', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-10-17', 'Desa Karanggedang RT.04 RW.01 kecamatan karanganyar kabupaten purbalt', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460308', 'Zain Attaqi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'BANYUMAS', '2019-05-05', 'Desa Karangturi RT 06 RW 01. kecamatan Sumbang Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460309', 'Zehyaan Ar Rayyan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Ikhwan' LIMIT 1), 'L', 'Banjarnegara', '2019-05-15', 'Jompo RT 03 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450246', 'Abdul Mu''iz', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-01-20', 'Desa selaganggeng RT 02 RW 02, kecamatan mrebet, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450247', 'Abdullah Yusuf Alfarisi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2017-07-12', 'Jl. Riyanto GG. Mawar III no 265a, Kel. Sumampir, Kec. Purwokerto Utara, Kab. Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450248', 'Abu Abdillah Ahsan Faozi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-06-14', 'Pegandekan RT 001 RW 002, Kecamatan Kemangkon, Kabupaten Purbqlingga', 'Sefi Mardianti', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450249', 'Ahmad', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-01-09', 'Griya Kalika B9 Kedungwuluh, Kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450250', 'Bilal', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'banyumas', '2016-12-10', 'kedungwuluh rt 8 rw 2 kalimanah purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450251', 'Daniyal Abdul Ghoniy', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-07-27', 'Desa Bukateja RT 04 RW 02 ,kec.Bukateja,kab.Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450252', 'Ibrahim', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2017-10-15', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450253', 'Muhammad Adam', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-07-22', 'Kedungwuluh, karangwinong. Kec:kalimanah kab;purbalinggart02/04', 'Bpk yuswiarjo', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450254', 'Muhammad Ahnaf Elfathan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2025-08-31', 'Klapasawit RT 02 RW 07,Kecamatan Kalimanah Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450255', 'Muhammad Syaddad Afuw Rutka', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Tegal', '2018-03-29', 'Kober RT 3 RW 9 Kec.Purwokerto barat Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450256', 'Musa Putra Wibowo', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-12-16', 'Purbalingga Lor Rt3 Rw3, kecamatan Purbalingga, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450257', 'Naoki Hafiz Azzikri', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-09-15', 'Klapasawit RT 03/05 kecamatan kalimanah kabupaten Purbalingga', 'Tidak Ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450258', 'Raffasya Arkha Syahreza', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-11-26', 'Jln.Puring Rt 01 Rw 04 Nmr 9A', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450259', 'Syafiq Abdullah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Banjarnegara', '2018-03-26', 'Desa Blimbing RT.05/02 kec.mandirja', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450260', 'Syafiq Athar Ubaidillah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'PURBALINGGA', '2017-09-16', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450261', 'Tolhah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-05-05', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450262', 'Ukasyah Abdurrahman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-07-28', 'Jetis rt 14 rw 05, Kecamatan Kemangkon, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450263', 'Ukasyah Kalifa Fil Ardh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2017-08-10', 'Banjar Anyar RT 01 RW 05, Kecamatan Sokaraja, Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450264', 'Urwah Abdurrahman Al Huwairy', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2018-05-27', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450265', 'Zulfadhli Amru Mubarak', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2016-12-03', 'Karangwangkal RT 03 RW 01 Purwokerto Utara', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440201', 'Abdillah Fajri Assajid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-05-21', 'Jl. Mawar 487 Rt. 4 Rw. 4 kalimantan wetan  kec. kalimantan kab.Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440202', 'Abdullah Al Faqih', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-01-30', 'Dukuhcakra rt4/rw4 kec. Kalimanah kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440203', 'Abdurrahman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Sidoarjo', '2016-11-12', 'Padamara, RT 03 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440204', 'Abdurrofi Malikal Khakim', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-11-25', 'Kalimanah wetan RT 04 RW 07, kecamatan Kalimanah, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440205', 'Ahmad Sinaan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2016-07-22', 'Babakan Asri Blok A12, Babakan, Kec. Kalimanah, Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440206', 'Ahmad Yahya Ayyash', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-07-20', 'Lambur RT 01 RW 01,kecamatan mrebet, Kabupaten purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440207', 'Daffa Akar Ibrahim', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Bekasi', '2015-04-14', 'Kalikajar RT 03 RW 08 kecamatan Kaligondang kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440208', 'Fawaz Baihaqi Al Khairy', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-01-21', 'Bojanegara rt 5 rw 1 padamara, Purbalingga ( sedang tinggal di kedungwuluh rt 6 rw 1)', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440209', 'Hisyam Abdurrozzaq', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-03-29', 'Karangpule RT 03 RW 02, Kecamatan Padamara, Kabupaten Purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440210', 'Muhammad Al Fatih', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2017-02-15', 'Kramat Rt 05 Rw 01, Kecamatan Kembaran Kabupaten Banyumas', 'Paryono (kakek)', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440211', 'Muhammad Reyhan Al Hafidz', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-10-13', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440212', 'Muhammad Syafii Erlangga', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-01-01', 'Karang anayr rt02/01 Kec Karamganyar kab purbalingga', 'Toda ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440213', 'Muhammad Syafiq Arsy Saepuloh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-06-27', 'Selabaya RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440214', 'Musa Bin Imam Fajar Subekhi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-11-04', 'Bojongsari rt 02 rw 05 purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440215', 'Musa Bin Mingan Sutomo', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-05-05', 'Kedungwuluh RT 07 RW 01,Kecamatan Kalimanah,Kabupaten Purbalingga', 'Ibu Mutinggah', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440216', 'Radja Adnan Rifti Arsenio', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-10-28', 'Jl kenanga no 1 RT 3 RW 7 perumahan penambongan Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440217', 'Raihan Firdaus', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-08-03', 'Babakan 6/2 Kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440218', 'Umar ''Abdurrozaq', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-08-25', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440219', 'Usman Abdurrofi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2017-01-28', 'Desa Karangbanjar Rt19 rw08 kecamatan bojongsari kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440220', 'Yahya Abdullah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2017-02-07', 'Kedungwuluh Rt 08 Rw 02', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430147', 'Abdul Sakha Mubarakh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Jakarta Timur', '2025-08-28', 'Grecol RT 05 RW 01 Kecamatan Kalimanah ,Kabupaten Purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430149', 'Abdulloh Syamil', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-06-29', 'Ds.Walik Kutasari RT 16 RW 08 Kecamatan Kutasari, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430150', 'Abdurrahman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-02-08', 'Kedungwuluh RT 06 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430151', 'Abdurrohman Bin Tsabit', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-02-18', 'Kedungwuluh RT 07 RW 02 Kecamatan  Kalimanah Kabupaten  Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430152', 'Ahsanul Faqih M', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Bekasi', '2014-08-12', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', 'Hadi Priyanto', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460286', 'Athallah Hafiz Nurseto', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purwokerto', '2015-08-28', 'Ds. Suro RT 06/04, Karangjati, Kecamatan Kalibagor, Kabupaten Banyumas, Jawa Tengah, Indonesia 53193', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430154', 'Fatih Abdurahman Ayyub', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Banjarnegara', '2015-05-23', 'Desa Blimbing RT.05/02 kec.Mandiraja', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430155', 'Guinandra Amzar Khalfani Iskandar', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Sleman, Jogjakarta', '2016-03-09', 'Grumbul kikiran, desa Silado, Sumbang, banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430156', 'Haikal Hafiz Pratama', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-01-13', 'Kalikabong rt2/rw1.kec.kalimanah.kab.purbalingga.sekarang domisili klahang rt1/rw6.kec.sokaraja.kab.banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430157', 'Hamzah Nail', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-11-17', 'Jl Komisaris Noto Soemarsono 124 b  RT 03/02 Purbalingga 53313', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430158', 'Ibrohim Putra Wibowo', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2016-08-01', 'Purbalingga Lor RT 3 RW 3', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430160', 'Kahfi El Azzam', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-07-28', 'Klapasawit RT 04 RW 05, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430162', 'Muhamad Faris', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2015-10-28', 'Banyumas Rt 01/04, kecamatan Banyumas, Kabupaten Banyumas', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430163', 'Mustofa Al Huda', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-10-16', 'Kalikabong RT 01/ RW 01, Kecamatan Kalimanah, Kabupaten PurbalinggaP', 'Dwio Ratono', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430164', 'Unais Ainurrofiq', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2015-01-20', 'Kotayasa RT 08 RW 05, Kecamatan Sumbang, Kabupaten, Banyumas,', 'Darsun', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430166', 'Yazid Ghani Abdillah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Ikhwan' LIMIT 1), 'L', 'Bekasi', '2015-12-02', 'Kedungwuluh 02/04 Kec.Kalimanah Kab.Purbalingga.', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420107', 'Abdullah Syaukani', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2015-05-16', 'Kedungwuluh RT 05 RW 01, Kalimanah, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440190', 'Abdulloh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-10-07', 'Griya kalika B9 Kedungwuluh Kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420108', 'Abdulloh Bilal', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-08-05', 'Kedungwuluh rt 07 rw 01, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420109', 'Adh Dhuha Kaisar Alwi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2013-12-04', 'Klahang RT 02 RW 02 kecamatan Sokaraja Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420111', 'Arzachel Rasyad Mufid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Banjarnegara', '2014-12-21', 'Babakan RT 43 RW 11 Kec. Kalimanah Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420143', 'Jarvis Gaurav Elzanki', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2015-07-17', 'RT 001 RW 005, Kel. Purbalingga Lor, Kec. Purbalingga, Kab. Purbalingga, Jawa Tengah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430193', 'Kenzie Rayyan Adhyasta', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Banjarnegara', '2014-11-15', 'Kedungwuluh, RT 02 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420113', 'Lutfi Sakhi Zaidan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-04-14', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420114', 'Muhammad Alwi Al Rasyid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-04-29', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga.kabupaten purbalinggabalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420115', 'Muhammad Faiz Al Barra', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-06-16', 'Wirasana, RT 02/07 Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420116', 'Muhammad Mahrez Davitra', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-08-27', 'Lambur Rt 01 Rw 01,kecamatan mrebet, Kabupaten purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420117', 'Muhammad Syafiq Dwiputra Setiono', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Karawang', '2015-04-19', 'Desa Munjul Rt 12 RW 06 Kecamatan Kutasari Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420118', 'Nizar Hilmi Abdullah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Limbangan, kutasari, purbalingga', '2014-02-26', 'Limbangan rt 10 rw 05, kecamatan kutasari, kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420119', 'Rayhan Mirza Santoso', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-03-29', 'Perum Grita Perwira Asri, blok B no. 9, Babakan Purbalingga', 'Titi Hadiah Milihani', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420120', 'Said Ibnu Abdurrohman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-07-25', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420121', 'Ubay Ibnu Abdullah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-07-06', 'Jl Wiraguna no 16 purbalingga kidul, purbalingga.', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420122', 'Ubay Zufar', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '1983-04-13', 'Jl Komisaris Noto Soemarsono 124 b  RT03/02 Purbalingga 53313', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420123', 'Umair Abdurrozzaq Ar Royyan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2013-03-19', 'Kotayasa RT 08 Rw 02, kecamatan sumbang, kabupaten Banyumas', 'Darsun', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420124', 'Yusuf Hanif Alghifari', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2014-11-19', 'Selabaya/ Ruko selabaya indah jl geriliya barat', 'Tidak Ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420125', 'Zaidan Putra Arroyan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-04-08', 'Karang jambe rt 02/rw 03.kec.padamara.kab.purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410082', 'Abdul Barr', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-12-25', 'Klapasawit RT 01 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410084', 'Abdul Rozaq', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-05-16', 'Karanglewas RT 11 RW 05, kecamatan Kutasari,kabupaten Purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410078', 'Abdullah Hudzaifah Anggoro', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2014-02-14', 'Perum. Berlian Karangsentul Blok F No. 3, RT 02 RW 05, Desa Bojanegara, Kecamatan Padamara, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410080', 'Abdurahman Faruh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2013-02-28', 'Kalisube Rt 01/04. Banyumas', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410083', 'Abdurrahman Faid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-09-14', 'Dukuhcakra rt 4/rw4 kec. Kalimanah kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410086', 'Adam', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2013-10-29', 'Perum Abdi Negara, Kresna IV No 2. Bojaneraga, Padamara - Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410081', 'Dafa Khusna Aulia Pradipta Al-Gifari', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-07-17', 'Galuh RT 08 RW 04, Bojongsari, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410051', 'Faisal Imam Khalil', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-01-09', 'Perum trajumalang blok C1, Kandang gampang RT 04/02, kec. Purbalingga, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410085', 'Hafiz Azka Jibran', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-05-22', 'Selabaya RT 02 RW 04 kecamatan Kalimanah kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410088', 'Ibrahim Nusantara Putra Susanto', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2014-05-23', 'Sokawera RT 02 RW 04, Kec. Padamara Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450243', 'Muhammad Dandi Pratama', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Banjarnegara', '2013-05-28', 'Kavling Tunas Ilmu Depok Kedungwuluh RT 3 RW 3 Kalimanah', 'Agus Sutomo', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410091', 'Muhammad Labib', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-09-10', 'Pekalongan,RT02 RW04 kecamatan Bojongsari, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440198', 'Muhammad Yazid Al Fajr', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Depok', '2013-08-19', 'Graha Permata Selabaya No G14 Selabaya Kalimanah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410089', 'Ukasyah Abdullah Al Atsary', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Banyumas', '2014-03-29', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440199', 'Utbah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2014-03-01', 'Kedungmenjangan RT 01 RW 03, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440192', 'Yazid', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Riyadh', '2013-03-09', 'Ponpes Tahfiz An-Naba, Jl. Karangjati, Desa Suro Rt06/Rw04, Kec. Kalibagor, Kab.Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410079', 'Zain Abrizam', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Ikhwan' LIMIT 1), 'L', 'Purbalingga', '2013-10-22', 'Karangkabur rt 01/ rw 02, kel, bojanegara, kec. Padamara, kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460310', 'Afanin Umaiza Rahmah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'purbalingga', '2018-11-04', 'karangreja RT012 RW006,kecamatan kutasari,kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460311', 'Aisyah Ahsanu Amala', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2019-02-25', 'Domisili:Perum puri kalimanah blokA17', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460312', 'Amroh Kholilah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Banyumas', '2018-12-18', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460313', 'Aysha Khaliqa Naditya', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2019-02-01', 'Penaruban RT 2 RW 2,KALIGONDANG, PURBALINGGA', 'Firma nur aditya', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460314', 'Din Nailah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Banyumas', '2018-03-20', 'Jalan Jambu No. 5 Desa Kalimanah Wetan RT 04 RW 07 Kecamatan Kalimanah Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460315', 'Faradillah Mufidah Alfathunissa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2019-01-11', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460316', 'Fatimah Nuha Jamilah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-05-02', 'Selabaya rt02 rw04 kec.kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460317', 'Jazlyn Ameera', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-09-06', 'Desa Gembong, RT 03 RW 02, Kecamatan Bojongsari, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460318', 'Khaulah Asiyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-11-12', 'Kedungwuluh rt 07 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460319', 'Najla Hanin', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Banyumas', '2018-04-04', 'RT 1 RW 4 desa kalisube,kec Banyumas,kab,Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460320', 'Najma Arsy Yumna', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-11-21', 'Selabaya RT 02 RW 04', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460321', 'Nara Nur Lestari', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2019-01-31', 'Kedungmenjangan Rt 04  Rw02 Kec. Purbalingga Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460322', 'Rifda', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Banyumas', '2019-02-19', 'Kedungwuluh Rt 08 Rw 02', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460323', 'Rufaidah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Pemalang', '2018-07-10', 'Babakan rt10/02,Kalimanah, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460324', 'Shofiyah Khumairo', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-06-01', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460325', 'Shofiyyah Ibnatu Agus', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-05-23', 'Perum Puri Babakan lama, no 76, RT 32 RW 08, Kecamatan Kalimanah, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460326', 'Sofiyyah Ibrahim', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Banyumas', '2017-08-02', 'Jl. Baturraden Timur. Gang Sadewa RT4 RW2 No. 7. Sumbang, Banyumas.', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460328', 'Zainab', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2019-01-18', 'Padamara, RT 03 RW 03, Kec. Padamara, Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460327', 'Zainab Adzkiya', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-06-14', 'Kandang Gampang RT 03 RW 05, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460329', 'Zainab Rufaidah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 1 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2019-05-23', 'Bojanegara rt 5 rw 1 padamara, Purbalingga (sedang tinggal di kedungwuluh rt 6 rw 1)', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450266', 'Afiza Farzana Mufidah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Bekasi', '2017-01-09', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', 'Hadi Priyanto', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450267', 'Aisyah Adz Dzakiyyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-03-14', 'Karang tengah rt 05/rw03, kemangkon, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450268', 'Aisyah Nismaranur Ndaru', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Jakarta', '2018-01-21', 'Kedungwuluh RT 05 RW 02, Kalimanah, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450270', 'Annisa Khairana Miza', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2021-04-19', 'Klapasawit RT 2 RW 5, Kalimanah, purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450271', 'Arsyifa Khoirunnisaa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-01-05', 'Klapasawit RT 02 RW 01', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450272', 'Asiyah Kurniawan', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-12-29', 'Pagedangan RT 03 RW 06   Kecamatan Purbalingga kidul Kabupaten  Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450273', 'Athaya Ashiilah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-11-14', 'Kedungwuluh RT 04 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450274', 'Azkadina Zoraida Wijaya', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Banyumas', '2017-03-17', 'Karangklesem RT 03 RW 02 kec. Kutasari kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450288', 'Gemilang Silmi Kaffah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-11-17', 'Pengadegan rt08/rw04, Kec.Pengadegan, Kab.Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450275', 'Hamida Asy-Syaima', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'PURBALINGGA', '2017-08-31', 'Rt 08 Rw 04. Des karangsari Kalimanah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450276', 'Haniifah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-03-16', 'Kedungwuluh RT 1 RW 2, kecamatan Kalimanah kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450277', 'Khansa Alula Nadir', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purwakarta', '2017-07-12', 'Pengalusan, RT 05 RW 01, kecamatan Mrebet, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450278', 'Maisaroh', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-02-05', 'Kedungwuluh RT 02 RW 04 Kecamatan Kalimanah Kabupaten Purbalingga', 'Sohiri', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450279', 'Maryam Ash-Shiddiqah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Bandung', '2017-06-07', 'Kavling Tunas Ilmu Blok B1 No.1 RT 04 RW 04  Desa Kedungwuluh, Kec Kalimanah, Kab Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450280', 'Nafisah Qulubiha', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Banyumas', '2017-07-12', 'Banteran RT 4 RW 5 kec sumbang kab banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460331', 'Raihana Asiyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-07-03', 'Jl. Jati, Rt. 1/ Rw. 3, Desa Kalimanah Wetan, Kec. Kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450281', 'Rumaisha Zaina Salsabila', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2018-04-27', 'Kelurahan bojong Rt03Rw01 Kec. Purbalingga Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450282', 'Shofiyah Nur Sabiya', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-05-29', 'Klapasawit RT 02 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450283', 'Shofiyyah Muhana Nufaisah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-07-21', 'Jalan Mangga Timur A1 perumahan selabaya indah desa selabaya kecamatan kalimanah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214450284', 'Sumayyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 2 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-01-22', 'Kedungwuluh,RT 08 RW 01,kalimanah kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440222', 'Aisyah Karunia Arbina', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-02-10', 'Kajongan RT 002 RW 002, Kecamatan Bojongsari, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440223', 'Anindya Mukhbita Myesha', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-10-27', 'Kedungwuluh rt 04 rw 01, kecamatan kalimanah, kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440224', 'Annasya Adreena Saila', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-10-13', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440225', 'Annasya Qirani Azzahra', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-08-23', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460289', 'Arwa Naila Dzakiyyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-09-30', 'Desa karangcegak RT 12 RW 05, Kec. Kutasari, Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440226', 'Attaqia Saufa Adzkia', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-04-11', 'Kedungwuluh RT 8 RW 1, kecamatan Kalimanah kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440227', 'Ghavrila Joza Valwa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Kutai Kartanegara', '2017-01-04', 'Karang jambe RT.2 RW.3 Kecamatan Padamara Kabupaten Purbalinggamra', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440228', 'Hafshoh Nafilah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Pekanbaru', '2017-05-13', 'Banteran RT 01 RW 05 kecamatan Purwokerto kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440229', 'Humaira Althafunnisa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-01-25', 'Galuh RT 008 RW 004, Kecamatan Bojongsari, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440230', 'Kahla Halimah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Banjarnegara', '2017-02-26', 'Kedungwuluh RT.01, RW.01. Kalimanah. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440231', 'Kayyisa Hawa Almedina', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Banyumas', '2016-09-08', 'Kedunhwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440232', 'Khadijah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-11-05', 'Jl.Jend Sudirman gg.Baraba 23 Rt 03/03 Purbalingga Kidul - Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440233', 'Maryam', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-06-21', 'Jompo RT 01 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440234', 'Mashel Anindya Azkadina', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Bekasi', '2016-11-22', 'Gandatapa RT 05 RW 06, kecamatan Sumbang, Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440235', 'Nafisah Anasyitah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-08-08', 'Domisili:Perum puri kalimanah blokA17', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440236', 'Rafayza Khumaira Al Habibi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-02-12', 'Kalimanah kulon RT 1 RW 2, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440237', 'Ruhma', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-08-25', 'Pekiringan Dusun 5 RT 02/10 Pekiringan Karangmoncol Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440238', 'Syafiqa Hasna Humaira', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Batam', '2016-12-01', 'Desa Purbalingga Wetan RT 4 RW 9 Kec. Purbalingga, Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440239', 'Syaima Bintu Agus', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-06-24', 'Perum Babakan lama, nomor 76, RT 32 RW 08, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214440240', 'Zahira', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 3 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2017-02-21', 'Padamara RT. 003 RW. 003, Kec. Padamara, Kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430169', 'Aisyah Nur Khisma', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-12-20', 'Sokawera Rt 01 RW 05,kecamatan Padamara, kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430170', 'Anisa Naufalin Fikria Faozi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-03-02', 'Desa pegandekan RT 001 RW 002,kecamatan kemangkon,kabupaten purblingga', 'Sefi Mardianti', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430171', 'Annisa Syafa Aqila', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Banyumas', '2016-02-21', 'Perumahan Puri Tama Indah blok Q4. Gemuruh. RT 2 RW 8, Padamara. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430172', 'Aqila Zahwa Jauza', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-02-10', 'Kandang gampang rt 03 rw 05 ,kecamatan /kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430173', 'Asma Khairunisa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Banyumas', '2015-10-04', 'Perum Griya Bantar Indah Blok J no.1 Rt 02 Rw 05 Bantarwuni Kembaran Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430174', 'El Syauqia Hibatillah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-12-19', 'Mangunegara RT 6 RW 1 , Kecamatan Mrebet, Kabupaten Purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430175', 'Fathimah Al-Humairaa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-04-06', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430176', 'Fatima Khairani Karfi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-12-31', 'Jl cempaka raya no 4 rt 006 rw 007 Perum Penambongan  Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430177', 'Fatimah Az Zahra', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-03-29', 'Grecol rt 04 rw 01,kecamatan kalimanah,kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430178', 'Fatimah Az Zahra Bayuputri', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Bekasi', '2015-05-14', 'Bancar RT 003 RW 003, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430179', 'Fiandra Ghani Azkadina', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-04-10', 'Griya Kalika blok C12A Sokawera, Kecamatan Padamara', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430180', 'Habibah Lathifunnisa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2016-01-24', 'Brobot RT 5 RW 2,Kecamatan Bojongsari, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430181', 'Hafsah Az Zahran', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-12-04', 'Kalikabong RT 02 RW 04 kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430182', 'Hanin Aulia Nur Faizah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Banyumas', '2015-04-17', 'karang tengah, rt 6 rw 2 kecamatan kembaran kabupaten banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430183', 'Khansa'' Nur ''Afifah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-07-21', 'Karangtengah Rt 07 Rw 04, kecematan Kemangkon, Kabupaten Purbalingga', 'Bpk Sutriyo', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430184', 'Lulu Anindya Rahma', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-09-13', 'Bojanegara rt 03rw02, kecamatan padamara, kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430185', 'Nabila Hafshah Mujahidah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Batam', '2015-04-29', 'Kaligondang RT 03 RW 04 kecamatan Kaligondang kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430186', 'Nabila Hasna', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-08-29', 'Kembangan Rt 04 Rw 05, Bukateja, Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430187', 'Niki Gendhis Satria', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'PURBALINGGA', '2015-09-05', 'Purbalingga lor RT 02 RW 03 , kecamatan Purbalingga, Kabupaten Purbalingga', 'tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430188', 'Shakila Nuraini', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-11-09', 'Babakan RT 15 RW 04, Kalimanah, Purbalingga', 'tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430189', 'Zaila Sakinah Syaima', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 4 Akhwat' LIMIT 1), 'P', 'Purbaljngga', '2015-07-05', 'Desa kr sari Rt 08 Rw 04 kalimanah Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420126', 'Adzkiya Hasna Syafiyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-12-16', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420127', 'Aisyah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-07-09', 'Desa Babakan RT 14 RW 04 kec. Kalimanah kab. Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420128', 'Amelia Karla Nur Azizah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-05-18', 'Purbalingga kidul RT 02,RW 01,Purbalingga kidul,Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420130', 'Dhiya Aqilla', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-01-19', 'Kedungwuluh RT 06 RW 01 kecamatan Kalimanah ,Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420131', 'Dzakira Aftani Arsy', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-08-08', 'Dusun 2 Meri RT 15 RW 06, Kecamatan Kutasari, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420132', 'Jihan Dhiya Ayyaasya', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-07-30', 'Gemuruh RT 01 RW 07, Kecamatan Padamara  Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420133', 'Khaulah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2015-06-04', 'Galuh RT 04 RW 03', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420134', 'Maryam', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-10-14', 'Kedungwuluh RT 08 RW 02, kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420135', 'Nadhira Rizky Setiandari', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-09-24', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420136', 'Naila Mafaza', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-04-17', 'Kedungwuluh RT 2 RW 4,Kecamatan Kalimanah,Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420138', 'Rumaisya Nuraini', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-10-31', 'Pekaja RT 05 RW 03 Kecamatan Kalibagor Kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420139', 'Shaquila Najwa Aira', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Banyumas', '2014-10-26', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214420141', 'Talita Zalfatunida Naqiyya', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 5 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-04-20', 'Bojongsari rt02 rw13 bojongsari purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410092', 'Ainiya Faida Azmi', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Bekasi', '2025-09-04', 'Karangpule RT 02 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410093', 'Aisyah Talita Zahran', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Putbalingga', '2014-03-12', 'Kalikabong RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214460285', 'Anisa Sherena Aquila Nurseto', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purwokerto', '2013-12-28', 'Kompleks Pondok Tahfidz AnNaba Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410095', 'Aqila Keisya Azzahra', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-11-28', 'Karang Banjar RT 16 RW 06, kecamatan Bojongsari, kabupaten Purbalingga', 'Tuwarno', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410096', 'Askanah Nida Hayfa', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-06-09', 'Purbalingga Kidul RT 03 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410097', 'Balqis Najma Habibah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2014-02-24', 'Sokawera RT 2 RW 4 kecamatan Padamara kabupaten Purbalingga', 'Tidak ada', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410098', 'Fadhilatul Aulia', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2012-06-15', 'Klapasawit RT 1 RW 2 , kalimanah, purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214430194', 'Fahira Kayla Fathiani', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Cirebon', '2013-04-04', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Padamara', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410099', 'Fathimah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-11-28', 'Bojongsari rt 02 rw 05 purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410100', 'Fatimah Azzahra', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Banyumas', '2013-11-09', 'Sambeng Kulon RT 04 RW 01 kecamatan Kembaran kabupaten Banyumas', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410062', 'Gendis Amira Pramesti Humayun', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-08-05', 'Kedungwuluh RT 08 RW 02 Kecamatan Kalimanah, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410101', 'Hanifah Uswatun Hasanah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-11-28', 'Grecol RT 04 RW 01,kecamatan kalimanah,kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410102', 'Huriyah Qurratu''ain Rosyidah Prasetyo', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Utecht, Netherland', '2014-03-17', 'Jl letkol isdiman no 01 rt/rw 01/04, kecamatan purbalingga kidul, kabupaten purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410103', 'Khanza Ghinna Nur Azizah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-02-18', 'Purbalingga kidul RT 02 RW 01,Kecamatan Purbalingga,Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410104', 'Khanza Saafia Nur Arafah Wibowo', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Banyumas', '2013-12-30', 'Desa Bojanegara, RT 01 RW 03, Kecamatan Padamara, Kabupaten Purbalingga Jawa Tengah', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410105', 'Liyana Nafisa Amri', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-03-14', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410106', 'Niswah Muthmainnah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Batam', '2013-05-22', 'Desa Kaligondang RT 03 RW 04 kec Kaligondang kab Purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214410059', 'Ravikah Rahmah Arum Fathurrohman', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-10-18', 'Desa Purbalingga Kidul, RT.002/RW.001, Kecamatan Purbalingga, Kabupaten Purbalingga', 'Suharto', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT IGNORE INTO `students` (`nis`, `name`, `class_id`, `gender`, `birth_place`, `birth_date`, `address`, `guardian_name`, `is_active`, `created_at`, `updated_at`) VALUES ('0214400048', 'Salsabila Nadhifah', (SELECT `id` FROM `classes` WHERE `name` = 'Mustawa 6 Akhwat' LIMIT 1), 'P', 'Purbalingga', '2013-08-18', 'Jompo Rt 01 RW 04 kec kalimanah kab purbalingga', NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- ========== STEP 3: USER ACCOUNTS + PARENT + RELASI ==========

-- Ayah: Popo Apri Rayo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Popo Apri Rayo', 'A0214420127', 'popoaprirayo@gmail.com', '$2y$10$emptraSeHd1lYOanDSnsPuJIkCPYnz.oqJYemEz1GG9d75WCbZiKK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085647138483', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0001-2026', '3303060504900002', 'Wiraswasta', 'Babakan rt 14/04 kec. Kalimanah kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420127' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460290' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Latifah Riani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Latifah Riani', 'B0214420127', 'latiefah.riani@gmail.com', '$2y$10$OFuyZBxuNokNW96i8CbYb.g51.QyGd189wid4EbDXjvCeUJfVpa2W', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085228436937', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0002-2026', '3303106710930001', 'Pengajar', 'Babakan rt 14/04 kec. Kalimanah kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420127' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460290' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Abdul Rohman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Abdul Rohman', 'A0214460291', 'abdulrohmanalkhairy@gmail.com', '$2y$10$HW9euQeInRbKaUwT9vpgNejUAS.56G3gJiW5CBOappdY5rQZIk8TC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082379455505', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0003-2026', '1809020212900004', 'Karyawan Swasta', 'Klapasaeit Rt 02 Rw 01 Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460291' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ihda Nur Azizah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ihda Nur Azizah', 'B0214460291', 'B0214460291@kajian.griyaquran.web.id', '$2y$10$SPwFDPKaQLY3lIxfeVN6c.G6RopavxoEbHdhRs9uHPGfUUFmALNh2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082236413676', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0004-2026', '3303065603960004', 'Guru Tahfidz', 'Klapasaeit Rt 02 Rw 01 Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460291' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sahad Halomoan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sahad Halomoan', 'A0214460292', 'halomoansahad@gmail.com', '$2y$10$avbqpMOqOCcGzr/vHfBV/ukSg3NvMrswWiXDuf5awplNsQieDEG7G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085959463344', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0005-2026', '1308191609930001', 'Wiraswasta', 'Desa Lemberang Rt 1 Rw 1, Kecamatan Sokaraja, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460292' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Farradhiva Ankla
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Farradhiva Ankla', 'B0214460292', 'farradhiva.ankla@gmail.com', '$2y$10$dyb04zhufYJodHbXxA.S3evp3r4rvsUfi1ECMWAhpBxrcaBhMPUSm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082138827550', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0006-2026', '3302196101920002', 'Ibu Rumah Tangga', 'Desa Lemberang Rt 1 Rw 1, Kecamatan Sokaraja, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460292' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Priyadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Priyadi', 'A0214460293', 'abufaatihpriyadi@gmail.com', '$2y$10$ou1VHsCpPev79sye5.vt1u23RhhKcOckZmSI7F8VjfRya85Ol3L7.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088902811189', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0007-2026', '3303012907920002', 'Pedagang', 'Kedungbenda RT 02 RW 12 kecamatan Kemangkon,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460293' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Iis Nur'aini
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Iis Nur''aini', 'B0214460293', 'B0214460293@kajian.griyaquran.web.id', '$2y$10$YfMD/2cbvjtoIUbRMY/IZufrvIX7/mLD4JQ8pSbjK2mx3rn6S37lq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08816605668', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0008-2026', '3301015004950003', 'Ibu rumah tangga', 'Kedungbenda RT 02 RW 12 kecamatan Kemangkon,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460293' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aminudin
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aminudin', 'A0214460294', 'adinachmad9@gmail.com', '$2y$10$KjgSKNm5n/j80/xbikbPlu.Cg/506jqdrltJaocLdAuse.svKKSoC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08982111321', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0009-2026', '3302192101900001', 'Pedagang', 'Desa banjaranyar RT/RW 03/08 kec sokaraja kab Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460294' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti saonah fauziah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti saonah fauziah', 'B0214460294', 'adinachmad91@gmail.com', '$2y$10$tk8dSN7bZjwh9vh87tkRIeyNu0HuWDBigtji.qO7st4SMFt7PTQpK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08990594647', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0010-2026', '3209216207980004', 'Tidak tahu', 'Desa banjaranyar RT/RW 03/08 kec sokaraja kab Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460294' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ulil Amri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ulil Amri', 'A0214410105', 'klikgrafikabukateja@gmail.com', '$2y$10$pJQlT43wM2xLUPzl3IiMNeqPgLVI37yfG2NNdsbbNXoGS9xUHxYou', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081215173817', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0011-2026', '3303022507870002', 'Wiraswasta', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410105' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460295' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Bena Mulfiarni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Bena Mulfiarni', 'B0214410105', 'mmulfiarni@gmail.com', '$2y$10$Ikg3.z.GTl0Hb3VLrp7MpOmnnTUtOBpYkfO4kHnP1uzq1ox8.SZBa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082225391384', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0012-2026', '3303064610900002', 'Ibu Rumah Tangga', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410105' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460295' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eko Martanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eko Martanto', 'A0214460296', 'A0214460296@kajian.griyaquran.web.id', '$2y$10$/fl8Z6ecwUenMGZYxYOk0ucOGH1N6.b19olvzhArxqRs.q/YiUCe6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227706045', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0013-2026', '3303062910830001', 'Dagang', 'Klapasawit RT 03 RW 06 Kec Kalimanah Kab Purbalingga Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460296' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sutiyah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sutiyah', 'B0214460296', 'sutiyahpurbalingga@gmail.com', '$2y$10$vpvqJk4jBA9xXIPjv.YRZe1aEnjmfpasZNF2LqOa4usR/DbhgyUBm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895634691444', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0014-2026', '3303064705830005', 'Karyawan', 'Klapasawit RT 03 RW 06 Kec Kalimanah Kab Purbalingga Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460296' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Muhamad Riana Yudianto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Muhamad Riana Yudianto', 'A0214460297', 'mrianayudianto@gmail.com', '$2y$10$dPicem/m/Un/3fl9rkSvJOQcKCgYqsVRuYQui5gLiU1bUQK/sLaRC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087822994032', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0015-2026', '3204051902930008', 'Guru', 'Komplek Kavling Tunas Ilmu Blok B1 no.01, Dukuhcakra RT.04/04 Desa Kedungwuluh, Kec. Kalimanah, Kab. Purbalingga, Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460297' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Gletika
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Gletika', 'B0214460297', 'g.ummumaryam@gmail.com', '$2y$10$oIQdD9E.pSr0a81WCdCS/.NxoZgam9NdM6giEoUHFTLoKcjPDpX3i', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087722875749', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0016-2026', '3204325209890007', 'Ibu Rumah Tangga', 'Komplek Kavling Tunas Ilmu Blok B1 no.01, Dukuhcakra RT.04/04 Desa Kedungwuluh, Kec. Kalimanah, Kab. Purbalingga, Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460297' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Heri Abdul Rozak
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Heri Abdul Rozak', 'A0214460298', 'azizahnur182011@gmail.com', '$2y$10$PbG5jon6/PH4XlYdy450ae/ZIWlrUeu4HIrJOw8EAer6xznHjdjRS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081218512532', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0017-2026', '3303072410740001', 'Dagang', 'Desa karang lewas RT 10 RW 05 kecamatan Kutasari,kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460298' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: TRI WAHYUNINGSIH
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('TRI WAHYUNINGSIH', 'B0214460298', 'azizahnur1820111@gmail.com', '$2y$10$MrIT/wSadB9XV/Tdb/rYuuFIW2QOG7npIgX8pRLe7kiQJ7f.oqs4O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081218512532', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0018-2026', '3303074312790001', 'Ibu rumah tangga', 'Desa karang lewas RT 10 RW 05 kecamatan Kutasari,kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460298' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ferdinan Candra Riawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ferdinan Candra Riawan', 'A0214460299', 'dinancandra@gmail.com', '$2y$10$W5WUXe5FKLS2uhbCx4Bgf.Mby80C3E6i1//1WA./d4h0znAMf7Gym', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085866125974', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0019-2026', '3303051304910001', 'Pedagang', 'Jalan Mandalika RT 5/5 Selabaya, Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460299' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Kirana Puspa
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Kirana Puspa', 'B0214460299', 'candranana502@gmail.com', '$2y$10$Glcjgm/6ddXhQzASMqrQne2GTw841V9aQsjb4AHf9.0ejDUdu.tMa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085713685802', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0020-2026', '3301024603920010', 'Ibu rumah tangga, membantu suami berjualan', 'Jalan Mandalika RT 5/5 Selabaya, Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460299' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rijal Awali
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rijal Awali', 'A0214460300', 'rijal311280@gmail.com', '$2y$10$o6ZnN5XSkDS2lPfQWBRHgeItYcRYklxqt.TRNy8vnFf0r3yXr/k/O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087728400111', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0021-2026', '3303053112800005', 'Terapist', 'Wirasana RT 001/RW002 Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460300' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ani Widanarti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ani Widanarti', 'B0214460300', 'aniwidanarti3@gmail.com', '$2y$10$h6JQeWi8dvQtJ8H7k5dOAujht.47QkPOiVYpq79bnPBsoQ7LKVnnq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085879815517', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0022-2026', '3303054308860002', 'Ibu rumah tangga', 'Wirasana RT 001/RW002 Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460300' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rohmatulloh
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rohmatulloh', 'A0214460301', 'rohmatullohaja@gmail.com', '$2y$10$wXDDvRiU1K1ipAfISI.t5OhS8iAeYIpv9gZsXfODuEUBdr4jhc/Qu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089636769737', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0023-2026', '3302262801900002', 'Karyawan swasta', 'Tambaksogra RT 09 RW 02, Kecamatan Sumbang, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460301' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Eka Meiliana
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eka Meiliana', 'B0214460301', 'B0214460301@kajian.griyaquran.web.id', '$2y$10$aZYoYlwAAiS6zSeT9LudWO0vjcx.7Kg7OiwQURsLDsg3Zr1Zv/W9G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089502191143', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0024-2026', '3302215405920005', 'Ibu Rumah Tangga', 'Tambaksogra RT 09 RW 02, Kecamatan Sumbang, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460301' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aan Rasiman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aan Rasiman', 'A0214460302', 'mamasalyayuais@gmail.com', '$2y$10$7adJQKowoAMhO7CyWqP4r.tnSe7qxMJ8.AgteuZxc21ZIYcdacPvO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083844329285', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0025-2026', '3303180302850001', 'Penjahit', 'Adoarsa,rt 8,rw 4,kecamatan kertanegara,kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460302' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nenih Agustina
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nenih Agustina', 'B0214460302', 'B0214460302@kajian.griyaquran.web.id', '$2y$10$zEAyIarf5RlTn7C0A2cZ2eZXOspbZjq9n6YNVDszA1Qcz6n2wKqVm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083876124001', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0026-2026', '3201146308910002', 'Ibu rumah tangga', 'Adoarsa,rt 8,rw 4,kecamatan kertanegara,kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460302' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Defy Kurniawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Defy Kurniawan', 'A0214460303', 'musyaffa.abdullah79@gmail.com', '$2y$10$v4qbX0b1g6ZYsSwajp9cye0FH3WpAnJTcU3tgljGqJ2R5R8snMxb6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088983517355', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0027-2026', '3303071612790002', 'Wiraswasta', 'Banjaran RT 8 RW 4 Kecamatan Bojongaari Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460303' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Fitriyani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Fitriyani', 'B0214460303', 'fitriummufadhil85@gmail.com', '$2y$10$ShXIUysFy31C8lKHz.RfCuXnremIfCS6uYK65IpKpth7do3uG..l2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088983212256', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0028-2026', '3303144806850005', 'Ibu Rumah Tangga', 'Banjaran RT 8 RW 4 Kecamatan Bojongaari Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460303' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Arif Wicaksono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Arif Wicaksono', 'A0214460304', 'arifsufyan288@gmail.com', '$2y$10$gDjGWPTgDXrEKovtyF.amuU8vGBVwzD/VVHC5jjjtmqcRBsuin782', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895357514456', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0029-2026', '3302030905920002', 'Guru', 'Bojong, RT 01 RW 03 Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460304' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Idha Alvianti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Idha Alvianti', 'B0214460304', 'idhaalvi@gmail.com', '$2y$10$.HUbFD4K.EA.WcyEMeAsXOfzGHuqqwBzk51tbCKX8D6OK7NBXGrKi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085726459348', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0030-2026', '3303054205910001', 'Guru', 'Bojong, RT 01 RW 03 Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460304' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Muallidin
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Muallidin', 'A0214460305', 'muallidinibnusyur@gmail.com', '$2y$10$saO94QirsKN3SbBdZh/g7OJxPhNAjsqmnEdSSiOav4MNuJVgXOI2a', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087763264149', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0031-2026', '5203131408910004', 'Guru', 'Dusun Melung RT 04 RW 05 Desa Larangan Kecamatan Pengadegan Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460305' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ani Tri Umayani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ani Tri Umayani', 'B0214460305', 'aniummuusamah@gmail.com', '$2y$10$6R/f.u2vPqlrMB7cj.NbEOI1PFHhT.JC/zQ/lq1C1mXd3chK16z22', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08741009602', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0032-2026', '3003165903950002', 'IRT', 'Dusun Melung RT 04 RW 05 Desa Larangan Kecamatan Pengadegan Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460305' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Fandiono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Fandiono', 'A0214460306', 'utsmanalfandiono83@gmail.com', '$2y$10$fXdV3o2vAxrLqCzuacEnRucXHxMUb2sTf4Hyl1LbASBanfdW8293W', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085642880377', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0033-2026', '3303062807830002', 'Perangkat desa', 'RT 1 RW 7 kecamatan Kalimanah, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460306' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ria susanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ria susanti', 'B0214460306', 'iassusanti88@gmail.com', '$2y$10$WdVyw4YdoBogTa8Fumwo6uoBfzJRt2afdgMPIGQNTdmt4VALEDidy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081515822677', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0034-2026', '2303024910860002', 'Ibu rumah tangga', 'RT 1 RW 7 kecamatan Kalimanah, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460306' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rusdiyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rusdiyono', 'A0214460307', 'yaminuwaies@gmail.com', '$2y$10$PjyfmqE3bC0r8cmQEcQN4.1z.u9YAfxCYd.uYVKrhlbr2ZMMXmhT6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082137579854', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0035-2026', '3303113004750001', 'POLRI', 'Desa Karanggedang RT.04 RW.01 kecamatan karanganyar kabupaten purbalt', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460307' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Suci pertamasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Suci pertamasari', 'B0214460307', 'rusdionosucipermatasari@gmail.com', '$2y$10$c8Axr7WdqxAdzJcMleD4DeHlq1nrxpHorTM460oAFpJmvsRPrzDPu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082324300133', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0036-2026', '3303115605830002', 'Ibu Rumah Tangga', 'Desa Karanggedang RT.04 RW.01 kecamatan karanganyar kabupaten purbalt', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460307' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aziz Zainur Rochman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aziz Zainur Rochman', 'A0214460308', 'azizzainur010@gmail.com', '$2y$10$2CxHg9t0pPbrPet6TLqD4O3ViyoCyrcY3/EZB19OYgcBiKRzFdbLu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083839010798', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0037-2026', '3302213009900001', 'Guru', 'Desa Karangturi RT 06 RW 01. kecamatan Sumbang Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460308' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti Fathonah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti Fathonah', 'B0214460308', 'sitifathonah8171@gmail.com', '$2y$10$11fnZDBdwZc0B2R8I0RY.uS93mvf7qnMg1xb2LoeH5sV6FVzlldIi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083836697264', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0038-2026', '3302214502930001', 'IRT', 'Desa Karangturi RT 06 RW 01. kecamatan Sumbang Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460308' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sutaryo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sutaryo', 'A0214460309', 'srs.exhaust1@gmail.com', '$2y$10$WfsJQGOa1T0duPR.IQtDDuphNQ.LCnCfTgvTU7QANXtlIT.H7m.MS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081285963000', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0039-2026', '3304032609890001', 'Wiraswasta', 'Jompo RT 03 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460309' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Iin nur Halimah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Iin nur Halimah', 'B0214460309', 'iin. nur3093@gmail.com', '$2y$10$gWhz.6Uv/Jo6pakcG7dB0O2XUHd6NTGfhBbQs0bdeeL/NmjJ5TSWG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089501374737', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0040-2026', '3301074405910001', 'Ibu rumah tangga', 'Jompo RT 03 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460309' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Alek Dianto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Alek Dianto', 'A0214450246', 'baemuis348@gmail.com', '$2y$10$.pjKegvCrXY5RpHetEdJIeSwRbn5pvliRr0JVjAiAAsPnFiF1LWC.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081228252231', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0041-2026', '3303081208870001', 'Pedagang', 'Desa selaganggeng RT 02 RW 02, kecamatan mrebet, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450246' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: PURWATININGSIH
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('PURWATININGSIH', 'B0214450246', 'baemuis3481@gmail.com', '$2y$10$wArjj3BN5aY43qmezFswvuAcKKftO5Z9aQzVZHENSyAdDA6VFgv8W', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081228252231', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0042-2026', '3303084701920001', 'Ibu rumah tangga', 'Desa selaganggeng RT 02 RW 02, kecamatan mrebet, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450246' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ady Achadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ady Achadi', 'A0214450247', 'adyachadi@unwiku.ac.id', '$2y$10$w7exk8zLw3lNPFA9k5gA2OzY63NPf3wxeRq7Sxbm8W172bWOCgI6q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0816695897', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0043-2026', '3302272606710005', 'Dosen', 'Jl. Riyanto GG. Mawar III no 265a, Kel. Sumampir, Kec. Purwokerto Utara, Kab. Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450247' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Indar Martanti Ariningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Indar Martanti Ariningsih', 'B0214450247', 'indarmartanti09@gmail.com', '$2y$10$gdbDRM4yYQPPLr55AQ1k4eJSedk9KCRElmbN9o2kKk1m6KilBNaTS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085291499698', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0044-2026', '3302276903750002', 'Ibu Rumah Tangga', 'Jl. Riyanto GG. Mawar III no 265a, Kel. Sumampir, Kec. Purwokerto Utara, Kab. Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450247' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Slamet Faozi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Slamet Faozi', 'A0214430170', 'slametfaozi28@gmail.com', '$2y$10$edDBQdJ5k8OSlSKxS9t.4eBm3XTm3AYVzPywhFAFziBSzFNiN9xEe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085540515556', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0045-2026', '33030109060002', 'Pedagang', 'Pegandekan RT 001 RW 002, Kecamatan Kemangkon, Kabupaten Purbqlingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430170' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450248' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rini nofitasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rini nofitasari', 'B0214430170', 'rini_nofitasari93@yahoo.com', '$2y$10$mJozi4BPPfNO.kkxz.XCjO3FnCnHgwSt28UCfpkGGyvzyurjyKEoq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085700900215', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0046-2026', '3303014111930001', 'Pedagang', 'Pegandekan RT 001 RW 002, Kecamatan Kemangkon, Kabupaten Purbqlingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430170' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450248' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: indra Meiga Buana
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('indra Meiga Buana', 'A0214440190', 'indra.mb@gmail.com', '$2y$10$rbq6gT9JdCz/8Xr/DdbfBOxU.7Nj5PZ90LJlygcThjrkjPJQkOPxW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085875814053', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0047-2026', '3303052305860003', 'Pedagang', 'Griya Kalika B9 Kedungwuluh, Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440190' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450249' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: yunia Triwulandari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('yunia Triwulandari', 'B0214440190', 'yuniatriwulandari14@gmail.com', '$2y$10$UUZxkEaCu6DGP1T3DKqwGeUxw6YlYc/KrpMfyuEjamE1f0lQhymPC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085799981421', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0048-2026', '3303055406860001', 'usaha Laundry', 'Griya Kalika B9 Kedungwuluh, Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440190' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450249' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: abdullah zaen
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('abdullah zaen', 'A0214450250', 'a_abdirrahman@yahoo.co.id', '$2y$10$addCJlHGoHUPWB9ph4WQBOCJZF5lCL6A5A5R3a4zjK8BOLUhHsUKC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081319839320', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0049-2026', '3303060107800003', 'ustadz', 'kedungwuluh rt 8 rw 2 kalimanah purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450250' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: aa tatariana ach dijana putra
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('aa tatariana ach dijana putra', 'B0214450250', 'B0214450250@kajian.griyaquran.web.id', '$2y$10$UUGTe.B/rMMyfkBltSxio.jh8bUYDTJXLqgoKIl5pxHBNEzYRL9UO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081318379721', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0050-2026', '3303065712790003', 'ibu rumah tangga', 'kedungwuluh rt 8 rw 2 kalimanah purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450250' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: BAMBANG ARDIYANTO
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('BAMBANG ARDIYANTO', 'A0214450251', 'danialabdulhoni@gmail.com', '$2y$10$URUAfG.Et8ZGUNH4Z3VL9eG4y3ct65hHbhAH08s3bqu1FmCxCBd2K', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087893195077', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0051-2026', '3303020112600003', 'Peternak Ayam', 'Desa Bukateja RT 04 RW 02 ,kec.Bukateja,kab.Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450251' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: SAMIAH
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('SAMIAH', 'B0214450251', 'danialabdulhoni1@gmail.com', '$2y$10$glPT/n3Hds19SQIedTehPOUnMmxlqkyeY0FcLZzN.6lCBMDgAMSR2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087893195077', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0052-2026', '3303024105710002', 'Pedagang', 'Desa Bukateja RT 04 RW 02 ,kec.Bukateja,kab.Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450251' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Show Andita Katon
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Show Andita Katon', 'A0214450252', 'katon.baee@gmail.com', '$2y$10$SfpRDbvUm1rgq0qls.uYiutpWUSbmI9OF8W8s3zpI6dW3EFQVJQcK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082136340364', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0053-2026', '3302190207870002', 'Karyawan', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450252' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460312' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Umi Kulsum
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Umi Kulsum', 'B0214450252', 'katon.baee1@gmail.com', '$2y$10$6OgLd27shP62/RgVrvyj9eRM4GBDcGG5FTBHnt/HyOW4OmE0x3ojO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087775212797', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0054-2026', '3327095107930005', 'Guru', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450252' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460312' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Heri dwiyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Heri dwiyanto', 'A0214450253', 'A0214450253@kajian.griyaquran.web.id', '$2y$10$46ejUsibAqzHbvo1jWE4DOtSrO5pWWZevF36rG7T1Lp8vvlKbnSWa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0055-2026', '3303062306840001', 'Pedagang', 'Kedungwuluh, karangwinong. Kec:kalimanah kab;purbalinggart02/04', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450253' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ririn ariastuti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ririn ariastuti', 'B0214450253', 'ririnzai@gmail.com', '$2y$10$nfSI2lT5Vw3d7JggxiEM4.RyPz0tfjGylNXh/VygxvOMrcjRjJx7i', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0882005766457', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0056-2026', '3303065206990002', 'Pedagang', 'Kedungwuluh, karangwinong. Kec:kalimanah kab;purbalinggart02/04', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450253' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sugeng Prijono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sugeng Prijono', 'A0214450254', 'asyaplatik82@gmail.com', '$2y$10$feP3IObDS6GQi6SKjzumpunM22ynDgyfHY0cLIuFc6vpaFdxTJKd6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085725846275', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0057-2026', '3303060104850001', 'Pedagang dan wirausaha', 'Klapasawit RT 02 RW 07,Kecamatan Kalimanah Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450254' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nurlaela
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurlaela', 'B0214450254', 'elafathan85@gmail.com', '$2y$10$nY.Pe9HCSa2GwP54ga6Goua06cK8p0TC/k4L.3JUGebvX1UVAskbm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085728663088', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0058-2026', '3303065904850001', 'Ibu rumah tangga', 'Klapasawit RT 02 RW 07,Kecamatan Kalimanah Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450254' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rakhmat Utama
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rakhmat Utama', 'A0214450255', 'rakhmat.utama@gmail.com', '$2y$10$ZBrJtzIQBArpsr12SMUm.uPexQIkkg65U9gWgDPYkyElmRUNQ0ytO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08812400791', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0059-2026', '3328060101790012', 'Karyawan swasta', 'Kober RT 3 RW 9 Kec.Purwokerto barat Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450255' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Tri Kukuh Agustini
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tri Kukuh Agustini', 'B0214450255', 'tkukuhagustini@gmail.', '$2y$10$IBE2syuoN64JjSJVqy7no.QHe8UdUc5G2.IOD6QRxNTeGFwW98AOi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0888066166', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0060-2026', '3328064506800009', 'IRT', 'Kober RT 3 RW 9 Kec.Purwokerto barat Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450255' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Tiyas Wibowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tiyas Wibowo', 'A0214430158', 'tiyaswibowo96@gmail.com', '$2y$10$bPjLq0VBVrgSpU/1y88oX.7vMLOssZ8pLWMXcnFacEgrIjpcNeCTC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08996688755', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0061-2026', '3303052912880002', 'Karyawan swasta', 'Purbalingga Lor Rt3 Rw3, kecamatan Purbalingga, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430158' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450256' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Eni Rahayu
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eni Rahayu', 'B0214430158', 'B0214430158@kajian.griyaquran.web.id', '$2y$10$5f4AQrTUdaZPB30ngTf0xOvLivQ79S7hlqhWHvJ6SYYPZJ2Y2uhpq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0062-2026', '3303054801930006', 'Ibu Rumah Tangga', 'Purbalingga Lor Rt3 Rw3, kecamatan Purbalingga, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430158' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450256' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Tegar hidayat
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tegar hidayat', 'A0214450257', 'tegarhidayat1131990@gmail.com', '$2y$10$0Wrv2cu6h3K0cAai3Hg4.O.LAaNomRegVC84u05KpXWsx.i2jX.Xq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085640750243', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0063-2026', '33030051103900004', 'Pedagang', 'Klapasawit RT 03/05 kecamatan kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450257' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Windiati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Windiati', 'B0214450257', 'tegarhidayato242@gmail.com', '$2y$10$BQIg3Ea9i3OdFadac.hqeubBd0b/Z7K2exhEoD95g4cufLCddkIZG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085803701154', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0064-2026', '3303064408880002', 'Ibu rumah tangga', 'Klapasawit RT 03/05 kecamatan kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450257' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rudy Nugroho
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rudy Nugroho', 'A0214450258', 'rudgulitpbg01@gmail.com', '$2y$10$xVbgqBrMzGpqUn0Eof71FuubI0sW1zVwl2bI29NNSP8UsJowr4.sC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085228072456', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0065-2026', '3303050701830003', 'Karyawan swasta', 'Jln.Puring Rt 01 Rw 04 Nmr 9A', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450258' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Heni Noviatin
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Heni Noviatin', 'B0214450258', 'ibrahimray231183@gmail.com', '$2y$10$3kxPHsoMfNwAIcZCQYzgnuHqr6dTNf.HibmItzwmTrNA9b381596C', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085228071887', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0066-2026', '3303056311830001', 'Ibu Rumah Tangga', 'Jln.Puring Rt 01 Rw 04 Nmr 9A', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450258' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sarip
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sarip', 'A0214430154', 'sarifabufatih@gmail.com', '$2y$10$.18rMhaPngySZpoz2brkau.aNjLqllpJcmyRaffkhICzolrSv4Fia', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081227366884', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0067-2026', '3304031504820007', 'Karyawan', 'Desa Blimbing RT.05/02 kec.mandirja', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430154' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450259' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti Mukhoyatun
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti Mukhoyatun', 'B0214430154', 'siti.mukhoyatun@gmail.com', '$2y$10$tNgS3vxcfdIEq29QRG7PmuZJFYbWKlypxUKyCze4weUpcXqJ04Njy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08220227309', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0068-2026', '3304036201880002', 'Ibu Rumah Tangga', 'Desa Blimbing RT.05/02 kec.mandirja', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430154' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450259' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Afit Setiadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Afit Setiadi', 'A0214420135', 'afitsetiadi@gmail.com', '$2y$10$LhEH3q8WrFNEVzc8h7C78ePzhog3kF7qW0kwcZF4EXpZLar6EdxLG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085713676679', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0069-2026', '3303052406880002', 'Karyawan', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420135' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450260' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Septi Wulandari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Septi Wulandari', 'B0214420135', 'nadhira.rs@gmail.com', '$2y$10$akkQNWzh2f3HfajBAet8XOeqyqeRXiqLrs1WFqfXFHFkCO66tnKh2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085713907978', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0070-2026', '3303055409890002', 'Ibu Rumah Tangga', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420135' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450260' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Bagus Pamungkas Pitaloka
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Bagus Pamungkas Pitaloka', 'A0214420134', 'dbaguse4748@gmail.com', '$2y$10$GO35mpgaom4jKQXOMwDrDOCijUxAZneHkVXuTVflikOiIHnZZmNYm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082147484913', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0071-2026', '3304101406820012', 'Pengajar', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420134' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450261' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Vinna Damayanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Vinna Damayanti', 'B0214420134', 'vinnadamayanti001@gmail.com', '$2y$10$yt2Anx.K7Jj2CH5oR83xG.qrgoCdU8zGaLVFeUonX5kUMkp8f6fEu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895369459879', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0072-2026', '3304105806910001', 'Pengajar', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420134' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450261' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Nur Mukti Wibowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nur Mukti Wibowo', 'A0214450262', 'mukti.wibowo88@gmail.com', '$2y$10$Rv0svwkg3B7KfrInY/elG.woCDy5uPfSfRBCxdi0UKeiOXWTqPFgC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085808543932', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0073-2026', '3303050805880001', 'Broker tanah dan rumah', 'Jetis rt 14 rw 05, Kecamatan Kemangkon, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450262' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ria Yunita
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ria Yunita', 'B0214450262', 'ryayunita.azzahra@gmail.com', '$2y$10$LxHLr8XX5/iINtc58RPazu/8IrWj6h9BgGOSMsFtbJLLw6RSJkZFO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085876797177', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0074-2026', '3303016206880001', 'Catering online', 'Jetis rt 14 rw 05, Kecamatan Kemangkon, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450262' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Supyan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Supyan', 'A0214450263', 'A0214450263@kajian.griyaquran.web.id', '$2y$10$P5BbveHgki6ccoq5eDIv6uyDHB0WRB1CsjiD3y6yov2lIse2ho6ce', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0075-2026', '3302220507850001', 'Pedagang', 'Banjar Anyar RT 01 RW 05, Kecamatan Sokaraja, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450263' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Memah Setiamah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Memah Setiamah', 'B0214450263', 'sofyanmemah@gmail.com', '$2y$10$vo0uqboKME8ansxokdD/cevr3sUVjyKznbkDY7GM25l2GCwQGtjI6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085292908280', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0076-2026', '3302195507880004', 'Ibu rumah tangga', 'Banjar Anyar RT 01 RW 05, Kecamatan Sokaraja, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450263' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Febrian Prabawa Hakim
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Febrian Prabawa Hakim', 'A0214410089', 'ukasyahibnuna@gmail.com', '$2y$10$dhcCFjfNDxOQunGsdsMYROIoD9gzrM0C7KeROEgqJDQoZKgeHd36K', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081326264191', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0077-2026', '3303052602880001', 'PNS', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410089' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450264' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nurul Layli Mafthuhah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurul Layli Mafthuhah', 'B0214410089', 'laylimafthuhah@gmail.com', '$2y$10$8d.YRjAPYDG6AvqqnVtZo.npScHo3VbeOSGhkhSFRUMTUOOYlztDS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082123415020', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0078-2026', '3303074511890001', 'Perawat', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410089' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450264' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eka Fatnurokhman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eka Fatnurokhman', 'A0214450265', 'featureka65@gmail.com', '$2y$10$ylenlFLT3ly3e9b5xm1DouwmfRjUP.fiHBlIpIszRUEEGFnsWEVBu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085287927368', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0079-2026', '3302271811810004', 'Dagang', 'Karangwangkal RT 03 RW 01 Purwokerto Utara', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450265' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nunik Setiyowati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nunik Setiyowati', 'B0214450265', 'nunik.setiyowati.2018@gmail.com', '$2y$10$susqVuXmkR47cFWo7I1jgeONenwEJX0MYsG5dlo.1Wf/e1QDkm2Yu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085876040235', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0080-2026', '3302277011810004', 'Swasta', 'Karangwangkal RT 03 RW 01 Purwokerto Utara', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450265' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: DWI NURHIDAYAH
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('DWI NURHIDAYAH', 'A0214440201', 'dwinur23577@gmail.com', '$2y$10$tKwGaw.8Ll8Z3kU.WkJq/.u3sMWPdT5PowuESIdnwPOqk7GY0yMAe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081902966544', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0081-2026', '3303062305770001', 'Karyawan swasta', 'Jl. Mawar 487 Rt. 4 Rw. 4 kalimantan wetan  kec. kalimantan kab.Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440201' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Zaenatun nur'aini
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Zaenatun nur''aini', 'B0214440201', 'zaenatun2401@gmail.com', '$2y$10$yRgavneRBwc/VcKtKtiTuO7jSFxYUf8sVMYVMSjmLa8nXILhGM0oa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085640240936', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0082-2026', '3303066401840004', 'Karyawan swasta', 'Jl. Mawar 487 Rt. 4 Rw. 4 kalimantan wetan  kec. kalimantan kab.Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440201' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Edi setiawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Edi setiawan', 'A0214410083', 'haddid.di@gmail.com', '$2y$10$225Es/.IHM560hh8p7SOY.n5M5N3WFS8ALbBX2bhugDg6wMMYdRxK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081327422222', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0083-2026', '3303062002830004', 'Wiraswasta', 'Dukuhcakra rt4/rw4 kec. Kalimanah kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410083' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440202' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nafritin dwi ratnasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nafritin dwi ratnasari', 'B0214410083', 'vievyt@gmail.com', '$2y$10$pReVQpY10WBcsLUnRfIsVu5/GtB/v7v212hNdmNMqbeOlkQyJMpke', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085876345192', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0084-2026', '3303095111880003', '-', 'Dukuhcakra rt4/rw4 kec. Kalimanah kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410083' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440202' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Gendon Puraba Fardhona
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Gendon Puraba Fardhona', 'A0214440203', 'armadhona@gmail.com', '$2y$10$IElI25i/W7ScoqJEUyWQZOQpfU6Is4aQkttc16Q7xTL134Dy.kToS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082329298583', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0085-2026', '3303152812900001', 'Wiraswasta', 'Padamara, RT 03 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440203' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460328' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Aisyah Zahidatuz Zahra'
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aisyah Zahidatuz Zahra''', 'B0214440203', 'aisyahzzahra@gmail.com', '$2y$10$8KNB2QuWZTFXndiLE0Zh9.1EOBrGVUso.D4WqbylU9GfFP2HGvmXq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082323210363', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0086-2026', '3515064106950001', 'Ibu Rumah Tangga', 'Padamara, RT 03 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440203' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460328' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Teguh Wardiyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Teguh Wardiyanto', 'A0214440204', 'A0214440204@kajian.griyaquran.web.id', '$2y$10$u7mmqwYZdrv33ke8VmOq0uUEsy3zfkSJVhrrJQzkUM6Xzb7uoDGC.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089510923327', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0087-2026', '3303062709810001', 'Wiraswasta', 'Kalimanah wetan RT 04 RW 07, kecamatan Kalimanah, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440204' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Kusyuliati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Kusyuliati', 'B0214440204', 'kusyuliati@gmail.com', '$2y$10$A9lZbhNJewrmoYQVx6J0oODddp1aLuPY6ghnMLoRYffQbySPACwO.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089637014760', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0088-2026', '3303066503830002', 'IRT', 'Kalimanah wetan RT 04 RW 07, kecamatan Kalimanah, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440204' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Iwan Setiawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Iwan Setiawan', 'A0214440205', 'esteween@gmail.com', '$2y$10$5owmZqd5rBUloUKFqJafQOs52Yj5NDBo1xIiCFiU0GMivUtDrItTi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08122851515', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0089-2026', '-', 'Dosen/Peneliti', 'Babakan Asri Blok A12, Babakan, Kec. Kalimanah, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440205' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ayatul Fauziyah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ayatul Fauziyah', 'B0214440205', 'ayatulfauziyah1@gmail.com', '$2y$10$0oEX.MrywqbYrar8176CC./wV5PnqegqRNEDrUD5dfH2tFPhdb.5O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085713698969', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0090-2026', '-', 'Ibu Rumah Tangga', 'Babakan Asri Blok A12, Babakan, Kec. Kalimanah, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440205' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mahmud
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mahmud', 'A0214420116', 'mahmudridlekosongdua@gmail.com', '$2y$10$K7uLwzW0NC0p9KD.fXXfaesZZYUPCG2xeiScswJZfxepRdCDLG6.G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085811139560', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0091-2026', '3302141006900008', 'Karyawan warung makan', 'Lambur RT 01 RW 01,kecamatan mrebet, Kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420116' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440206' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Restiani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Restiani', 'B0214420116', 'mahmudakhi019@gmail.com', '$2y$10$OIQIDHufKkLxf8hD4nwWD.W4ul4s.aq2eYX/8gzlDWCdchoOIGH/m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089526852508', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0092-2026', '3303087009920001', 'Ibu rumah tangga', 'Lambur RT 01 RW 01,kecamatan mrebet, Kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420116' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440206' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Dwi Yugo Apriyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dwi Yugo Apriyanto', 'A0214440207', 'dwiyugo82@gmail.com', '$2y$10$GDokUGFn1QXhgWpDQ.IXVO9ENUENyaZzNREyxoZTO.X.DXueBWfuC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0822214236746', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0093-2026', '3216221804820002', 'Wirausaha', 'Kalikajar RT 03 RW 08 kecamatan Kaligondang kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440207' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Susmiati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Susmiati', 'B0214440207', 'B0214440207@kajian.griyaquran.web.id', '$2y$10$vC5lhgFze6hCOqoVQZ7zj.VYxYn8q4kh7mrMLTAOzJOAVDYAChMAG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088221771254', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0094-2026', '3216225710810002', 'Ibu rumah tangga', 'Kalikajar RT 03 RW 08 kecamatan Kaligondang kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440207' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sudino
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sudino', 'A0214440208', 'dinobersaudara03@gmail.com', '$2y$10$xIB79w9SPFgZTctxuAXU8e2qy2CB2il6g1a7apDJAvtJzLz09.yI2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081280479703', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0095-2026', '3303150308910001', 'Buruh harian lepas/serabutan', 'Bojanegara rt 5 rw 1 padamara, Purbalingga ( sedang tinggal di kedungwuluh rt 6 rw 1)', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440208' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460329' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti kurniati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti kurniati', 'B0214440208', 'dinobersaudara031@gmail.com', '$2y$10$t.JTVkOhuSayZebYPIDinOTI2MWSnnj43PadFW9GJlc50gfylUCBq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081280479703', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0096-2026', '3303075605910004', 'Ibu rumah tangga', 'Bojanegara rt 5 rw 1 padamara, Purbalingga ( sedang tinggal di kedungwuluh rt 6 rw 1)', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440208' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460329' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Widi Utomo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Widi Utomo', 'A0214410092', 'akhwidiutomo@gmail.com', '$2y$10$HgjLffLYUdH7/xUP90uruuXYLwJ/HPLb6iZLtXaEPaM0lWJV7oWGW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085292158052', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0097-2026', '3275062212890010', 'Karyawan', 'Karangpule RT 03 RW 02, Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410092' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440209' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Anisa Novitasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anisa Novitasari', 'B0214410092', 'B0214410092@kajian.griyaquran.web.id', '$2y$10$G/nZ9eVL7KYeBsNSkrp5XOdo3Gu39.l6N1JC7kkIwXZL7ghsEL64C', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085292158052', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0098-2026', '3306044411890001', 'Guru tk', 'Karangpule RT 03 RW 02, Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410092' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440209' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: kurniawan Pamuji
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('kurniawan Pamuji', 'A0214440210', 'kurniawanpamuji27@gmail.com', '$2y$10$AzXhvtnfZVqisucAFLFXN.LgjHnAMLjAQmk3wI9LuSzIsM2z7BrRu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082323179892', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0099-2026', '3302202712880001', 'Tani', 'Kramat Rt 05 Rw 01, Kecamatan Kembaran Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440210' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Windi Anita Puspasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Windi Anita Puspasari', 'B0214440210', 'B0214440210@kajian.griyaquran.web.id', '$2y$10$Nc2UVs0TF6TpPh6MLQNxH.dQFWj1eyYJ3j4ZNFvJ7WDlOB3vjkQAa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0100-2026', '-', '-', 'Kramat Rt 05 Rw 01, Kecamatan Kembaran Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440210' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Dani Nur Sya'ban
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dani Nur Sya''ban', 'A0214420114', 'alwireyhan19@gmail.com', '$2y$10$/fOA58jI1zpKpU2aD715ZO0Yqmvu4BvTflgUlbs9cVysTc2GYn.SG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085647945661', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0101-2026', '3303051906810002', 'Dagang', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420114' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440211' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Suntarni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Suntarni', 'B0214420114', 'alwireyhan191@gmail.com', '$2y$10$cvVMCJd5pEd8jqELmaLG5OVFve9UmIexjdGwEBjSC15VjmINQIaFe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085647945661', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0102-2026', '3324016802890002', 'Dagang', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420114' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440211' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Anom Erlangga
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anom Erlangga', 'A0214440212', 'A0214440212@kajian.griyaquran.web.id', '$2y$10$ztrYM2qUZn5ibv05mCG2rOOHmT8LWw8g9JLzdIGzsoXmfriL5HvMG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081353089678', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0103-2026', '3303120703780003', 'Pekerja swasta', 'Karang anayr rt02/01 Kec Karamganyar kab purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440212' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sri Supriyatiningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sri Supriyatiningsih', 'B0214440212', 'B0214440212@kajian.griyaquran.web.id', '$2y$10$YiySGdw/CQmOmhsmhSnpf.0Mm95jkL/qnsv0xNNYoSTWe8EIv10qC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082242635133', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0104-2026', '3303124205790003', 'Ibu Runah Tangga', 'Karang anayr rt02/01 Kec Karamganyar kab purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440212' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Saepuloh
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Saepuloh', 'A0214440213', 'ipung859@gmail.com', '$2y$10$50uf.KvYKX7hhaBgxe5rge2cGApenIO4NFpAKYfxLK8AdXsIBX58G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085726000375', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0105-2026', '3329061002860001', 'Pegawai Swasta', 'Selabaya RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440213' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460320' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rini Muliasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rini Muliasari', 'B0214440213', 'rinimuliasari0505@gmail.com', '$2y$10$GJ127iZof5zcsnjFYfdM7..L7/waXcJ.SYUK8S6ScztTr.v806kzG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082134294097', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0106-2026', '3303065401890004', 'Ibu Rumah Tangga + Tutor PKBM', 'Selabaya RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440213' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460320' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Imam fajar subekhi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Imam fajar subekhi', 'A0214410099', 'galerimartpurbalingga76@gmail.com', '$2y$10$QdslsLbe2CAkXL.5V/8mOe11WEvXCZ4xFa3Nfa69Np6/ghrUffG0q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085226970555', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0107-2026', '3303140703870003', 'Wiraswasta', 'Bojongsari rt 02 rw 05 purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410099' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440214' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Anisa nurfitri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anisa nurfitri', 'B0214410099', 'galerimartpurbalingga761@gmail.com', '$2y$10$IWe84RCz0rUHydzy39JkBu/.c90m0uhwQ6Cgjewi5ULaYrzwti4/y', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082323723069', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0108-2026', '3329036011920007', 'Wiraswasta', 'Bojongsari rt 02 rw 05 purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410099' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440214' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mingan Sutomo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mingan Sutomo', 'A0214440215', 'A0214440215@kajian.griyaquran.web.id', '$2y$10$QnzmodsnfJEsbbOnoYiXMudiBwOhHjpacu2.Ip6GcIhr6DBhsch1C', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081289997557', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0109-2026', '3303061201810003', 'Karyawan pondok', 'Kedungwuluh RT 07 RW 01,Kecamatan Kalimanah,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440215' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sri Partini
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sri Partini', 'B0214440215', 'B0214440215@kajian.griyaquran.web.id', '$2y$10$Hcvw0RdmIVl8MbX.jqbA2e6o7DtsAtpOfwM/RHRde5nrGmWsZL8Ca', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081289997557', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0110-2026', '3303065409810002', 'Ibu rumah tangga', 'Kedungwuluh RT 07 RW 01,Kecamatan Kalimanah,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440215' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Arif Tri Pamungkas
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Arif Tri Pamungkas', 'A0214440216', 'pamungkasariftri@gmail.com', '$2y$10$c/dCkOvZ6Yi.D2w6jMxJUOqVMFmGnPxfPSqCwvcEEop8Yl.eyyZ/q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08122954123', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0111-2026', '3303012604830003', 'Supir', 'Jl kenanga no 1 RT 3 RW 7 perumahan penambongan Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440216' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Gita Pratiwi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Gita Pratiwi', 'B0214440216', 'tiwi.tywoel@gmail.com', '$2y$10$ZLSDgz46eNTHZ8mDaF/S5.HpZLE9ZvfJYC8n8cOyHStGwd8msm6GO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08158085400', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0112-2026', '3303054705860001', 'Ibu Rumah tangga', 'Jl kenanga no 1 RT 3 RW 7 perumahan penambongan Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440216' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Roni Eko Prastyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Roni Eko Prastyono', 'A0214440217', 'roniprasetyono80@guru.smk.belajar.id', '$2y$10$XKEVCFAMGNgi0D07pmkFHO7bvbDd0CQlDJfYvU0THz5gnIxmFYQGC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082313255402', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0113-2026', '3303152810800004', 'Asn Guru', 'Babakan 6/2 Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440217' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ambar Sriutami
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ambar Sriutami', 'B0214440217', 'ambarutami75@gmail.com', '$2y$10$C.CfCp1PqXUeeT2/O3/sPu4I0bGaRL2VfpaDX5GJH8pqq38SvxEvi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082134278687', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0114-2026', '3303155611810003', 'ASN Guru', 'Babakan 6/2 Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440217' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Teguh pambudi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Teguh pambudi', 'A0214420113', 'pambudit648@gmail.com', '$2y$10$bsPfCUFlcolrezsM4KXpKe5paxS2fbBZs1nEUr5KrYPiGV0oTK.H2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081215410246', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0115-2026', '3303141207790002', 'Padagang', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420113' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440218' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Anik lusiani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anik lusiani', 'B0214420113', 'aniklusianianiklusiani@gmail.com', '$2y$10$r6BtkER1N4aUnEqo09bha.qS05VupUcs7h41zzgMGJrqIurQ0EX1.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089542796366', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0116-2026', '3303146708840002', 'Ibu rumah tangga', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420113' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440218' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Koswara mohamad faiz
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Koswara mohamad faiz', 'A0214440219', 'mf078061@gmail.com', '$2y$10$4AXnprRGL5GfKnigdsRMN.n5patBDrAiQLrt9Vpx4sjT7GUcKYBku', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081809415955', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0117-2026', '3303141303720001', 'Pedagang', 'Desa Karangbanjar Rt19 rw08 kecamatan bojongsari kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440219' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siswati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siswati', 'B0214440219', 'B0214440219@kajian.griyaquran.web.id', '$2y$10$bvX63f1ZDaKQkBduF2kQ7e6s9ufyq/ONQMvVmqBlEi76iVZdzTB7C', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081802815588', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0118-2026', '3303146010800001', 'Ibu Rumah Tangga', 'Desa Karangbanjar Rt19 rw08 kecamatan bojongsari kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440219' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rifqi Agung Mulyadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rifqi Agung Mulyadi', 'A0214440220', 'isyarif31@gmail.com', '$2y$10$L.9SRtBOf7N542x9Al8AdejrrT243yjQXNBZs.X5158v7xpzZdmX6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08972968222', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0119-2026', '3302080506920002', 'Karyawan Swasta', 'Kedungwuluh Rt 08 Rw 02', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440220' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460322' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Asih Fatonah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Asih Fatonah', 'B0214440220', 'asihfatonah36@gmail.com', '$2y$10$l8Q0YCYVVLVYLwTfIL/4YOQJzWVKh4vh4xaZKG6xIfedDw5Xm9P02', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089508982373', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0120-2026', '3303104306930003', 'Ibu Rumah Tangga dan Privat Online', 'Kedungwuluh Rt 08 Rw 02', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440220' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460322' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sutrisno
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sutrisno', 'A0214430147', 'inomase3@gmail.com', '$2y$10$bBD7JKmupttwrDCEcRXVG.CFirX1xAdMOHxZJhmCJ94OX2Kg8Q8BS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089637360588', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0121-2026', '3303060709850001', 'Pedagang', 'Grecol RT 05 RW 01 Kecamatan Kalimanah ,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430147' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sunartini
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sunartini', 'B0214430147', 'inomase31@gmail.com', '$2y$10$t5fbOKM8EvtKWq3sbt1KgeBd5Em4hc.7bjXwjJPLzGUVDsp8ZvpDW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089637360588', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0122-2026', '3276024409910009', 'Ibu Rumah Tangga', 'Grecol RT 05 RW 01 Kecamatan Kalimanah ,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430147' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rokhanudin
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rokhanudin', 'A0214430149', 'rohan nudin@ gmail.com', '$2y$10$7Gy2h7L7TrXhEz.lgF56z.7sA5s4LSAc.q9A5laSzysqn5Q38WyIC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085848701806', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0123-2026', '3303071908790001', 'Buruh', 'Ds.Walik Kutasari RT 16 RW 08 Kecamatan Kutasari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430149' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rokhanah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rokhanah', 'B0214430149', 'B0214430149@kajian.griyaquran.web.id', '$2y$10$V/iytHQfDow6nLOQS2A2YuUXjOQR4eP8IX6B4ZP98ZAAnmmC7Nyqy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085725962454', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0124-2026', '3303074701810001', 'Ibu Rumah Tangga', 'Ds.Walik Kutasari RT 16 RW 08 Kecamatan Kutasari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430149' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Jupri Santoso
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Jupri Santoso', 'A0214430150', 'bankumpbg@gmail.com', '$2y$10$Xhx.VK9ES5na/OIgMYWEDe3GMOPfOM3nrQQnRWR2PtMHYl7XWNDxW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085742540922', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0125-2026', '3303060302840002', 'PNS', 'Kedungwuluh RT 06 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430150' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: An Nisaa Pratiwi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('An Nisaa Pratiwi', 'B0214430150', 'tiwisolehah@gmail.com', '$2y$10$BKyUfEbDNsVlWRvYEFSHKO39sTcjFy.nKkJ1TzwOMGErRwm12zjIW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085292505941', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0126-2026', '3303914503870001', 'Guru TK', 'Kedungwuluh RT 06 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430150' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Teguh Rahyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Teguh Rahyono', 'A0214430151', 'teguhrahyono23@gmail.com', '$2y$10$LJqouAwkBveJnnCcdspHvu974kTYSimlHmVIKpAKmUwvMkktxh5Vq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082327212554', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0127-2026', '3275022301810014', 'Wirasuasta', 'Kedungwuluh RT 07 RW 02 Kecamatan  Kalimanah Kabupaten  Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430151' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ade Ernawati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ade Ernawati', 'B0214430151', 'adeernawati530@gmail.com', '$2y$10$TxUMJqb2bqGA3UNgJHDqZ./1dBqJ5238pMkcROpj0TreArU0VUFDS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081296046421', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0128-2026', '3201385907850002', 'Ibu rumah tangga', 'Kedungwuluh RT 07 RW 02 Kecamatan  Kalimanah Kabupaten  Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430151' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sugiyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sugiyanto', 'A0214430152', 'sugiyantoleader@gmail.com', '$2y$10$sNFagmfiRRtnBwkX7r2hku6iLnIq.u9JlzEHR5UN4BX3E0i66ImcK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083821253135', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0129-2026', '3303060208850002', 'Servis AC', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430152' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450266' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Puji Listiani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Puji Listiani', 'B0214430152', 'liestiani28@gmail.com', '$2y$10$h3KplfN2CnLa.EZuk9IvS.3.vB8BP.MthRD8hVJPrxA7xq5wH0GzG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089508676796', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0130-2026', '3303066807850001', 'Ibu rumah tangga ( GQ Tunas Ilmu )', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430152' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450266' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Atma Nurseto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Atma Nurseto', 'A0214460285', 'nursetoatma@gmail.com', '$2y$10$nHqV0IzyD/hKGm5lCHi84e3zPwAmP9T1R3c7iYRoXndc1YMXc51tO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085727051663', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0131-2026', '3302201111880001', 'Guru', 'Ds. Suro RT 06/04, Karangjati, Kecamatan Kalibagor, Kabupaten Banyumas, Jawa Tengah, Indonesia 53193', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460285' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460286' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Dian Anisa Listya
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dian Anisa Listya', 'B0214460285', 'diananisalistya@gmail.com', '$2y$10$w3NyvIJZ6Dy66aGmw7Q0Qu7TdWd7Bj93wOu8D9VjCII/bYTc67FiC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081568270248', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0132-2026', '3302247006880002', 'Guru', 'Ds. Suro RT 06/04, Karangjati, Kecamatan Kalibagor, Kabupaten Banyumas, Jawa Tengah, Indonesia 53193', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460285' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460286' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Gusbada adi kusumpraja Iskandar
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Gusbada adi kusumpraja Iskandar', 'A0214430155', 'gusbafa.adi@gmail.com', '$2y$10$C60iwE8IPfN4BTfbMvQE1uynqfrZlBn8x8mKO5mn3vaLxmwMt8paS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085743129000', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0133-2026', '3402012310830001', 'Peternak', 'Grumbul kikiran, desa Silado, Sumbang, banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430155' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ariyanti rosaliani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ariyanti rosaliani', 'B0214430155', 'ariyanti.rosaliani@gmail.com', '$2y$10$e29sHx..OA2bxedSwFSErOA4TSe2l7JUCXv7jhn0bOWT.R8hzABlK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085743371805', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0134-2026', '3402015605840001', 'Ibu rumah tangga', 'Grumbul kikiran, desa Silado, Sumbang, banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430155' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ajis
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ajis', 'A0214430156', 'abuhaikal754@gmail.com', '$2y$10$g8a8fK/eGoSP830FsU0KEOQZucSUVtRjSBnXV4Sal8nIIFD.45yHy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085228063495', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0135-2026', '3303062008820003', 'Pedagang sempol ayam', 'Kalikabong rt2/rw1.kec.kalimanah.kab.purbalingga.sekarang domisili klahang rt1/rw6.kec.sokaraja.kab.banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430156' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Agustinidewisaputri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Agustinidewisaputri', 'B0214430156', 'ummahusain306@gmail.com', '$2y$10$fRPLMy4/tzHwpWZLtGW0yOhljrCVXzSd/U/oRMS1Ifb0oDneYiwWO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087823631010', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0136-2026', '3302194308890001', 'Ibu rumah tangga', 'Kalikabong rt2/rw1.kec.kalimanah.kab.purbalingga.sekarang domisili klahang rt1/rw6.kec.sokaraja.kab.banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430156' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aris Dianto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aris Dianto', 'A0214420122', 'haritsabyan122@gmail.com', '$2y$10$GxME4mNw8ob2HPr5zursEecnCUmElEHauY1iMKWiJAwjvkN1HlENy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081327970602', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0137-2026', '3303051304830004', 'Wiraswasta', 'Jl Komisaris Noto Soemarsono 124 b  RT 03/02 Purbalingga 53313', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420122' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430157' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Titin Agustriani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Titin Agustriani', 'B0214420122', 'titin.agz@gmail.com', '$2y$10$oFkO09VKLmmPPfLklf.o7.bj083PFTrbC0U2IHm7mJk86LQPRtAlK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227227483', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0138-2026', '3303044408880001', 'Ibu Rumah Tangga', 'Jl Komisaris Noto Soemarsono 124 b  RT 03/02 Purbalingga 53313', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420122' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430157' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: AGUNG PRIYAMBODO
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('AGUNG PRIYAMBODO', 'A0214430160', 'indigo.agung@gmail.com', '$2y$10$7slFlcaFF8wPbTL9FNH9o.iiHaRaC3iQMKEWkiTBqg9kVoK4061gO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085747777075', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0139-2026', '3303141307830001', 'Wiraswasta', 'Klapasawit RT 04 RW 05, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430160' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: EVA SUSANTI
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('EVA SUSANTI', 'B0214430160', 'mbaevasusanti@gmail.com', '$2y$10$5Kf8Aj8GXzLDxVLSomVj5OnaNGv2HjkCCLgviWNTrNJKK5dfy3e4u', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085641009500', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0140-2026', '3303065902910001', 'Ibu rumah tangga', 'Klapasawit RT 04 RW 05, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430160' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Arif Hidayatullah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Arif Hidayatullah', 'A0214410080', 'abumamah@gmail.com', '$2y$10$hlostYsO/L1VrFRwIrQOLeXiLVsGYrP8DlFgD755jKtQYsAow/n0O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082135366338', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0141-2026', '3302112404810005', 'Ustadz', 'Banyumas Rt 01/04, kecamatan Banyumas, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410080' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430162' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Dhurrotun Nasikah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dhurrotun Nasikah', 'B0214410080', 'abumamah1@gmail.com', '$2y$10$FvZPp2FitMlHVqBpAhNtwOw14EXXDet5GrYrl9ZxzQ.in6wfe7S7m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082135366338', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0142-2026', '3302116412840002', 'Ibu Rumah Tangga', 'Banyumas Rt 01/04, kecamatan Banyumas, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410080' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430162' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Dwio Ratono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dwio Ratono', 'A0214430163', 'A0214430163@kajian.griyaquran.web.id', '$2y$10$Y4TC9oBCy2kyolB6iQeoxu9LKqRi/IFc1Eh.GNevYTzuQlMJa52fy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081542728260', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0143-2026', '3303062805710002', 'Swasta', 'Kalikabong RT 01/ RW 01, Kecamatan Kalimanah, Kabupaten PurbalinggaP', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430163' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Dwi Ambarsari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dwi Ambarsari', 'B0214430163', 'dwiambar116@gmail.com', '$2y$10$AjY4LzSk7f.5mc.6UD552.6B4Q9RNWnqwF7eNggs38r7/YDuZp4TW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082243510290', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0144-2026', '3303064111750002', 'Pedagang', 'Kalikabong RT 01/ RW 01, Kecamatan Kalimanah, Kabupaten PurbalinggaP', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430163' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Darsun
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Darsun', 'A0214420123', 'unaisummu16@gmail.com', '$2y$10$fo/5adG8wRB5D4NYhAEpz.LvnCN6KtKsAnSVx0Kwd3XZ08iF5put6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085929883236', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0145-2026', '3302212711830002', 'Wiraswasta', 'Kotayasa RT 08 RW 05, Kecamatan Sumbang, Kabupaten, Banyumas,', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420123' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430164' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Suci Uswatun Hasanah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Suci Uswatun Hasanah', 'B0214420123', 'unaisummu161@gmail.com', '$2y$10$VMQWvZsKXoXuSjmWh7OsvebC/Jcd0.BhGpfObBueQ6Y4CC7iP3XPa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085929883236', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0146-2026', '3302216212820002', 'Ibu Rumah Tangga', 'Kotayasa RT 08 RW 05, Kecamatan Sumbang, Kabupaten, Banyumas,', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420123' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430164' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Anjar Triwibowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anjar Triwibowo', 'A0214430166', 'triwibowoanjar@gmail.com', '$2y$10$D1ivzioTvW6kmDgaNZWPmOSyHBonNiu8OblpmhDPDeZquLDiclKSS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085777322111', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0147-2026', '3301021606870007', 'Wirausaha', 'Kedungwuluh 02/04 Kec.Kalimanah Kab.Purbalingga.', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430166' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Fitri Mulyani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Fitri Mulyani', 'B0214430166', 'triwibowoanjar1@gmail.com', '$2y$10$umermp514rIwDbTtEjsPFOEVhc8ZRi1IaFEA9EyvN5N14U0B3pPxy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081390332133', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0148-2026', '3302254602900002', 'Ibu Rumah Tangga', 'Kedungwuluh 02/04 Kec.Kalimanah Kab.Purbalingga.', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430166' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mohammad Lathief Shidiq
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mohammad Lathief Shidiq', 'A0214420107', 'bismillah.shidiq@gmail.com', '$2y$10$jABcrJzYOkOQGnjEQ4LS.OZ.J.7jpuxMQfog2o.xls8JN5XFPMfCS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227924913', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0149-2026', '3303062805880002', 'Guru', 'Kedungwuluh RT 05 RW 01, Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420107' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Anita Rahayu
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anita Rahayu', 'B0214420107', 'alifahhasna09@gmail.com', '$2y$10$WsIGRCX5e4pcZ..NbTZerehfPIYEKs8QPcQHTchq4F3KuWT.1b50G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082227068015', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0150-2026', '3303046609960004', 'Ibu rumah tangga', 'Kedungwuluh RT 05 RW 01, Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420107' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Wahyu hidayat
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Wahyu hidayat', 'A0214420108', 'wahyuhidayathidayat047@gmail.com', '$2y$10$TFYCYDai0g5kC/ybHuxC2Og3wdmxUbgSFa5BxT/S6ebN.d6auYCxO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089654958996', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0151-2026', '3303140411820001', 'Pedagang', 'Kedungwuluh rt 07 rw 01, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420108' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460318' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti khuswatun hasanah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti khuswatun hasanah', 'B0214420108', 'siti khuswatunhasanahsitu@gmail.com', '$2y$10$3/AWIYz56SnNf8hXhlzsfOrNYOsCfifeO2MTV4SfSFjwwB4E65Adu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089647653800', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0152-2026', '3303144708880003', 'Ibu rumah tangga', 'Kedungwuluh rt 07 rw 01, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420108' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460318' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ali kurniadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ali kurniadi', 'A0214420109', 'radjakaisar@gmail.com', '$2y$10$yMT.Dxd3QIHEaqan6nRYdOI0DlBLMneSll4T/SonjFgoQAlF6IS.m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08127553316', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0153-2026', '3302191602770002', 'Berdagang', 'Klahang RT 02 RW 02 kecamatan Sokaraja Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420109' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Widiyantiningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Widiyantiningsih', 'B0214420109', 'foreverrichxm@gmail.com', '$2y$10$1z7fPQQD7E5fjwwtZjGVUuYyoMfBaMAeb94YLdxXIdFmm.BmoLU/G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082227727991', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0154-2026', '3302194706800002', 'Ibu rumah tangga', 'Klahang RT 02 RW 02 kecamatan Sokaraja Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420109' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Khorizun
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Khorizun', 'A0214420111', 'izunkita@gmail.com', '$2y$10$A.U5MhL.O9YswgUnIv5aFePfp0Tddms1DRP9m60EQuUxg1RnRzsma', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082133334556', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0155-2026', '3304040104820008', 'Wirausaha', 'Babakan RT 43 RW 11 Kec. Kalimanah Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420111' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Atun Fatmawati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Atun Fatmawati', 'B0214420111', 'atunfatmawati22@gmail.com', '$2y$10$/M2wzxEhwVyb8cp2r4K35eK4L5rdhbJYSAngy/ugRGOMAQLVZlpEG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085226041166', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0156-2026', '3304046204830006', 'Ibu Rumah Tangga', 'Babakan RT 43 RW 11 Kec. Kalimanah Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420111' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Imam Fajar Martha
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Imam Fajar Martha', 'A0214420143', 'imvee3d.art@gmail.com', '$2y$10$QW//rIIt1y..augNQsImY.8iU25ofRQbvxHtF3VeEZqPpWxWe5z9K', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085726433665', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0157-2026', '3303050403860001', 'Wiraswasta', 'RT 001 RW 005, Kel. Purbalingga Lor, Kec. Purbalingga, Kab. Purbalingga, Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420143' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Vita Listiani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Vita Listiani', 'B0214420143', 'vitalist20@gmail.com', '$2y$10$26vG6Upq.Mar356yeOprNOnCYHh0LLxuzzzk/Y8jDvB1cEr.UxDg.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082337519483', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0158-2026', '3302166011860001', 'PNS', 'RT 001 RW 005, Kel. Purbalingga Lor, Kec. Purbalingga, Kab. Purbalingga, Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420143' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Adiyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Adiyanto', 'A0214430193', 'kenzie.kr5@gmail.com', '$2y$10$8mJFtwF0b/3uCzAkq1fMKuKQ7N/nyRfjC1X/KkX9QozclN8oImQDC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085219989715', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0159-2026', '3304031510870005', 'Pedagang', 'Kedungwuluh, RT 02 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430193' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Pipit Safitri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Pipit Safitri', 'B0214430193', 'kradhyasta01@gmail.com', '$2y$10$064UrMZrrnoa/athCiYeFOvvcd5NP2Jcohz.mwA2Skv5usmLMmGsW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081292788816', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0160-2026', '3304025604920003', 'Ibu Rumah Tangga', 'Kedungwuluh, RT 02 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430193' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ari Priyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ari Priyanto', 'A0214420115', 'dzakirah.nur81@gmail.com', '$2y$10$1uAs4mpBs70Yr04sf4O8leozVU/Eyj4ArjskpuZDGLZifWZnHOw26', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083160830572', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0161-2026', '3303122206810003', 'Swasta', 'Wirasana, RT 02/07 Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420115' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nurdiyati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurdiyati', 'B0214420115', 'dzakirah.nur811@gmail.com', '$2y$10$IMah3CDij2PRyKvFsHfpvOV7ySvCGLw1Oxb9IijfbqEFcHdLuCu.i', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895402116677', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0162-2026', '3303125208810006', 'Wiraswasta', 'Wirasana, RT 02/07 Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420115' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sapto Agus Setiono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sapto Agus Setiono', 'A0214420117', 'agoest.st@gmail.com', '$2y$10$pNanxoJLJE1CddEnOAFl..UCdwcGGvOP6C6cxyYpMnllbj2K20ZMy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081282874185', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0163-2026', '3215050208780006', 'Wiraswasta', 'Desa Munjul Rt 12 RW 06 Kecamatan Kutasari Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420117' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Neng Rini Komalasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Neng Rini Komalasari', 'B0214420117', 'B0214420117@kajian.griyaquran.web.id', '$2y$10$KAX2Nqe9pC09Gjx6RLcVK.nrX4J119nd4dOWCKtXXqLwlK2iMoAnS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085724149983', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0164-2026', '3215055805860005', 'Ibu Rumah Tangga', 'Desa Munjul Rt 12 RW 06 Kecamatan Kutasari Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420117' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Nurhadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurhadi', 'A0214420118', 'kembarancyty123ae@gmail.com', '$2y$10$0X5QLir1fF41cikIDBAZwOu3WsuTYIOKwWMxgJdKOjAV/S2ePa4Yy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082265051924', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0165-2026', '3303052205870004', 'Buruh', 'Limbangan rt 10 rw 05, kecamatan kutasari, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420118' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Erna yuniasih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Erna yuniasih', 'B0214420118', 'lengkongcyty123@gmail.com', '$2y$10$TJptxLoeDVHsk07bWWQz.eJ7d.bgDjfPlo0Itoh5Z9mQ3z3Piidee', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '81325358633', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0166-2026', '3303075206920003', '-', 'Limbangan rt 10 rw 05, kecamatan kutasari, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420118' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Darmawan Tri Santoso
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Darmawan Tri Santoso', 'A0214420119', 'teponksubarkah@gmail.com', '$2y$10$QRJj/oCItdRugBQMCMRkQuqHKziDTt2C8OqlEmizzTUeFDbJvJM2u', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082330030655', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0167-2026', '3302261201860006', 'Wiraswasta', 'Perum Grita Perwira Asri, blok B no. 9, Babakan Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420119' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nurani Luthfi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurani Luthfi', 'B0214420119', 'utipatonah11@gmail.com', '$2y$10$HvSRJ6jEMPcMkZMqYkRQm.Z1Ss6U8i1GJkEZEgi1c/LNFvRhQ1ETq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082233880305', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0168-2026', '3303155311870001', 'Ibu rumah tangga, freelance', 'Perum Grita Perwira Asri, blok B no. 9, Babakan Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420119' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mega Fathurrahman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mega Fathurrahman', 'A0214420120', 'baelah.mega@gmail.com', '$2y$10$hSpaoV1klp4Zetq8kOgcoOojUfKxjC78kqZNkPqZfgZ.KTL8FkdLu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085600001124', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0169-2026', '3303061903880002', 'Serabutan', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420120' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460324' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Tri Windarti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tri Windarti', 'B0214420120', 'baelah.mega1@gmail.com', '$2y$10$eaPNRSEYKPUO9hqe.CpT7u4NlfEA5YHrOOUZENvDe/PmPTVrcbvim', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085600001124', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0170-2026', '3303055911890004', 'Ibu rumah tangga', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420120' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460324' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Victor Abdullah Zulkarnain
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Victor Abdullah Zulkarnain', 'A0214420121', 'victor07581@gmail.com', '$2y$10$VduW8ySO2t2lkS57Ht7M9etyQeG.rOYhlvtZU0XWsr1poII9XPwX2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081319999119', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0171-2026', '3303050208880001', 'Swasta', 'Jl Wiraguna no 16 purbalingga kidul, purbalingga.', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420121' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Laely Dwi Anjani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Laely Dwi Anjani', 'B0214420121', 'anjazulkarnain@gmail.com', '$2y$10$PhR7Jh37YKUP2Mjmb832ROEwgE59xxCZkT0y7uf0K/bKAVlY2DeRO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082234343408', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0172-2026', '3303056203890001', 'Ibu rumah tangga', 'Jl Wiraguna no 16 purbalingga kidul, purbalingga.', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420121' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Adi Priyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Adi Priyanto', 'A0214420124', 'adipriyanto0290@gmail.com', '$2y$10$oe02isxtLogqL.ZxE2HTy.q/z27wu9Y5hHzplpbbA9/w/o2HT23Pe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895411208000', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0173-2026', '3302160202900005', 'Wirausaha', 'Selabaya/ Ruko selabaya indah jl geriliya barat', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420124' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Marlinah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Marlinah', 'B0214420124', 'sednasaras87@gmail.com', '$2y$10$nbnxOMQXSzmG8xDr.ioefOgQra73ulYLQenvhnYBRwSPVpWzmhsp2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085600477400', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0174-2026', '3301226606870002', 'Ibu Rumah Tangga', 'Selabaya/ Ruko selabaya indah jl geriliya barat', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420124' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Purwanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Purwanto', 'A0214420125', 'ipurziidanp@gmail.com', '$2y$10$yBRbhKtd6ywb0.TvOOjrouevHbtfVylwWY3PaXz2LxZiwiGZBxl0.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085826000320', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0175-2026', '3303051503890001', 'Berdagang', 'Karang jambe rt 02/rw 03.kec.padamara.kab.purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420125' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sukesih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sukesih', 'B0214420125', 'B0214420125@kajian.griyaquran.web.id', '$2y$10$iurHKegw6O75SwuqSrzLp.aQ.4VWNCW8.jaawNFUAQ/.G8wWp6lkm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082178255578', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0176-2026', '3303154407890001', 'Mengajar di rumah qur''an', 'Karang jambe rt 02/rw 03.kec.padamara.kab.purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420125' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Priyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Priyono', 'A0214410082', 'priyono111281@gmail.com', '$2y$10$sWEjtJU2n9VWQHmtSBrmYe5XbIGfdH9WoZ7pIJUQZ2KGiXwHU7PQi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082115320975', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0177-2026', '3303061112810004', 'PNS', 'Klapasawit RT 01 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410082' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ristiana
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ristiana', 'B0214410082', 'maspripbg99@gmail.com', '$2y$10$dcQ92EoFU7WkELqeEnSb3OJHnPiqvaP/eoSpIxs0h2x7WkYAy7XHK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087817902886', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0178-2026', '3303066212850005', 'Ibu Rumah Tangga', 'Klapasawit RT 01 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410082' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Untung subarman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Untung subarman', 'A0214410084', 'untungsubarman3@gmail.com', '$2y$10$qUtkULRP8sk5l/cB7nGzF.hmMKvCqWKJ/p/ZKrMWdU0mR.m1mgXbe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08989087707', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0179-2026', '3303072705790001', 'Buruh bangunan', 'Karanglewas RT 11 RW 05, kecamatan Kutasari,kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410084' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ida mursyidah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ida mursyidah', 'B0214410084', 'ummuabdurrozaq61@gmail.com', '$2y$10$Iy6AptMr.U.a0BXFzGJHweHTPM/fNVxnnhSn/si6jo5bl9IwU7ZJi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085864179538', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0180-2026', '3303076406880001', 'Guru TK suwasta', 'Karanglewas RT 11 RW 05, kecamatan Kutasari,kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410084' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Andin Anggoro
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Andin Anggoro', 'A0214410078', 'aanggoro87@gmail.com', '$2y$10$EAotau5CVYCub3RV0fwHFu2L4sSrjgZcBWh4xsFV4kcIGsHsv78Ja', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081232676414', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0181-2026', '3303051301870001', 'Pegawai Negeri Sipil', 'Perum. Berlian Karangsentul Blok F No. 3, RT 02 RW 05, Desa Bojanegara, Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410078' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ina Asmi Rachmani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ina Asmi Rachmani', 'B0214410078', 'asma.arrahmah@gmail.com', '$2y$10$1QOPEIjlOy7dHrfUs6w7SOxDEp4DoJBZAvcE9v.KEIp9Iprl4y1qm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081252033559', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0182-2026', '3303054705870001', 'Mengurus Rumah Tangga', 'Perum. Berlian Karangsentul Blok F No. 3, RT 02 RW 05, Desa Bojanegara, Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410078' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Abdul Rachman Muslim
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Abdul Rachman Muslim', 'A0214410086', 'dreamcorner@gmail.com', '$2y$10$uEoNSXe5CTfIvCh6QF66y.1Ik019YpjMwMu3FgghOHloqSVNqDPQC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081542880795', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0183-2026', '3302203010760001', 'IT', 'Perum Abdi Negara, Kresna IV No 2. Bojaneraga, Padamara - Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410086' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Maria Fita Marwati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Maria Fita Marwati', 'B0214410086', 'vitalogiez@gmail.com', '$2y$10$D/hOXdZTks4fkhodnkbdbu6KkzC.PMRi.a9Gy5W1EQsLo8JbkOcyW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08112623325', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0184-2026', '3302206604830008', 'IRT', 'Perum Abdi Negara, Kresna IV No 2. Bojaneraga, Padamara - Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410086' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aulia riska nur khusna
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aulia riska nur khusna', 'A0214410081', 'desynvnv@gmail.com', '$2y$10$XX0jxUlnoKnCDs3n4yxNWO18lLHpQZZ/C50mBVyUv5IKJooeKKPYq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089619296997', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0185-2026', '3303051303870001', 'Pedagang', 'Galuh RT 08 RW 04, Bojongsari, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410081' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Desi nur fiana
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Desi nur fiana', 'B0214410081', 'desynvnv1@gmail.com', '$2y$10$FRZEy59QHYDxAAnQgMdPxO/T1.35eQOF70iZN7z6xo9idGkpGswAC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895348310937', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0186-2026', '3303145012920001', 'IRT', 'Galuh RT 08 RW 04, Bojongsari, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410081' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Januar imam prabawanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Januar imam prabawanto', 'A0214410051', 'januarip_1974@yahoo.co.id', '$2y$10$1p14WQLmr6xmP0yDUrpb8uevyTEJwN3QusmzY/6nYQfQj/9f9ycqW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08990628106', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0187-2026', '3303140201740002', 'Tidak bekerja', 'Perum trajumalang blok C1, Kandang gampang RT 04/02, kec. Purbalingga, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410051' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Werdha adityasari putri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Werdha adityasari putri', 'B0214410051', 'januarip_19741@yahoo.co.id', '$2y$10$3LzntBrEYt5FC9J/Tw49yeqlzCf6bd9/NcbWBcCC9ygPoARBeWEfm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08990628106', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0188-2026', '3303145610790001', 'Ibu rumah tangga', 'Perum trajumalang blok C1, Kandang gampang RT 04/02, kec. Purbalingga, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410051' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Yuli Efendi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Yuli Efendi', 'A0214410085', 'yulie1946@gmail.com', '$2y$10$zmiYKpX5JNLlxTklQP6b9e1jNAKZqH/7BtVwCOD65uHURkO936O4O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227223691', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0189-2026', '3303062812810005', 'Wiraswasta', 'Selabaya RT 02 RW 04 kecamatan Kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410085' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460316' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti Asiyah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti Asiyah', 'B0214410085', 'yulie19461@gmail.com', '$2y$10$lH3loiVrrayrS2iPnjCprOpYP6bvSwTfItXVnB40fIYivUgHyMkSG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895383067871', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0190-2026', '3303065305830002', 'IBU rumah tangga', 'Selabaya RT 02 RW 04 kecamatan Kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410085' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460316' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Heri Susanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Heri Susanto', 'A0214410088', 'hsusanto1912@gmail.com', '$2y$10$imtW5zh2qTD4TW/Ah6YQiOIx5QGPXC5CPAWEFTJCbYLE6O9El5HRK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081326991225', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0191-2026', '3301122002850002', 'Wirausaha', 'Sokawera RT 02 RW 04, Kec. Padamara Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410088' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Regi Willi Sartika
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Regi Willi Sartika', 'B0214410088', 'regiwsartika398@gmail.com', '$2y$10$kAtwF.wtImOC4wN5.aeNzuW4vK9/42Q7I/mwW6fwjp5uGGan0.OHi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082224420283', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0192-2026', '3303154305910001', 'Wiraswasta', 'Sokawera RT 02 RW 04, Kec. Padamara Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410088' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Agus Sutomo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Agus Sutomo', 'A0214450243', 'septianaratnawati@gmail.com', '$2y$10$3GPvMb.czaLlXxPpMwLfw.F9C7sEN3RHBuiNPl9waVm9NfOys6Yhq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081327017546', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0193-2026', '3304083108670001', 'Swasta', 'Kavling Tunas Ilmu Depok Kedungwuluh RT 3 RW 3 Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450243' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Septiana Ratnawati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Septiana Ratnawati', 'B0214450243', 'septianaratnawati1@gmail.com', '$2y$10$PG4CvT76qusp6AdK9vkgq.L9Nco3AClGsRg2EyRxaKQ2JXjfZUUle', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08988171169', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0194-2026', '3304124909890003', 'Ibu Rumah Tangga', 'Kavling Tunas Ilmu Depok Kedungwuluh RT 3 RW 3 Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450243' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Nurul Abdulloh
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurul Abdulloh', 'A0214410091', 'A0214410091@kajian.griyaquran.web.id', '$2y$10$LXGgyE7P9SWy/pMMyIUEaOvCavdMZ6lknGYeZRq89VJWb2.1mrQJS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088226880280', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0195-2026', '3303141208820001', 'Pedagang', 'Pekalongan,RT02 RW04 kecamatan Bojongsari, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410091' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: SURTIWI
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('SURTIWI', 'B0214410091', 'abdullahnurul718@gmail.com', '$2y$10$k24lI0Z.XJ3VT6ckXp5KoeYoA4s6ufteQJur0a5QKlT.YajUVWgJy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088216581589', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0196-2026', '3303146704860001', 'Ibu rumah tangga', 'Pekalongan,RT02 RW04 kecamatan Bojongsari, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410091' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Dadiyono Pulung Nugroho
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dadiyono Pulung Nugroho', 'A0214440198', 'd_poeloeng@yahoo.com', '$2y$10$fhExOfCz/WEOzpAnrYDXw.a4f1482xrfOaMBZysLHOYQ5omIvOfhC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081586809401', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0197-2026', '3276060904800006', 'Karyawan Swasta', 'Graha Permata Selabaya No G14 Selabaya Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440198' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Amelia Meliawati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Amelia Meliawati', 'B0214440198', 'd_poeloeng1@yahoo.com', '$2y$10$UYzo0On7e6BfwhpfCZAaQefKtzL7ANF62ZotAeJR6gu7P4xSXbnOe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081586809401', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0198-2026', '327606181110004', 'Ibu Rumah Tangga', 'Graha Permata Selabaya No G14 Selabaya Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440198' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Imam Choerun
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Imam Choerun', 'A0214440199', 'kdmenjangan2019@gmail.com', '$2y$10$VS.IoYb55kG.0XnYKnWGYOD7sCYVsUuJcTW5PEbZ.pm0bP.VuJEyG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227747444', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0199-2026', '3303052212820002', 'PNS', 'Kedungmenjangan RT 01 RW 03, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440199' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Santu Mikael Kanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Santu Mikael Kanti', 'B0214440199', 'B0214440199@kajian.griyaquran.web.id', '$2y$10$iHWy0iXy5UjqNAWpLG2aoemDzfEiA68AjCwjWkTLOHAsdd0u4qu.u', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), NULL, 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0200-2026', '3303054404850004', 'Mengurus Rumah Tangga', 'Kedungmenjangan RT 01 RW 03, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440199' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: TM Nurdin
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('TM Nurdin', 'A0214440192', 'abuyazidnurdin@gmail.com', '$2y$10$qG0eku0zWxBbRFc34RV/5OHHEB8RcT7J/53RaadBLSlBgpKOxXEwe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081347106451', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0201-2026', '3302102206820001', 'Mengajar', 'Ponpes Tahfiz An-Naba, Jl. Karangjati, Desa Suro Rt06/Rw04, Kec. Kalibagor, Kab.Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440192' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Fatihdaya Khoirani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Fatihdaya Khoirani', 'B0214440192', 'ummuyazidf2@gmail.com', '$2y$10$XyY0Q1DcxACLdU7022KhR.rwf3gCqsdjHd94I/zbdNJ2/lLbP8jHS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085700578185', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0202-2026', '332106501860001', 'Ibu Rumah Tangga sambil mengajar privat', 'Ponpes Tahfiz An-Naba, Jl. Karangjati, Desa Suro Rt06/Rw04, Kec. Kalibagor, Kab.Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440192' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Jamal
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Jamal', 'A0214410079', 'A0214410079@kajian.griyaquran.web.id', '$2y$10$ie2T2w2fdj0cO/7JIFgzbuwVMYGTj8XtKneuzsxS0d1ksMxks0M9G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085799401588', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0203-2026', '3303050406650003', 'Wiraswasta', 'Karangkabur rt 01/ rw 02, kel, bojanegara, kec. Padamara, kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410079' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Khadijah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Khadijah', 'B0214410079', 'muchafood.id@gmail.com', '$2y$10$z6IDy0t24i7r7UD9EhG6B.dxG3QFIG9k8zIjfQ.DVMl0a2iqgOp9a', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895322008589', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0204-2026', '3303054501740002', 'IRT', 'Karangkabur rt 01/ rw 02, kel, bojanegara, kec. Padamara, kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410079' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: yuni setiyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('yuni setiyono', 'A0214460310', 'afaninpbg@gmail.com', '$2y$10$eZILoJOCmv9ss0bqZ4.dHO8uIrbUz0wGo0/N.yvYgClaRXHlnN0FW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08895465799', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0205-2026', '3303071306870002', 'berdagang', 'karangreja RT012 RW006,kecamatan kutasari,kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460310' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rokhmah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rokhmah', 'B0214460310', 'yudhantadavanpramana@gmail.com', '$2y$10$1ykfLwrGCM3aKzGueoRlh.768DyDcMYowILZo4tkZdmSXkFLvWslu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089508956347', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0206-2026', '3303074307860005', 'pekerja rumah tangga', 'karangreja RT012 RW006,kecamatan kutasari,kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460310' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aly subarkah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aly subarkah', 'A0214440235', 'alysubarkah63@gmail.com', '$2y$10$dMSx4/wQoOEs77ke8K0/Huo4LiCJqcOEeDrKdsT1jMgxb4cY3Vxhq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085641654562', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0207-2026', '3303010311930002', 'Berjualan online', 'Domisili:Perum puri kalimanah blokA17', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440235' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460311' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Susy haryanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Susy haryanti', 'B0214440235', 'susyharyanti701@gmail.com', '$2y$10$wzh0n6V4x396LxlSypTA3.k7m7WLj5.pI6wbcAc3Rl7ERRVPVZHJa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0865875986239', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0208-2026', '3303156710930001', 'Ibu rumah tangga', 'Domisili:Perum puri kalimanah blokA17', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440235' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460311' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Firma Nur Aditya
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Firma Nur Aditya', 'A0214460313', 'firmanuradityaaditya043@gmail.com', '$2y$10$RMIkmStmiH4PFdfbGZHsj.k6i8LalV3sGIkmW4RfmZOMZy/W4U2Dy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895378176311', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0209-2026', '3303041005930002', 'Pedagang', 'Penaruban RT 2 RW 2,KALIGONDANG, PURBALINGGA', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460313' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Trian purwanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Trian purwanti', 'B0214460313', 'trianpurwanti49@gmail.com', '$2y$10$7icEkkzRsZ11SECbk0MfuuFl2unntoQVYI3wQd1rwkQRmOMFo6hvO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085229406607', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0210-2026', '3303026607910005', 'Dagang', 'Penaruban RT 2 RW 2,KALIGONDANG, PURBALINGGA', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460313' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Tofan Anggana Adi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tofan Anggana Adi', 'A0214460314', 'angganatopan123@gmail.com', '$2y$10$n95nAY9qeSCNILWX1IR3SORjEWU0kRy8jeZ8/EWqqE/VVif04dByu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081215050123', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0211-2026', '330306180590001', 'Pedagang', 'Jalan Jambu No. 5 Desa Kalimanah Wetan RT 04 RW 07 Kecamatan Kalimanah Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460314' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Intan Hasti Utami
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Intan Hasti Utami', 'B0214460314', 'hasti.intan123@gmail.com', '$2y$10$3hV6SNqrE0MBRaNs30CF7eSh7mHEvvNLlt6Fp.wRvNKL0ILxLZuxq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895327373771', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0212-2026', '3302186107930001', 'Ibu Rumah Tangga', 'Jalan Jambu No. 5 Desa Kalimanah Wetan RT 04 RW 07 Kecamatan Kalimanah Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460314' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mulki Indana Zulfa
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mulki Indana Zulfa', 'A0214430194', 'admiportfolio@gmail.com', '$2y$10$t19bwBL/kX/SKAXrCE1z8OKsVWQ3ufgO0SeG5I0TLFDOYp/DnYHSy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895341295031', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0213-2026', '3209180812860009', 'ASN', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430194' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430179' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460315' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti Mutmainah Anwar
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti Mutmainah Anwar', 'B0214430194', 'bundaiza2023@gmail.com', '$2y$10$4UKHje3fW0/Y0AmZtGUr1e0Zl48Dak3Jjw9H1PZK63aK6dESjnTsy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085659670038', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0214-2026', '3274034811860007', 'IRT', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430194' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430179' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460315' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Andi Kurniawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Andi Kurniawan', 'A0214460317', 'andimaharani2003@gmail.com', '$2y$10$m6.6MEcx.8yPrY3gvEJ8xOYisMdU4htPOEfsZr1KdiYQwexAIp18O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085727417779', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0215-2026', '3303142907920002', 'Wiraswasta', 'Desa Gembong, RT 03 RW 02, Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460317' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Mei Maharani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mei Maharani', 'B0214460317', 'me.mheiy@gmail.com', '$2y$10$EvEJ68n1zaL.LvmrqAhcQ.0s9G6S7caCmclu0alT4im6IUeo0l/ny', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085747745774', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0216-2026', '3303146405920002', 'Guru SD', 'Desa Gembong, RT 03 RW 02, Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460317' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Arif Hidayatullah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Arif Hidayatullah', 'A0214460319', 'abuumamamh@gmail.com', '$2y$10$ePnTTqaVSPOXdqwpk3q0ve.exe76PgH6M.uES/0PfAmtBNUlgEQiK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082135366338', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0217-2026', '3302112404810005', 'Pengajar', 'RT 1 RW 4 desa kalisube,kec Banyumas,kab,Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460319' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Durrotun Nasikah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Durrotun Nasikah', 'B0214460319', 'hobimasak@gmail.com', '$2y$10$BMmZZ79wb7/GKeA5u7sir.XUxXsR6.8xEGJaC8QwtO67OrAHLz/9q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081392179935', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0218-2026', '3302116412840002', 'Tidak bakerja', 'RT 1 RW 4 desa kalisube,kec Banyumas,kab,Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460319' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Dwi Nurhidayat
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dwi Nurhidayat', 'A0214460321', 'hidayat.microlux@gmail.com', '$2y$10$W4xmRzUxnWexrNnfOnWjU.rlDIbERd/YvOpacm6RQWe3fQmVY2EDu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089690782228', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0219-2026', '3303051702950001', 'Pedagang', 'Kedungmenjangan Rt 04  Rw02 Kec. Purbalingga Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460321' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ruli Dwi Saputri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ruli Dwi Saputri', 'B0214460321', 'rulidwi32@gmail.com', '$2y$10$am730Fmr7wt3OvdoyLt06Ow9qrebHHmq2K3Vlydkp2VPyV9.B8hlO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895385067106', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0220-2026', '3303056504970001', 'ibu rumah tangga', 'Kedungmenjangan Rt 04  Rw02 Kec. Purbalingga Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460321' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Heri setyadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Heri setyadi', 'A0214460323', 'herisetyadi008@gmail.com', '$2y$10$sLIChVRVC9epdhvaGzgtA.M7k5b/SP2n.v5fQNUvC9Vm3P9nKRrmG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088238126676', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0221-2026', '3303060104880001', 'Tetap bekerja', 'Babakan rt10/02,Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460323' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nur Adibatul Wajihah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nur Adibatul Wajihah', 'B0214460323', 'herisetyadi0081@gmail.com', '$2y$10$dpqWM9zGFZcErIqNBSvn/.edHsowySwyOTM.Xa8AzvGWjb9xnqSwi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '85225279570', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0222-2026', '3327095809970012', 'Mengajar di Kelompok tahfidz', 'Babakan rt10/02,Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460323' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Agus Priyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Agus Priyanto', 'A0214440239', 'priyanto.agus4022@gmail', '$2y$10$JV1Nnr5z4EI0cQwn4stVTujwKEdIexIdl76eGDk3/oOnzkqTRn69.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081323576252', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0223-2026', '3303092208820006', 'Pengajar', 'Perum Puri Babakan lama, no 76, RT 32 RW 08, Kecamatan Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440239' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460325' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Irianti Purwaningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Irianti Purwaningsih', 'B0214440239', 'B0214440239@kajian.griyaquran.web.id', '$2y$10$fVUW5dpq0.9wsTOTcq/KDeYA5d0.SLm4txr1eztx4GyFYqF7W7VsS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895421935328', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0224-2026', '3303056803850002', 'Ibu rumah tangga', 'Perum Puri Babakan lama, no 76, RT 32 RW 08, Kecamatan Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440239' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460325' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Izandi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Izandi', 'A0214460326', 'zndbr91@gmail.com', '$2y$10$uuBvIvZalYLjLirZDHw.p.SHMVC.6Eaj8N49lSHNwEps/5xGOOlNa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085774007400', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0225-2026', '330406261190004', 'Buruh', 'Jl. Baturraden Timur. Gang Sadewa RT4 RW2 No. 7. Sumbang, Banyumas.', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460326' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sus Sabarniati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sus Sabarniati', 'B0214460326', 'B0214460326@kajian.griyaquran.web.id', '$2y$10$DFiDIhT3vFQWwsts6AQB..8xmRrtK4iEVke/X2gGVOrSuhlCLgJ0.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085156008902', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0226-2026', '3304124608940002', 'Ibu Rumah Tangga', 'Jl. Baturraden Timur. Gang Sadewa RT4 RW2 No. 7. Sumbang, Banyumas.', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460326' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Budi Haryanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Budi Haryanto', 'A0214460327', 'zaenabyanto@gmail.com', '$2y$10$uPyuydlTsT0Xxg0vymRvaObNmzwrVIZrfLsPNR/jomwyglyd1LjeG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085385287879', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0227-2026', '3303051301770003', 'Pedagang', 'Kandang Gampang RT 03 RW 05, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460327' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Setianingsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Setianingsih', 'B0214460327', 'setianing01.s@gmail.com', '$2y$10$D8lweij6WFMPwLjyiwwIm.ZkHcSDRn8zDvu.r/Rsm2aVv071.urdy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089675722749', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0228-2026', '3303055510850003', 'Pengajar TAUD & Ibu Rumah Tangga', 'Kandang Gampang RT 03 RW 05, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460327' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Jamhari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Jamhari', 'A0214450267', 'A0214450267@kajian.griyaquran.web.id', '$2y$10$rPhizxlGdHhV2iJvY4MGrudvG045Jsx8Zzf317x4181DBQ2U6ibCS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0229-2026', '3303010208730002', 'Buruh lepas', 'Karang tengah rt 05/rw03, kemangkon, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450267' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Indah wigati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Indah wigati', 'B0214450267', 'indahummuyusuf75@gmail.com', '$2y$10$LBeCJHjPzHAFL7xtrwfPlOIxnqgbSE0LKA4PRtPLum.U.4rMSInBC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089658088499', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0230-2026', '3303016402750001', 'Ibu rumah tangga  dan penjahit rumahan', 'Karang tengah rt 05/rw03, kemangkon, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450267' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ndaru Satrio
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ndaru Satrio', 'A0214450268', 'satrio.ndaru9@gmail.com', '$2y$10$HTEfnDZzZnwibeys8upZYuo4mGlQw0h7LO5pZvXCpzmM5IqDk4pia', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082281075295', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0231-2026', '3302210509870001', 'Dosen', 'Kedungwuluh RT 05 RW 02, Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450268' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nur Apriani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nur Apriani', 'B0214450268', 'ia.apriani@gmail.com', '$2y$10$11KxT5ijYjCp5Y8z3VXepejs2TKI1KhboCKOJB1zg1ofskhm6C9RW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08159230700', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0232-2026', '3171046104890004', 'IRT', 'Kedungwuluh RT 05 RW 02, Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450268' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Doni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Doni', 'A0214450270', 'kdoni7951@gmail.com', '$2y$10$oCtKLiGAI5hbBNt/dEZaOeBZd.fad1hqQj2f.QHjBd.JYgyaVQY6q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895349865488', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0233-2026', '3303062012880003', 'Dagang', 'Klapasawit RT 2 RW 5, Kalimanah, purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450270' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nurhayati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurhayati', 'B0214450270', 'nurhayati127898@gmail.com', '$2y$10$7YO2AepaiBrU7pNem.p9FeV.MU1H7ugYhRLeKqAWlNJ.IRbjDOW9K', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08971860090', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0234-2026', '3327086901880081', 'Ibu rumah tangga', 'Klapasawit RT 2 RW 5, Kalimanah, purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450270' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Purnawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Purnawan', 'A0214450271', 'A0214450271@kajian.griyaquran.web.id', '$2y$10$2LyGobuzBYJFjRQ7JmPVpeUx.9AQ01hGzwwytDEo9q4E7E6Ueijtu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0235-2026', '3303062007810003', 'Buruh', 'Klapasawit RT 02 RW 01', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450271' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ita Susanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ita Susanti', 'B0214450271', 'susantiita0204@gmail.com', '$2y$10$HHHFGnjdFKn0USXNGTJBzuc3XwrZPHeB8ub0pAofQ7QsPnk3lgk12', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085640961141', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0236-2026', '3303064204840002', 'Ibu Rumah Tangga', 'Klapasawit RT 02 RW 01', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450271' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Subhan kurniawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Subhan kurniawan', 'A0214450272', 'subhankurniawan825@gmail.com', '$2y$10$tuGxRpcPf9lNIggC2GXc6uzDbPstUYfM02w.oxTQ6zJl0irigHdL6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081328746391', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0237-2026', '333051902780005', 'wiraswasta', 'Pagedangan RT 03 RW 06   Kecamatan Purbalingga kidul Kabupaten  Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450272' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: sari rohyati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('sari rohyati', 'B0214450272', 'sarirohyati83@gmail.com', '$2y$10$U3GJNAom3CBTfRbBvhMB1.OIoDZgq8KJ5O2u.VTmictomn8.A5Wwe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089678781158', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0238-2026', '3303056804840008', 'ibu rumah tangga', 'Pagedangan RT 03 RW 06   Kecamatan Purbalingga kidul Kabupaten  Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450272' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aris Santosa
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aris Santosa', 'A0214450273', 'A0214450273@kajian.griyaquran.web.id', '$2y$10$.K99V9dCpVpCcfSayXrQNupwoGjG8VX8g75UIofa46wtxDMrWdse.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227852122', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0239-2026', '3303150907790001', 'Wiraswasta', 'Kedungwuluh RT 04 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450273' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Mei Purwati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mei Purwati', 'B0214450273', 'meypurwatipbg@gmail.com', '$2y$10$M7cLIwwp0hqvHUKiE9c8o.lofXJL5M1VA51tbpOhBvoX/t144DaIq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085291822449', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0240-2026', '3303156105850001', 'Ibu Rumah Tangga dan guru', 'Kedungwuluh RT 04 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450273' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Romi wijaya
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Romi wijaya', 'A0214450274', 'romiwijaya070391@gmail.com', '$2y$10$jvYPsp1eJlxm2hqijAJb1ep8gbvmWH5nBoBWJAxjEW/OCPMXoapKe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082138030401', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0241-2026', '3301010703910002', 'Wiraswasta', 'Karangklesem RT 03 RW 02 kec. Kutasari kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450274' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Elsa Nurida
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Elsa Nurida', 'B0214450274', 'elshanurida@gmail.com', '$2y$10$CdWRjN6F6OTEbsBOeCmkLubfgKSZnbIf0jgnhFujtTjQ2DYBYv9/q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082221044498', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0242-2026', '3303074406910001', 'Ibu rumah tangga', 'Karangklesem RT 03 RW 02 kec. Kutasari kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450274' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Margo Priastowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Margo Priastowo', 'A0214450288', 'priastowomargo@gmail.com', '$2y$10$XCBezam8xjzsSs0C1I7XXexwAFyFSazF0lgx0lzBCm8byC.3IIZny', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081326309503', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0243-2026', '3302192903790006', 'Serabutan', 'Pengadegan rt08/rw04, Kec.Pengadegan, Kab.Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450288' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sita Arimbie
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sita Arimbie', 'B0214450288', 'maulidyarz14@gmail.com', '$2y$10$CdSbDpK1XmnFbEh7WEc/kuqjZjrnWFTe78KA/GXavaI12NEAirISC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081215872049', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0244-2026', '3302194211850003', 'Ibu Rumah Tangga', 'Pengadegan rt08/rw04, Kec.Pengadegan, Kab.Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450288' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rizal Fauzk
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rizal Fauzk', 'A0214450275', 'fauzirizal059@gmail.com', '$2y$10$HJqF8fCsGh9vLOQCpMbY..uvEGInhpJtMAm7ajKFidGbIEoivdNO.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085173165491', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0245-2026', '3303051205870002', 'PNS Guru', 'Rt 08 Rw 04. Des karangsari Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450275' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Wiwied Dewintari Haryani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Wiwied Dewintari Haryani', 'B0214450275', 'junoque@gmail.com', '$2y$10$pQ/RAgMHo9COtnRw2IZ8nuBpDJmSw.VX4U4aLwpzunaWajaF4LOGi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085159030158', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0246-2026', '3303064205880002', 'IRT', 'Rt 08 Rw 04. Des karangsari Kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450275' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Trimo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Trimo', 'A0214450276', 'A0214450276@kajian.griyaquran.web.id', '$2y$10$qgWId8SftnFBpqz7PwllLulVAGaC1CvhJhfA64JxpQ8lYg38ZPieW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088212961253', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0247-2026', '3303062301720002', 'Buruh Bangunan', 'Kedungwuluh RT 1 RW 2, kecamatan Kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450276' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Suciana
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Suciana', 'B0214450276', 'B0214450276@kajian.griyaquran.web.id', '$2y$10$hh08cj7ON3zKKwN2Gw2YB.8hzNFmTJG/AXHJs4.uhB2vD9vyCGWe.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088212961253', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0248-2026', '3303064303910001', 'Ibu rumah tangga', 'Kedungwuluh RT 1 RW 2, kecamatan Kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450276' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Afrizal
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Afrizal', 'A0214450277', 'afrizalstudio@gmail.com', '$2y$10$A.FmVMPsesXpJLn7EaeWye0uQgTl4ow7GFLOV8Q/X5nTCD/IMMJNm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08562324224', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0249-2026', '3175071908840014', 'Pedagang Bensin ECER & ikan hias', 'Pengalusan, RT 05 RW 01, kecamatan Mrebet, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450277' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Devi Nopiyanty S. Kep
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Devi Nopiyanty S. Kep', 'B0214450277', 'devinopiyanty@gmail.com', '$2y$10$hgeS1/LRksVwf7uncJK9n.OtauttsEEVS28cR8R2QduGT12ebdCji', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08562165550', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0250-2026', '3214014711910005', 'Ibu rumah tangga', 'Pengalusan, RT 05 RW 01, kecamatan Mrebet, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450277' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Tugiono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tugiono', 'A0214450278', 'alifnurrizky.akbar00@gmail.com', '$2y$10$HR/ZHbmnz7T5SgLMIlQnfunRapuVpIhWkKZnx/7EIyAu0mqs.rT0i', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083114284766', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0251-2026', '3303063006810001', 'Buruh', 'Kedungwuluh RT 02 RW 04 Kecamatan Kalimanah Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450278' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Suratni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Suratni', 'B0214450278', 'alifnurrizky.akbar001@gmail.com', '$2y$10$pdZmBEUcKcpiuUL2lOpRC.H2un/A1srZC82h/qRdv2ygeEtY.uB4q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '083821973186', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0252-2026', '3303066509850002', 'Ibu Rumah tangga', 'Kedungwuluh RT 02 RW 04 Kecamatan Kalimanah Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450278' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Muhamad Riana Yudianto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Muhamad Riana Yudianto', 'A0214450279', 'mrianayudianto1@gmail.com', '$2y$10$ouFU7uUj.sjX.4kd8RkjQObWOofqrAtEzisyZRTQpF7tZ/otDhoXC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087822994032', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0253-2026', '3204051902930008', 'Guru', 'Kavling Tunas Ilmu Blok B1 No.1 RT 04 RW 04  Desa Kedungwuluh, Kec Kalimanah, Kab Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450279' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Gletika Halmaherani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Gletika Halmaherani', 'B0214450279', 'g.ummumaryam1@gmail.com', '$2y$10$qTF45T8L/Z33zvxJh.GuN.V9v0u9c2e.GTuOdPC4VSArRfk1WvYk6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087722875749', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0254-2026', '3204325209890007', 'Ibu Rumah Tangga', 'Kavling Tunas Ilmu Blok B1 No.1 RT 04 RW 04  Desa Kedungwuluh, Kec Kalimanah, Kab Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450279' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rochiman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rochiman', 'A0214450280', 'rochiman@gmail.com', '$2y$10$5k6dqmmw7foxcLfNdS.XHe3JFABewkv.D5JH8DSN8Zj.n7LZ0df/e', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081215444410', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0255-2026', '3302200810880004', 'Wiraswasta', 'Banteran RT 4 RW 5 kec sumbang kab banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450280' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rusmiati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rusmiati', 'B0214450280', 'miateguhjaya@gmail.com', '$2y$10$pmfBleGoDKUq2ghHDmF0kub1DrxYbvYqlutvVSMU1s2qmwacBz5wy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085719811233', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0256-2026', '3302275206950002', 'Ibu rumah tangga', 'Banteran RT 4 RW 5 kec sumbang kab banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450280' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rouf Aizat
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rouf Aizat', 'A0214460331', 'rouf.aizat@gmail.com', '$2y$10$/DaB2OxjAeJs4J2Wb05mAuctihMMf8toVBizs7KvGjqxijc1KKqAO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082137645625', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0257-2026', '330306040190001', 'Pegawai BUMN', 'Jl. Jati, Rt. 1/ Rw. 3, Desa Kalimanah Wetan, Kec. Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460331' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Amalia Utari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Amalia Utari', 'B0214460331', 'amaliaut76@gmail.com', '$2y$10$CoqWzg9ekGmYbjkG2FDQ2e/mhuq2qwSxh11k34BhyCjHP9tapZvAy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081280946409', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0258-2026', '3216025002940016', 'IRT', 'Jl. Jati, Rt. 1/ Rw. 3, Desa Kalimanah Wetan, Kec. Kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460331' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Taufik Dermansyah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Taufik Dermansyah', 'A0214450281', 'taufikdermansyah@gmail.com', '$2y$10$44hlaeNDulypd9w0yXPZ2ueLfCk3b4ysSjodHXKmbfUSUcc9mX73G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081327097271', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0259-2026', '3303141511810001', 'Karyawan swasta', 'Kelurahan bojong Rt03Rw01 Kec. Purbalingga Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450281' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Retno Sumanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Retno Sumanti', 'B0214450281', 'retnosumanti84@gmail.com', '$2y$10$Eqwrd3veCAkdXX20boyhheFjufOZYxPtLWZSvXaG6WwQ5m0rRLsHq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082136291172', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0260-2026', '3303144906830003', 'Rumah Tangga', 'Kelurahan bojong Rt03Rw01 Kec. Purbalingga Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450281' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sarkono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sarkono', 'A0214450282', 'pakjenggot804@gmail.com', '$2y$10$l5P.qdkk78J6Y7Q./ve/d.3Aqyxyh0cagpG76Gy8Cpd/24j5H9nDC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085738067587', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0261-2026', '3303061905750003', 'Pedagang', 'Klapasawit RT 02 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450282' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nurul Fatichach
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurul Fatichach', 'B0214450282', 'nurulpbg75@gmail.com', '$2y$10$tI22n6KWaGAPVcL2HXkM0OykxmggW62.EM3zzRrmerT0nVGfW.ix6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082325842090', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0262-2026', '3303066305800002', 'Ibu rumah tangga', 'Klapasawit RT 02 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450282' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eron Ferlando
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eron Ferlando', 'A0214450283', 'eferlandiron@gmail.com', '$2y$10$NcmuRx1QjN0pxBFtLsukDOwdXg5qfPVXvehrE9rGt64DafWudkgja', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082153413644', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0263-2026', '3303062303840002', 'Wiraswasta', 'Jalan Mangga Timur A1 perumahan selabaya indah desa selabaya kecamatan kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450283' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Hanum Ayuningtyas
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Hanum Ayuningtyas', 'B0214450283', 'ayhanum90@gmail.com', '$2y$10$3avoLiONBUq5gu43ySGWWOQLn4WgwVglvpSHhLHNIjvV2gVptZMR2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081225657143', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0264-2026', '3374054805900004', 'Wiraswasta', 'Jalan Mangga Timur A1 perumahan selabaya indah desa selabaya kecamatan kalimanah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450283' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Kholidun
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Kholidun', 'A0214450284', 'A0214450284@kajian.griyaquran.web.id', '$2y$10$sYqL1FTMHMg5hxRCsLVUsOCxwZqqAYqQYlZdpUH2/tdJ0JaX/K8FO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081215425200', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0265-2026', '3201010106740018', 'Karyawan pondok (TI mart)', 'Kedungwuluh,RT 08 RW 01,kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450284' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Eti Susanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eti Susanti', 'B0214450284', 'B0214450284@kajian.griyaquran.web.id', '$2y$10$RiyZeyW1789DE5U83kg4nuKvjIdv3qhZc2WbXmGQNv743/IWNZbSm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085226318044', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0266-2026', '3204174304800021', 'Guru TK', 'Kedungwuluh,RT 08 RW 01,kalimanah kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214450284' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Marzuki Fajar
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Marzuki Fajar', 'A0214440222', 'marzukifajar@gmail.comq', '$2y$10$4K7ZA2wCODkHitg6n3QkSuCyi7LFF/OJBCZtFD5lnvCQlEwB7sK72', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081548602000', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0267-2026', '3303141603850001', 'Pedagang', 'Kajongan RT 002 RW 002, Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440222' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Anna Avionita
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Anna Avionita', 'B0214440222', 'avionitaana@gmail.com', '$2y$10$svgKUNV0pXUzAR3F.Z9oGe5Uq3uAeJ9Inw68TMUQIm1qqGr5T9Pvy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085640306770', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0268-2026', '3303055705880003', 'ibu rumah tangga', 'Kajongan RT 002 RW 002, Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440222' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Toto barkah setyadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Toto barkah setyadi', 'A0214440223', 'totobarkah24@gmail.com', '$2y$10$wjc5g8YRP3S/0uToHgbRWOufHBktHiCW0TOYT36NE2EJfy7nUiK2S', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085800763136', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0269-2026', '3303051008880001', 'Guru', 'Kedungwuluh rt 04 rw 01, kecamatan kalimanah, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440223' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Tabah rinaya
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tabah rinaya', 'B0214440223', 'tabahkdwuluh@gmail.com', '$2y$10$CzbYJT6cmcOWppQLGt7HI.7PBp6DyH/WWurWSG5fnhge2cwgBke/2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085855720557', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0270-2026', '3303066004890002', 'Mengurus rumah tangga', 'Kedungwuluh rt 04 rw 01, kecamatan kalimanah, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440223' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ryan Baihaqi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ryan Baihaqi', 'A0214440224', 'pjmmobil89@gmail.com', '$2y$10$Nnyw84MdjXCk0P9D2.t39..jjSMOEnsNS.tFHch.QpvKlROqJdEni', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08112786630', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0271-2026', '3303061407890004', 'Wiraswasta', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440224' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Dwianinda Fegi Armitha
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dwianinda Fegi Armitha', 'B0214440224', 'annasya1309@gmail.com', '$2y$10$rlmDGELipPsinKqpa.vlleQ38iuGdJWEgssQCA.SSLg2LdemPJNQq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087710459728', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0272-2026', '3303054801900001', 'Ibu Rumah Tangga', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440224' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ahmad Dewanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ahmad Dewanto', 'A0214420126', 'dewanto10@gmail.com', '$2y$10$bA17iiAly..9sh7CmMl.POYtbkPtGYs1m2Ngfo3rn856i4Gkhl5zG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087725344900', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0273-2026', '3302261012830008', 'Karyawan Swasta', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420126' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440225' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: IIS ROVINGATUN
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('IIS ROVINGATUN', 'B0214420126', 'bundakusayang05@gmail.com', '$2y$10$Mj/0tZnhA3pd3IsC9BnFteqHFiQHmvLtfdIf7eA8/8wJ8X9TR0ZYC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085876013459', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0274-2026', '3303064503900003', 'Ibu Rumah Tangga', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420126' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440225' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Hermawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Hermawan', 'A0214460289', 'hermawan.abuarwanaila@gmail.com', '$2y$10$XGl0Z5YGkOnrkedLVmmuFeZ1TZ1jAHOCxkK4.0DeovnAOAWjvdPJi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082135744100', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0275-2026', '3303072010900001', 'Mubaligh', 'Desa karangcegak RT 12 RW 05, Kec. Kutasari, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460289' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rahmi Destiani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rahmi Destiani', 'B0214460289', 'rahmidestiani@gmail.com', '$2y$10$FJh8IJ3EtuwpOMKgqA/M6OcgdCaVOdm9cmb3ZwMwf9R7ddgv8008u', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082135744100', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0276-2026', '3303074412940003', '-', 'Desa karangcegak RT 12 RW 05, Kec. Kutasari, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214460289' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Wiwit Yanuar Udin Nugroho
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Wiwit Yanuar Udin Nugroho', 'A0214440226', 'nofikapuspitasari90@gmail.com', '$2y$10$4C0blo2jCnB1Uc7TV/ylX.PwMHIan0dMJyU7MJ9G3/3G13/0HK3qC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895322352124', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0277-2026', '3303060601860001', 'Karyawan', 'Kedungwuluh RT 8 RW 1, kecamatan Kalimanah kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440226' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nofika Puspitasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nofika Puspitasari', 'B0214440226', 'nofikapuspitasari901@gmail.com', '$2y$10$u9YFmvouz57n3pxNZnt9zOgVJoBHMmdwTh8onfefvsaZMiYlhiQIu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895322352124', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0278-2026', '3303064602890002', 'Ibu rumah tangga dan tutor PKBM', 'Kedungwuluh RT 8 RW 1, kecamatan Kalimanah kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440226' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Purnawan Wijonarko
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Purnawan Wijonarko', 'A0214440227', 'A0214440227@kajian.griyaquran.web.id', '$2y$10$L5CXavvjEtpvlcA41isXUeaYLHbzyQyI3vUbSnafOtx0yxf2MSG82', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081347172737', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0279-2026', '6402101207750004', 'Wiraswasta', 'Karang jambe RT.2 RW.3 Kecamatan Padamara Kabupaten Purbalinggamra', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440227' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Senkip Evamotik Akbar
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Senkip Evamotik Akbar', 'B0214440227', 'senkip82@gmail.com', '$2y$10$zfjNwVY8pb0L.K07ND/klenXryiJdBIj0B7kXIrqmvpw4m2d0ArcK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082322841150', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0280-2026', '6402105302820003', 'Mengurus rumah tangga', 'Karang jambe RT.2 RW.3 Kecamatan Padamara Kabupaten Purbalinggamra', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440227' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eko handoko
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eko handoko', 'A0214440228', 'A0214440228@kajian.griyaquran.web.id', '$2y$10$VyD3pUWsODtGuiBVhZUAmeZ0ofUa78iQbHnMmEdL9d38PyR7rkfTm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085731133370', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0281-2026', '3276011505840004', 'Berdagang', 'Banteran RT 01 RW 05 kecamatan Purwokerto kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440228' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Lili susanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Lili susanti', 'B0214440228', 'lilysusanti.han@gmail.com', '$2y$10$mw2N44QqpWrjTDpglTtURuPHycfTC98DM.8LLzU7rSLP7Wvo//wXy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089677199715', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0282-2026', '3302084709880001', 'Ibu rumahtangga', 'Banteran RT 01 RW 05 kecamatan Purwokerto kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440228' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Muhammad Fikri Saputro
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Muhammad Fikri Saputro', 'A0214440229', 'fikri17saputro@gmail.com', '$2y$10$zezdjhpjISPahULLXaJW7.SAf63JQN1VMq7cNY/ztsx53roln37Xy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081335617292', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0283-2026', '1803071709920003', 'Karyawan Swasta', 'Galuh RT 008 RW 004, Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440229' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Shila Anesh Sundari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Shila Anesh Sundari', 'B0214440229', 'uhumaira36@gmail.com', '$2y$10$evqbvXHgsW.MSkM4YmHWMu2A2iZrHlyCkMKZ3Bz1ukhnNrL1Ji8HG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089507994870', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0284-2026', '3303146105920001', 'Ibu Rumah Tangga', 'Galuh RT 008 RW 004, Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440229' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Fadil Dikty Mustasyfa
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Fadil Dikty Mustasyfa', 'A0214440230', 'A0214440230@kajian.griyaquran.web.id', '$2y$10$nBKhcyhvnzmyRxna2oLNKen/1K70kC5Oud6tn9S2psfXxhydoixp.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082340635615', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0285-2026', '3304020911910001', 'Wiraswasta', 'Kedungwuluh RT.01, RW.01. Kalimanah. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440230' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sofia Hikmawati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sofia Hikmawati', 'B0214440230', 'sofala.forever@gmail.com', '$2y$10$p2GqTLa/rqJ7ipSpNfQm3ejje9DXHI8lGz01VVB1C5PrUFnsadhxK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895377204485', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0286-2026', '3329035806970003', 'Pengajar TK', 'Kedungwuluh RT.01, RW.01. Kalimanah. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440230' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Firman Maolana Abdullah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Firman Maolana Abdullah', 'A0214420139', 'firmanabdullah512@gmail.com', '$2y$10$0NyRpcoyFG1DnY5ZnUYiZOOVG14tPjevMR.w5QS09vTK8X2qmwf4G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085226050070', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0287-2026', '3302222212830001', 'Guru', 'Kedunhwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420139' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440231' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ita Nur Arifiyah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ita Nur Arifiyah', 'B0214420139', 'firmanita265@gmail.com', '$2y$10$yfGIe9up2TgFDVVsUgIAdu.v1T9RaGb.JB9GttOlN2CUclfBjFLJm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081229515057', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0288-2026', '3324077010900002', 'IRT dan guru', 'Kedunhwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420139' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440231' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Pamula Panji Sanubari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Pamula Panji Sanubari', 'A0214440232', 'A0214440232@kajian.griyaquran.web.id', '$2y$10$SwTQkrlRwnd8cwLC1oMKiOZ.JNBtlBkSCDYEH7JzpDUGRinWdT9qi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '81548812016', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0289-2026', '3303050701890003', 'Wirausaha', 'Jl.Jend Sudirman gg.Baraba 23 Rt 03/03 Purbalingga Kidul - Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440232' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Uji Astuti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Uji Astuti', 'B0214440232', 'asaujay@yahoo.com', '$2y$10$pchHr2Sdz2UovX5ao6fDu.ltSKhUbuGpknE31yqbfer0U9EhU0Ska', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081548812016', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0290-2026', '3303045505890001', 'Ibu rumah tangga', 'Jl.Jend Sudirman gg.Baraba 23 Rt 03/03 Purbalingga Kidul - Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440232' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Sumardi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sumardi', 'A0214440233', 'maryamsarah34@gmail.com', '$2y$10$mujwX8NHZOyLiI4lqPXhZe5mHiQy.MGvokD6j764jSTkDpOAbEyoO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085747781223', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0291-2026', '3303051409850002', 'Guru', 'Jompo RT 01 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440233' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Umu Habibah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Umu Habibah', 'B0214440233', 'umu.habibah.ai@gmail.com', '$2y$10$yuSGTQSWuYXQph7m57DT8unH/WOqJDgkBYeVgOeXy8wrqwsCEXvqG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '85726244426', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0292-2026', '3303064901810001', 'Ibu Rumah Tangga', 'Jompo RT 01 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440233' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Karsono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Karsono', 'A0214440234', 'onocornea@gmail.com', '$2y$10$lmYWjAkGkWrRiZJd6j45xOinVITNPwupGTllI6jdojWLszl9vOhiC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081808161353', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0293-2026', '3327033112810001', 'Swasta', 'Gandatapa RT 05 RW 06, kecamatan Sumbang, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440234' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sulis Tiyowati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sulis Tiyowati', 'B0214440234', 'bintangasa28@gmail.com', '$2y$10$BfcDhBvOb7wdRnTa9nolSuWXsLvbPqv2TlNAZxK/wSOQamIaHvZVe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082210152735', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0294-2026', '33022148802900004', 'Ibu rumah tangga', 'Gandatapa RT 05 RW 06, kecamatan Sumbang, Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440234' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Husni Abdul Aziz Rais Al Habibi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Husni Abdul Aziz Rais Al Habibi', 'A0214440236', 'husnialhabibi@gmail.com', '$2y$10$Z3PeXM.l7NMvS5aA8O9p0OhIHHrOkMlVoxTQT5xzLHUD11hGXlknu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08988771080', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0295-2026', '3303060406910001', 'Buruh pabrik kayu', 'Kalimanah kulon RT 1 RW 2, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440236' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Mega Triana
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mega Triana', 'B0214440236', 'alhabibimega@gmail.com', '$2y$10$YXf51SHzIgFZr0lif/mROuTTHzTPmArSoT02p7mHGc3HD.L63biy6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089669662383', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0296-2026', '3303064808930001', 'Ibu rumah tangga', 'Kalimanah kulon RT 1 RW 2, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440236' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Taufiqurrohman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Taufiqurrohman', 'A0214440237', 'taufiqruhama@gmail.com', '$2y$10$hZohstqR7UkaHCJL5BAam.H8XzozsItZDMJv2ML/YLqnaCBs2krUG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08572657234', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0297-2026', '3303122607840001', 'Wiraswasta', 'Pekiringan Dusun 5 RT 02/10 Pekiringan Karangmoncol Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440237' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti Wulandari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti Wulandari', 'B0214440237', 'wulanummuruhma@gmail.com', '$2y$10$b9jVayxa66yNVd2tvreULOLn2FRDR1EORwrT8tcrDtXofw7ctkh/m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085641669056', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0298-2026', '3329064310960004', 'Ibu rumah tangga', 'Pekiringan Dusun 5 RT 02/10 Pekiringan Karangmoncol Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440237' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Aulia Rahman Hakim
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Aulia Rahman Hakim', 'A0214440238', 'rahmanhakim1989@gmail.com', '$2y$10$BEiB86ZXxsvACr93ykF5vu.JKT1ghswnwCxhhJDYHFtc84I4U48bm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085227322889', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0299-2026', '3305100201890002', 'PNS', 'Desa Purbalingga Wetan RT 4 RW 9 Kec. Purbalingga, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440238' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Yuliati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Yuliati', 'B0214440238', 'rahmanhakim19891@gmail.com', '$2y$10$KB37ycpbvD7OrTldE3NTA.tD99sKUPg0SINBgMuFLvjTgU2pdbEDK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081228756633', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0300-2026', '3305075508910003', 'Ibu Rumah Tangga', 'Desa Purbalingga Wetan RT 4 RW 9 Kec. Purbalingga, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440238' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Biyonic Banuyan Prabhita
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Biyonic Banuyan Prabhita', 'A0214440240', 'prabhitabiyonic@gmail.com', '$2y$10$7R.JwWsLzJ.W6EsmVYN3yeFKek4bJoK9e8fmKECM7wu1/RLYebwK6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085640544580', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0301-2026', '3303153110830001', 'Serabutan', 'Padamara RT. 003 RW. 003, Kec. Padamara, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440240' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: An-Nafi Aisyah Zur'ah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('An-Nafi Aisyah Zur''ah', 'B0214440240', 'anafi.aisyah95@gmail.com', '$2y$10$zqTGSvpDg.0EtbTfp9Uf7ebxpBevriVZFjIBpQXgrfbpRrIZtIbCW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085159493365', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0302-2026', '3374116007950001', 'Rumah Tangga', 'Padamara RT. 003 RW. 003, Kec. Padamara, Kab. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214440240' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Nurdiono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nurdiono', 'A0214430169', 'sulifahsokawera@gmail.com', '$2y$10$.XDqAylAeL.J6R5omS1BcuxIFwU7e9SBk2yZmPx.F3sy1YlU3nnLa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0303-2026', '3303153112850001', 'Petani, tukang traktor', 'Sokawera Rt 01 RW 05,kecamatan Padamara, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430169' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sulifah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sulifah', 'B0214430169', 'sulifahsokawera1@gmail.com', '$2y$10$hY3DQEVjMYpXwPdxDuN0TOiXmBFSURqZ8oTIP1/xlsxn6PAjVc.66', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0894643184', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0304-2026', '3303154902770001', 'Ibu rumah tangga', 'Sokawera Rt 01 RW 05,kecamatan Padamara, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430169' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Pujan Sukito
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Pujan Sukito', 'A0214430171', 'tsaqifmuhammad40@gmail.com', '$2y$10$722X0bXfK03k8uyvfgiNFuTx5ClxYPj6hKX/b6x2ewwbrezuL8KJm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085602158298', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0305-2026', '3305081509830003', 'Guru', 'Perumahan Puri Tama Indah blok Q4. Gemuruh. RT 2 RW 8, Padamara. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430171' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Erfina Kurnia Sari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Erfina Kurnia Sari', 'B0214430171', 'tsaqifmuhammad401@gmail.com', '$2y$10$K.FkKF.Auq6b3f6MLCDRY.iB6Mn6yzAjZzSsYdBGLKlKcUg0146cy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085743825086', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0306-2026', '3305086908870001', 'Tidak bekerja', 'Perumahan Puri Tama Indah blok Q4. Gemuruh. RT 2 RW 8, Padamara. Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430171' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Igit setiawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Igit setiawan', 'A0214430172', 'igitzsangkar@gmail.com', '$2y$10$ISajuYYgXWHH7kgA.9PoBe2kut3Y9k728FEQsuhf4j0AFH/uqbuHO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082225339991', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0307-2026', '3303050305870002', 'Dagang sangkar burung', 'Kandang gampang rt 03 rw 05 ,kecamatan /kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430172' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Dyah Maretnowati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dyah Maretnowati', 'B0214430172', 'B0214430172@kajian.griyaquran.web.id', '$2y$10$.a.Bo2ffIS7PQQ4uBNLKoeQcpXIH1k4hxwFy.P9VEHTl/5.W6qRn6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082225339992', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0308-2026', '3303054803890001', 'Ibu rumah tangga', 'Kandang gampang rt 03 rw 05 ,kecamatan /kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430172' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Era Wibowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Era Wibowo', 'A0214430173', 'eranitikan@gmail.com', '$2y$10$0cUb4NS7J.4QjLo/x48RQe1y9NGV/YJtihdD7JoQMI2VKsk0ydlRC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088806667171', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0309-2026', '3471133005750003', 'Swasta', 'Perum Griya Bantar Indah Blok J no.1 Rt 02 Rw 05 Bantarwuni Kembaran Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430173' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ira Maryati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ira Maryati', 'B0214430173', 'umi.bumburujak@gmail.com', '$2y$10$CDrqkLhlqFm.qQ2KKAhJreJjgm6ri9hpFIOAlYBv1YHtffHaWyP76', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081914967171', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0310-2026', '3471134410780002', 'Ibu Rumah Tangga', 'Perum Griya Bantar Indah Blok J no.1 Rt 02 Rw 05 Bantarwuni Kembaran Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430173' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: NURINTO
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('NURINTO', 'A0214430174', 'nurinto049@gmail.com', '$2y$10$Pbwucy3ATZ9dG/RYRp/.a.O6Fkbbe9nM9ydsnjCyMtrwKTuQBi3GS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088220139847', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0311-2026', '3303081304890002', 'Jual beli barang bekas', 'Mangunegara RT 6 RW 1 , Kecamatan Mrebet, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430174' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Lellia Agustianingsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Lellia Agustianingsih', 'B0214430174', 'nurinto0491@gmail.com', '$2y$10$q1NzUcB8E3WQ2TMj.cc8jOlBV.igjHF9DPPwteM41xVfSnPwBPsGm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895417625447', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0312-2026', '3303085608960003', 'Ibu rumah tangga dan jualan es jajan', 'Mangunegara RT 6 RW 1 , Kecamatan Mrebet, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430174' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Liman Pujo Waluyo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Liman Pujo Waluyo', 'A0214430175', 'faslijannah@gmail.com', '$2y$10$JUtNmzvUQXbJSqy/K1lbaOGUKKLlRKBgB.4tdSG8JGxxcSi7kZ1kK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082325911416', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0313-2026', '3303150101870001', 'Guru Swasta', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430175' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Astri Andikarlina
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Astri Andikarlina', 'B0214430175', 'B0214430175@kajian.griyaquran.web.id', '$2y$10$u8VJZKGK0.LNLJou.o1jp.gkwBTJ.VArLulcP9vnYAWRtK2F8sOAe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085691767822', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0314-2026', '3303066109920002', 'Mengurus Rumah Tangga', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430175' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Arri Indiarto Merenda
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Arri Indiarto Merenda', 'A0214430176', 'arinme@gmail.com', '$2y$10$5CsJD3iC6qLUpwJq1NwFdOUWzjCR6nsXYmSd9Buv8eKzI7ljNkKqK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0811291913', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0315-2026', '3303051302840003', 'Wiraswasta', 'Jl cempaka raya no 4 rt 006 rw 007 Perum Penambongan  Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430176' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Fitri Yulianingrum
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Fitri Yulianingrum', 'B0214430176', 'fitri.yulianingrum1985@gmail.com', '$2y$10$zU3Za4tnnq.m1lSKUh7HN.6Y3WG.tnEOL3emG3dt/wqdROsJPWL86', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08561650572', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0316-2026', '3303055207850001', 'Ibu Rumah Tangga', 'Jl cempaka raya no 4 rt 006 rw 007 Perum Penambongan  Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430176' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Iwan nurohman
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Iwan nurohman', 'A0214410101', 'A0214410101@kajian.griyaquran.web.id', '$2y$10$qPkjEsm/eLrzefhDDtGvh.vxvz3privLMN.raBD7z1JgSgMyQ0t7u', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0317-2026', '3303060209860001', 'Buruh harian lepas', 'Grecol rt 04 rw 01,kecamatan kalimanah,kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410101' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430177' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Resti diyanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Resti diyanti', 'B0214410101', 'ummuhanifah1122@gmail.com', '$2y$10$6dpefE7AhDEnZCTqLcjhOurM4yp21DH.pZStpawBae2V5/jp6J.12', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088221145480', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0318-2026', '3303064911910002', 'Wira usaha', 'Grecol rt 04 rw 01,kecamatan kalimanah,kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410101' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430177' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Tonny Bayu Aji
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tonny Bayu Aji', 'A0214430178', 'ynnotuyab@gmail.com', '$2y$10$jcAxyVoa2.A0ShCqT9tYnOiiQITh2RydVFVVy6Zrr0sfLr908LPKG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089602154320', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0319-2026', '3216080110780011', 'Peternak Ayam Telur', 'Bancar RT 003 RW 003, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430178' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Meta Djahara
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Meta Djahara', 'B0214430178', 'mdjahara@yahoo.com', '$2y$10$BOlh4NzxNIt0/g20NfPRL.fe71.s3v2Zdj.8V8DaCKRohYffJaUKa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081229931979', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0320-2026', '3216085209840017', 'Ibu Rumah Tangga', 'Bancar RT 003 RW 003, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430178' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: ERWIN SETIAWAN
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('ERWIN SETIAWAN', 'A0214430180', 'A0214430180@kajian.griyaquran.web.id', '$2y$10$V9B1mf0FDAxNrTXDWSAlA.mnqfQu6k93WXh9S6YXopgSjdUMhODJq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895328144837', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0321-2026', '3303051301860006', 'PEDAGANG BUBUR AYAM', 'Brobot RT 5 RW 2,Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430180' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: IIS NOFIANI
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('IIS NOFIANI', 'B0214430180', 'B0214430180@kajian.griyaquran.web.id', '$2y$10$OWjnqEJFt0IyrShBY/h7yelxoWSDj2MZ.3CEwhHmfMXVLwer9qq06', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895328144837', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0322-2026', '3303146211880001', 'Ibu rumah tangga', 'Brobot RT 5 RW 2,Kecamatan Bojongsari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430180' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Hayat Humaydi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Hayat Humaydi', 'A0214410093', 'hayathumaydi3@gmail.com', '$2y$10$EIKrJqkOt.xzVMfGmpNOo.XIOdlWYpCCfgPnEeQQ1FaLt4GdIQ6mC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089674946711', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0323-2026', '3303150606910001', 'Wiraswasta', 'Kalikabong RT 02 RW 04 kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410093' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430181' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ria Kartikasari
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ria Kartikasari', 'B0214410093', 'riakartikasari92@gmail.com', '$2y$10$ZsI2Q6hR1WmRwOIZ45gddeSL7Z7ENPB0aatjxvnYsCtVsPlIwx0K2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089665861188', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0324-2026', '3303065212910001', 'Pengajar Taud', 'Kalikabong RT 02 RW 04 kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410093' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430181' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: SUGITO
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('SUGITO', 'A0214430182', 'sugitoykhanf@gmail.com', '$2y$10$ptUbstC.sFXu8gZGDF5KwO7XG2wNfZiFf.qbbPLKHG9HzfGVY4Hsu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081390730653', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0325-2026', '3302102203900003', 'Pedagang', 'karang tengah, rt 6 rw 2 kecamatan kembaran kabupaten banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430182' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: YENI KURNIAWATI
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('YENI KURNIAWATI', 'B0214430182', 'B0214430182@kajian.griyaquran.web.id', '$2y$10$9z7lZjVC5WwW1YGDtd8U6eVVsSNDp95tX7Kb.j47KhDkkqISVg/X.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081578225868', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0326-2026', '3302205705890003', 'Guru SD', 'karang tengah, rt 6 rw 2 kecamatan kembaran kabupaten banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430182' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Affif sutriyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Affif sutriyono', 'A0214430183', 'afifsutriono8@gmail.com', '$2y$10$c5wmYKU8AVYEgRIpOQUtK.56Ak14Ld8jolVX5qsegJmiTMMJTHfLK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081536601942', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0327-2026', '3303011711790002', 'Wiraswasta', 'Karangtengah Rt 07 Rw 04, kecematan Kemangkon, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430183' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Leli Robitoh
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Leli Robitoh', 'B0214430183', 'lelikhansa17@gmail.com', '$2y$10$Xmn1uhO8AI042tjSGtY6RugKXUXO4mEaC3K7gz8ARK7qM0ItERMkO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085641746652', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0328-2026', '3303017008880001', 'Wiraswasta', 'Karangtengah Rt 07 Rw 04, kecematan Kemangkon, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430183' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Kuat pamuji
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Kuat pamuji', 'A0214430184', 'pamujikuat05@gmail.com', '$2y$10$JwsPjQ9wIhqP.hKoRnf5SORUTKaflFLl7XhNP.oqM9vE/r3X7eCMy', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082135843600', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0329-2026', '330306060183005', 'Pedagang', 'Bojanegara rt 03rw02, kecamatan padamara, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430184' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nuning widyaningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nuning widyaningsih', 'B0214430184', 'B0214430184@kajian.griyaquran.web.id', '$2y$10$5HetReVtbizcWAoYJZHh7uBqtONS4f7u/nehU20vCsdYKyOBP5joa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '088980371036', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0330-2026', '3303156202830006', 'Ibu rumah tangga', 'Bojanegara rt 03rw02, kecamatan padamara, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430184' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Niko Priyanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Niko Priyanto', 'A0214410106', 'ibnulharist@gmail.com', '$2y$10$lCpRIA2UEFZkYycQwuzKWeBzRn0E3Kt4FO3Jyo6ENtQw2GhOH1UBG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087737780051', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0331-2026', '3303040104850001', 'Swasta', 'Kaligondang RT 03 RW 04 kecamatan Kaligondang kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410106' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430185' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Siti Dyah Jayanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Siti Dyah Jayanti', 'B0214410106', 'shofiyyahdyah1@gmail.com', '$2y$10$spXzs4RcKLS1OQlph91pcuvtMMfMLoTmQVENi5vvksqm3Er33Fr1G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087764691569', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0332-2026', '3371015504880002', 'Ibu Rumah tangga', 'Kaligondang RT 03 RW 04 kecamatan Kaligondang kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410106' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430185' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eko Yulianto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eko Yulianto', 'A0214430186', 'ekoyulianto_714@yahoo.co.id', '$2y$10$cqk2YUHRhYmo77kMTvCxBOmr5g3vQ.Qrb1POe2R721z/5T2yKfS26', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085712999892', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0333-2026', '3303051407800001', 'Karyawan honorer', 'Kembangan Rt 04 Rw 05, Bukateja, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430186' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ani Wahyuni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ani Wahyuni', 'B0214430186', 'B0214430186@kajian.griyaquran.web.id', '$2y$10$q1rg7BrbkpKP3LenIfgX6.mYqnSWFCX8qn2E5R9leE7Ro7P5Hdwa2', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08163655180', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0334-2026', '3303025108830002', 'Ibu Rumah Tangga', 'Kembangan Rt 04 Rw 05, Bukateja, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430186' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Iwan Widya Tinarto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Iwan Widya Tinarto', 'A0214430187', 'A0214430187@kajian.griyaquran.web.id', '$2y$10$C1PfSZbsHVMp11wgWdb3reJqev54Qan06LopYQSGBBwBjNf/RDKHq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0335-2026', 'tidak tahu', 'tidak tahu', 'Purbalingga lor RT 02 RW 03 , kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430187' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: RETNO RIA OCTAFIANA
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('RETNO RIA OCTAFIANA', 'B0214430187', 'kendedez358@gmail.com', '$2y$10$fSZ8v7u9BVpBN9.lLyV7.O5/xvMyPQao5jfkPHbOmman/biIDer4m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081326324359', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0336-2026', '3303055110850001', 'guru TK', 'Purbalingga lor RT 02 RW 03 , kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430187' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: SAHELAN
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('SAHELAN', 'A0214430188', 'A0214430188@kajian.griyaquran.web.id', '$2y$10$QfOPvVpFfmFIfq/Bvj90aeSVa92dlnJ9G3HEPtoN/mNSe8ZWJhbYK', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0337-2026', '3303061601790001', 'Buruh dagang', 'Babakan RT 15 RW 04, Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430188' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Umi Solikhah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Umi Solikhah', 'B0214430188', 'B0214430188@kajian.griyaquran.web.id', '$2y$10$dsCTsRsycF89JrIXcif6YOmJ1QnKfCtYu/PbTYyY45VI.QBAPnXX6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082112310786', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0338-2026', '3303064410840002', 'Pengajar TK Isalam Nashirus Sunnah Purbalingga', 'Babakan RT 15 RW 04, Kalimanah, Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430188' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Rizal Fauzi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rizal Fauzi', 'A0214430189', 'fauzirizal0591@gmail.com', '$2y$10$pas19SNaRWk.wjbQUOKtYuErkK49AG1P1i1j/p/qGgMpgMRpB8G0W', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085173165491', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0339-2026', '3303051205870002', 'GURU SMK N Kutasari', 'Desa kr sari Rt 08 Rw 04 kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430189' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Wiwied Dewintari Hadyani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Wiwied Dewintari Hadyani', 'B0214430189', 'junoque1@gmail.com', '$2y$10$sVDBu3IreTvEyB/ExQ3qv.dEOdxGcAR6qIS2WtOemmO1UcUe3I59.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085159030158', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0340-2026', '3303064205880002', 'IRT', 'Desa kr sari Rt 08 Rw 04 kalimanah Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214430189' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Riyan setiawan
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Riyan setiawan', 'A0214410103', 'riyansetiawank18k@g mail.com', '$2y$10$uzF8Pv4W1pnQEQ924uJE8uUUsL3SEWW/77MaA9wmczTIr2lJubGQm', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087715127391', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0341-2026', '3303050102870004', 'Dagang', 'Purbalingga kidul RT 02,RW 01,Purbalingga kidul,Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410103' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420128' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Sri martini
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Sri martini', 'B0214410103', 'sri2703martini@gmail.com', '$2y$10$YkeY8EngzlW5/P/fiA5a.u4XyffV8zMgmev3eVhwacgRHWJKVU48O', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0897665561715', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0342-2026', '3303096703910001', 'Ngajar TAUD', 'Purbalingga kidul RT 02,RW 01,Purbalingga kidul,Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410103' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420128' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Iguh prasetianto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Iguh prasetianto', 'A0214420130', 'iguhprasetianto@gmail.com', '$2y$10$K7i20Gc7uYcZ48n0kA3HKeRslc1xSrhKDQaGwNGrV1Wy6cZLz4ysS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089659932220', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0343-2026', '3303061404710002', 'Wiraswasta', 'Kedungwuluh RT 06 RW 01 kecamatan Kalimanah ,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420130' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Winarni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Winarni', 'B0214420130', 'dhiyaaqila09@gmail.com', '$2y$10$FO8/QKg8B/nBnTm0y6t4beA1ihK5ElAuedcefvgBsBkFlbahjczI.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081297606645', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0344-2026', '3303066006730003', 'Ibu rumah tangga', 'Kedungwuluh RT 06 RW 01 kecamatan Kalimanah ,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420130' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ari Wibowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ari Wibowo', 'A0214420131', 'arsywibowo25@gmail.com', '$2y$10$X5CheAqHfcHryWZ3HYD.Qemc4nohgycRKWFEgvrVxjWIwvc2isRHG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085291525959', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0345-2026', '3303070501850004', 'Pedagang', 'Dusun 2 Meri RT 15 RW 06, Kecamatan Kutasari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420131' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Susiati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Susiati', 'B0214420131', 'B0214420131@kajian.griyaquran.web.id', '$2y$10$CMCcQfDBPGO7jQoORj5H9OTprNTTDUgmdJGL5tEBJYd0M0D0JsuLS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085865988832', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0346-2026', '3303076004850003', 'Ibu Rumah Tangga', 'Dusun 2 Meri RT 15 RW 06, Kecamatan Kutasari, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420131' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Irwanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Irwanto', 'A0214420132', 'irwanzhayya@gmail.com', '$2y$10$bhU7vfTuk8dyL/Rlh40RleUU0lmuxpbyHfX9WJENL/XJMfT85k1I6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089685511756', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0347-2026', '3303051312840002', 'Wiraswasta', 'Gemuruh RT 01 RW 07, Kecamatan Padamara  Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420132' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Shinta Irani
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Shinta Irani', 'B0214420132', 'ishin4273@gmail.com', '$2y$10$klU5SZyB3r7X.VcMKRNGnu.YNU93kyzoVD477MDVLUhoECVGFJJaS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '089508937473', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0348-2026', '3303054411810002', 'Ibu Rumah Tangga', 'Gemuruh RT 01 RW 07, Kecamatan Padamara  Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420132' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mahfudin
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mahfudin', 'A0214420133', 'abuxsafanah@gmail.com', '$2y$10$N1wpdFrYD49SMUoXARYXsOYRJ1kuYtN0BWaMTgnKPgSB6tjbCBgyS', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '087844825016', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0349-2026', '3303141405840001', 'Karyawan Swasta', 'Galuh RT 04 RW 03', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420133' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Julastri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Julastri', 'B0214420133', 'abuxkhaulah@gmail.com', '$2y$10$7lpbJuoRoXSUs2ZIHDug/ubaMH8pPAdRXO5iLCSyOqVPUbaj1uZVe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085964230630', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0350-2026', '3303114606850002', 'Guru TK', 'Galuh RT 04 RW 03', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420133' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Isto Wurio
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Isto Wurio', 'A0214420136', 'A0214420136@kajian.griyaquran.web.id', '$2y$10$hhCkt6sqgR/vm2FqQEozj.vFIn3Y70vLNn9zIGRJKBO8FNMyYYz26', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0885600636960', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0351-2026', '3303051307800005', 'Ustadz', 'Kedungwuluh RT 2 RW 4,Kecamatan Kalimanah,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420136' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Reni Yuliyanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Reni Yuliyanti', 'B0214420136', 'reniyulianti073@gmail.com', '$2y$10$gnfv.NpJ.DLoIPAV86UF8O3XicwCVRCleCrf2R9dyYWVMJr5E0Yq.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082328381600', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0352-2026', '3303055807830003', 'Bidan', 'Kedungwuluh RT 2 RW 4,Kecamatan Kalimanah,Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420136' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eko Diyono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eko Diyono', 'A0214420138', 'A0214420138@kajian.griyaquran.web.id', '$2y$10$k4OLlOwVFy8BDJg7Dc1zHOQ5mWh.rvxjqKfnDFt2Ufw7CxV6nzTCu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0353-2026', '3302100803780003', 'Membantu orang tua di pasar', 'Pekaja RT 05 RW 03 Kecamatan Kalibagor Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420138' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Estining Pribadi
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Estining Pribadi', 'B0214420138', 'estiningpribadi@gmail.com', '$2y$10$ZfB4TKZZ8z/mUFKQhDNAduqGPbRh5qt68HXvbZHGc22D57eDvPFhW', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081327933877', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0354-2026', '3303056404870003', 'Ibu rumah tangga', 'Pekaja RT 05 RW 03 Kecamatan Kalibagor Kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420138' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Susilo eko putro
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Susilo eko putro', 'A0214420141', 'susiloekoputro3@gmail.com', '$2y$10$lqvfSmwLSBmGwV7ZwgctUeRsF3QTjO1ahHVQM4cyCfYCOjWnL.41q', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081391040369', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0355-2026', '3303142808780002', 'Pengrajin kayu_', 'Bojongsari rt02 rw13 bojongsari purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420141' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Yuliati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Yuliati', 'B0214420141', 'pagutan0213@gmail.com', '$2y$10$BuSiZKlGehwSeDXeUSZhtOpWzJNaD6eO4DDJC3dv0EBaUD6fj1q5u', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085706346365', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0356-2026', '3303145507840001', 'Ibu rumah tangga', 'Bojongsari rt02 rw13 bojongsari purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214420141' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Tuwarno
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Tuwarno', 'A0214410095', 'A0214410095@kajian.griyaquran.web.id', '$2y$10$56Sx1vOIYH3Z7ty8ohUAe.JnvneVoqC6jsC5d7MnGIZdch70a1nEe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0882005655534', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0357-2026', '3303070603930002', 'Petani', 'Karang Banjar RT 16 RW 06, kecamatan Bojongsari, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410095' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Rizki Saputri
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Rizki Saputri', 'B0214410095', 'saputririzki119@gmail.com', '$2y$10$yiPAT5AEV9eadUGNlq0QXuEj12I1g.udDvcMZ/K0Oji6uUhJXHPL.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08980661250', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0358-2026', '3303145205930002', 'Ibu rumah tangga', 'Karang Banjar RT 16 RW 06, kecamatan Bojongsari, kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410095' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Okto Parmono
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Okto Parmono', 'A0214410096', 'oktopbg12@gmail.com', '$2y$10$9XQsEx8NC9cvyBjnQ6ahbuEGr22tcmZIp.ayqyxVuVV68H8zULGqu', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0895622396996', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0359-2026', '3303051610860001', 'Dagang', 'Purbalingga Kidul RT 03 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410096' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Nani Juwariyah
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Nani Juwariyah', 'B0214410096', 'nani.juwariyah@gmail.com', '$2y$10$fWJes4lOWJI6mibKj7D/z.TlzfaAP/Nkl6Fi57KpUqXKJVwmJAbKG', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085700618861', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0360-2026', '3303054805900003', '-', 'Purbalingga Kidul RT 03 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410096' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Ruspriyanto, AMK
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ruspriyanto, AMK', 'A0214410097', 'ruspri84@gmail.com', '$2y$10$zbZUDMfw/pX9L4rd5ZuCweJQC4.ipsyOg2pdSJUi0waPOizl7SlSC', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '0882006210885', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0361-2026', '3303152603840002', 'Perawat', 'Sokawera RT 2 RW 4 kecamatan Padamara kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410097' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Ririn Windia Sari, AMK
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Ririn Windia Sari, AMK', 'B0214410097', 'ummusumayyahpbg2020@gmail.com', '$2y$10$tij0yUdsNlT5XTUPtGnqm.ovsgQeERxjx44vcjTTa3YuAG03g4b1a', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081327846373', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0362-2026', '3303155512850002', 'PNS', 'Sokawera RT 2 RW 4 kecamatan Padamara kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410097' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Eko prihayanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eko prihayanto', 'A0214410098', 'faisol pbg@gmail.com', '$2y$10$rimV04pcMr3JXyiiSzAFIOblbzmtC2V0ThHbzzfyw.YpsW4LH8Q/m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081391709081', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0363-2026', '3303062001790004', 'Karyawan swasta', 'Klapasawit RT 1 RW 2 , kalimanah, purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410098' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Poniati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Poniati', 'B0214410098', 'faisol pbg @gmail.com', '$2y$10$tHDa.owaxto91BebdwzhAOaqygufOf4bA13h0i0H2dMe3rtcw/P/G', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085700919918', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0364-2026', '3303065102830001', 'Karyawan swasta', 'Klapasawit RT 1 RW 2 , kalimanah, purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410098' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Hari Waluyo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Hari Waluyo', 'A0214410100', 'hariwaluyo51346@gmail.com', '$2y$10$Uecb6A04AFjB7IBeZodjlOayiksLtHNqeak4G6jlnkVy0LR8zC0gO', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '08994499759', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0365-2026', '3302202306800005', 'Wiraswasta', 'Sambeng Kulon RT 04 RW 01 kecamatan Kembaran kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410100' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Reni Yustanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Reni Yustanti', 'B0214410100', 'B0214410100@kajian.griyaquran.web.id', '$2y$10$/BcheZU8bIdf.wKcRTbMuOGEKBwhzttTBZoIO4XpWzgT63/5VH90i', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '1', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0366-2026', '3302205911860005', 'Ibu rumah tangga', 'Sambeng Kulon RT 04 RW 01 kecamatan Kembaran kabupaten Banyumas', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410100' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Yuniarto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Yuniarto', 'A0214410062', 'yuniarto1249@gmail.com', '$2y$10$mYcfRfCgwlfBwURm8L8LIOLMgRcha.v2hRkvl/a82DM2Cpw5OJhUi', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082221820408', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0367-2026', '3303063006800002', 'Wiraswasta', 'Kedungwuluh RT 08 RW 02 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410062' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Dwi Murwatiningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Dwi Murwatiningsih', 'B0214410062', 'dwimurwatiyuniar@gmail.com', '$2y$10$nYzoiiWCyTDMy4LtWNKKo.pdvDPivCWM27A.SyEBBVxdCwNEhoa6e', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085186867500', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0368-2026', '3303065409830001', 'Ibu Rumah Tangga', 'Kedungwuluh RT 08 RW 02 Kecamatan Kalimanah, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410062' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Untung Prasetyo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Untung Prasetyo', 'A0214410102', 'pras_sut05@yahoo.com', '$2y$10$gygE2jcNVYD6w15sSEIdvOk9sQZGG1OScTkwbkkJw5T9cCMpGCcVq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081225456304', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0369-2026', '3671052608710003', 'Wiraswasta', 'Jl letkol isdiman no 01 rt/rw 01/04, kecamatan purbalingga kidul, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410102' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Reni
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Reni', 'B0214410102', 'uncuren0505@gmail.com', '$2y$10$b6hoiEZ35/pesegVyGxLVOCjhanzGgLNG5MtHsu769TUWR3HkZLTe', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '081218591170', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0370-2026', '3671054505770021', 'Ibu rumah tangga', 'Jl letkol isdiman no 01 rt/rw 01/04, kecamatan purbalingga kidul, kabupaten purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410102' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Udayanto Wibowo
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Udayanto Wibowo', 'A0214410104', 'wibowoudayanto@yahoo.com', '$2y$10$WXeWYvG2NBBUuAaFOUH3P.D1infinAWL.8DHz/mnJ7JuRmyO9lece', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082136281956', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0371-2026', '3303150904750004', 'Karyawan Swasta', 'Desa Bojanegara, RT 01 RW 03, Kecamatan Padamara, Kabupaten Purbalingga Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410104' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Evi Nurnaningsih
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Evi Nurnaningsih', 'B0214410104', 'dobog888@gmail.com', '$2y$10$qF2tbq09GRgyp8wgENrAfOL6twK9hnARtz2WgOagn0VST29u.zukq', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082227047639', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0372-2026', '3303156912770001', 'Ibu rumah tangga', 'Desa Bojanegara, RT 01 RW 03, Kecamatan Padamara, Kabupaten Purbalingga Jawa Tengah', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410104' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Teguh Sukanto
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Teguh Sukanto', 'A0214410059', 'sahteguh7@gmail.com', '$2y$10$B0MtPXxJ52IIw8jkXWNzq.OPXlS7PgCmliDhKHw875zXDKce4pMVa', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082227917382', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0373-2026', '3303052401800001', 'Wiraswasta', 'Desa Purbalingga Kidul, RT.002/RW.001, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410059' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Lestari Suhartanti
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Lestari Suhartanti', 'B0214410059', 'suhartanti@gmail.com', '$2y$10$.9aFKkomv7sfsFiCjtgeKeEBn.Jznvx2eyL0kfy9IeEz3ftZJ6vN6', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '082227917382', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0374-2026', '3303066004860002', 'Ibu Rumah Tangga', 'Desa Purbalingga Kidul, RT.002/RW.001, Kecamatan Purbalingga, Kabupaten Purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214410059' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ayah: Mustofa
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Mustofa', 'A0214400048', 'alifmustofa79@gmail.com', '$2y$10$mIDjpk3vYIczjPQFW1sdLuCSzmJ3acaRYTJb.SKQGuEtfdfmc5/.m', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085747541696', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ayah_user_id, 'father', 0, 'WS-IMPORT0375-2026', '3303060106790001', 'Kurir katering', 'Jompo Rt 01 RW 04 kec kalimanah kab purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ayah_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ayah_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214400048' LIMIT 1), 'biological', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

-- Ibu: Eka Trisnawati
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role_id`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES ('Eka Trisnawati', 'B0214400048', 'B0214400048@kajian.griyaquran.web.id', '$2y$10$O16DNfim2fTk.giBSEemtuZInbZ32TXHtcicqE2vLmNqD807iQeZ.', (SELECT `id` FROM `roles` WHERE `name` = 'wali_santri' LIMIT 1), '085799305952', 1, '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_user_id = LAST_INSERT_ID();
INSERT INTO `parents` (`user_id`, `type`, `is_single_parent`, `qr_code_string`, `nik`, `occupation`, `address`, `created_at`, `updated_at`) VALUES (@ibu_user_id, 'mother', 0, 'WS-IMPORT0376-2026', '3303065806820001', 'Ibu rumah tangga', 'Jompo Rt 01 RW 04 kec kalimanah kab purbalingga', '2026-04-14 01:57:23', '2026-04-14 01:57:23');
SET @ibu_parent_id = LAST_INSERT_ID();
INSERT INTO `parent_student` (`parent_id`, `student_id`, `relationship`, `is_primary_contact`, `created_at`, `updated_at`) VALUES (@ibu_parent_id, (SELECT `id` FROM `students` WHERE `nis` = '0214400048' LIMIT 1), 'biological', 0, '2026-04-14 01:57:23', '2026-04-14 01:57:23');

SET FOREIGN_KEY_CHECKS = 1;
