# Arsip guru resign / keluar

Admin membuka **Data Guru**, memilih **Arsipkan guru resign / keluar** pada baris guru, lalu mengisi tanggal keluar dan alasan. Tanggal keluar tidak boleh di masa depan. Halaman **Arsip Guru** tersedia dari tombol pada Data Guru dan hanya dapat dibuka admin.

## Data dan status

- `parents.teacher_archived_at` menandai bahwa peran guru telah diarsipkan. Identitas, relasi anak, QR, dan presensi tidak dihapus.
- `teacher_archives` menyimpan satu arsip per profil: tanggal keluar, alasan, admin pelaksana, waktu arsip, salinan profil, dan salinan riwayat presensi beserta tautan buktinya. Salinan tidak memuat kata sandi atau token akun.
- Arsip hanya untuk dibaca. Tidak ada alur edit, hapus, atau aktivasi ulang arsip. Pengaman model juga menolak perubahan dan penghapusan arsip.
- `ParentModel::activeTeachers()` menjadi sumber daftar guru aktif. Dashboard admin, presensi guru, laporan kepala sekolah, hitungan API, serta roster AI memakai aturan yang sama. Guru arsip dikeluarkan dari perhitungan guru, termasuk Alpha; riwayat lama tetap tersedia dalam arsip admin.

## Akun dengan dua peran

Guru murni yang diarsipkan dinonaktifkan, termasuk alias loginnya. Akses dari sesi yang masih terbuka juga ditolak. Profil, akun, serta presensi guru murni tidak dapat diedit atau dihapus melalui model aplikasi.

Jika guru masih menjadi ayah/ibu dengan anak aktif, akun orang tuanya dipertahankan. Peran login guru/wali kelas dikembalikan menjadi wali santri, dan penugasan wali kelas dilepas. Orang tersebut tetap dihitung dalam tren orang tua, tetapi dikeluarkan dari tren guru. Identitas dan transaksi orang tua dapat terus berjalan; salinan profil dan presensi dalam arsip guru tetap tidak berubah.

Arsip kepegawaian guru merupakan status lokal aplikasi Kajian. Pengarsipan tetap dapat dilakukan ketika identitas wali/guru pada Data Induk bersifat hanya baca; aplikasi tidak mengubah identitas pusat.

## Penerapan

Jalankan migrasi `2026_10_19_000001_create_teacher_archives.php` sebelum memakai fitur. Migrasi menambah kolom status dan tabel arsip tanpa mengarsipkan siapa pun secara otomatis. Guru yang resign dipilih dan diarsipkan secara eksplisit oleh admin.

Pengujian utama ada pada `tests/Feature/TeacherArchiveTest.php`, mencakup pengarsipan, akses admin, pengamanan perubahan, akun dengan dua peran, salinan riwayat, validasi tanggal/alasan, dan pengiriman arsip berulang.
