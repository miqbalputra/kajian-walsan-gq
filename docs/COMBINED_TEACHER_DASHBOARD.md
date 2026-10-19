# Satu akun: guru, wali kelas, dan orang tua

Admin mengatur identitas di **Data Guru** atau **Orang Tua**:

- Untuk guru murni, pilih **Guru saja**.
- Untuk guru yang juga orang tua, pilih **Ayah + Guru** atau **Ibu + Guru**, kemudian sambungkan anaknya. Pada formulir Orang Tua, gunakan centang **Guru/Pengajar**.
- Centang **Juga sebagai Wali Kelas**, lalu pilih kelas binaannya. Kelas binaan tidak harus sama dengan kelas anaknya.

Tidak perlu membuat akun kedua atau mengganti role akun secara manual untuk memperoleh akses kelas. Penugasan memakai `classes.teacher_id` yang sudah ada. Guru dengan profil aktif dapat dipilih melalui menu Kelas, termasuk guru dengan role dasar `wali_santri` karena juga orang tua.

## Beranda

Semua akun guru, wali santri, dan wali kelas masuk ke beranda pribadi. Jika ada kelas aktif yang ditugaskan, beranda menampilkan pilihan **Presensi Saya** dan **Kelas Saya**.

Bagian presensi pribadi tetap menyediakan QR atau unggah bukti sesuai identitas guru/orang tua. Satu catatan presensi tetap digunakan untuk kedua peran dan dihitung dalam tren orang tua serta guru sesuai ketentuan sebelumnya. Bagian kelas menggunakan komponen laporan wali kelas yang sudah ada, termasuk filter, daftar ayah/ibu, tren, dan tindak lanjut.

## Batas akses dan penugasan

- Data kelas selalu dicari dari akun yang sedang login, bukan dari ID kelas yang dikirim browser. Kelas lain dan santri di luar kelasnya tetap tidak dapat dibuka.
- Memilih kelas yang sudah dimiliki guru lain dari formulir guru ditolak. Pergantian wali kelas dilakukan secara eksplisit melalui menu Kelas.
- Satu guru ditugaskan ke satu kelas aktif. Memindahkan kelas melalui profil guru melepas penugasan lamanya. Menu Kelas menolak memilih guru yang sudah memiliki kelas aktif lain.
- Melepas centang wali kelas menghentikan penugasannya tanpa menghilangkan presensi pribadi atau hubungan anak.
- Guru yang diarsipkan tidak dapat ditugaskan. Pengarsipan melepas penugasan kelas, sementara akses orang tua dengan anak aktif tetap dipertahankan.

Peran administrasi, panitia, dan kepala sekolah tetap memakai pemeriksaan role dasarnya. Kemampuan guru dan orang tua mengikuti profil, sedangkan kemampuan wali kelas mengikuti penugasan kelas aktif. Role wali kelas lama tetap dikenali untuk kompatibilitas; tanpa penugasan, halaman kelas hanya menampilkan kondisi belum memiliki kelas.

## Penerapan dan pengujian

Migrasi `2026_10_19_000002_mark_legacy_class_teacher_profiles.php` menandai profil wali kelas lama sebagai guru aktif tanpa mengubah tipe ayah/ibu maupun mengaktifkan guru arsip. Akun lama tanpa profil mendapat satu profil guru saat pertama membuka beranda, mengikuti alur pembuatan profil guru yang sudah ada.

Pengujian kombinasi peran, penugasan kelas, rollback konflik, akses kelas, dan arsip berada di `tests/Feature/CombinedTeacherDashboardTest.php`.
