# Dashboard wali kelas

Dashboard `/wali-kelas` menggunakan penugasan `classes.teacher_id` yang sudah ada. Satu akun menangani satu kelas. Halaman `/wali-kelas/santri/{student}` hanya dapat dibuka untuk santri aktif yang saat ini berada di kelas penugasan, termasuk pada permintaan Livewire dan ekspor.

## Data dan perhitungan

- Halaman awal memilih kajian terakhir yang sudah ditutup dalam tahun ajaran aktif. Rekap tanpa pilihan kajian hanya menghitung kajian yang telah selesai; kajian mendatang tidak menurunkan persentase.
- Ayah dan ibu dihitung terpisah. Ringkasan kelas menggunakan pasangan unik wali–kajian sehingga saudara sekandung tidak menggandakan persentase. Daftar dan ekspor tetap dikelompokkan menurut santri.
- Hanya hadir fisik/online yang disetujui dihitung hadir. Izin, menunggu validasi, dan ditolak memiliki kategori tersendiri. Alfa hanya diturunkan untuk sasaran kajian yang sudah ditutup.
- Riwayat seluruh tahun dan kelas sebelumnya tersedia bagi wali kelas santri saat ini. Snapshot peserta diprioritaskan, lalu bukti presensi langsung dan enrollment/hubungan keluarga yang dapat dipastikan. Data lama yang tidak cukup pasti ditandai `Data belum lengkap` dan dikecualikan dari persentase.
- Layanan `WaliKelasReportService` dipakai bersama oleh dashboard, profil, laporan, dan ekspor. Data keluarga tidak memuat nama saudara di luar kelas penugasan. Query relasi dilakukan secara berkelompok.

## Tindak lanjut

Setiap kombinasi santri–wali–kajian dengan satu alfa merupakan kasus. Kasus tanpa catatan dihitung saat membaca, tanpa membuat baris presensi ataupun kasus baru. Kasus baru disimpan di `guardian_follow_up_cases` ketika aktivitas pertama ditambahkan; aktivitas selanjutnya selalu ditambahkan ke `guardian_follow_up_entries`.

Status: belum ditindaklanjuti (`open`), dalam proses (`in_progress`), selesai (`completed`). Setiap perubahan wajib memiliki hasil komunikasi. Tenggat terlewat didahulukan, kemudian tanggal kajian terlama. Kasus yang belum selesai tetap muncul meskipun filter dashboard berubah. Menyelesaikan satu kasus tidak menyelesaikan kasus kajian lainnya dan tidak mengubah kehadiran.

Wali kelas pengganti dapat melanjutkan kasus santri yang menjadi tanggung jawabnya. Nama pencatat disimpan pada aktivitas agar riwayat penulis tetap tersedia. Catatan admin lama pada presensi ditampilkan sebagai catatan presensi dan tidak dimigrasikan otomatis. Tautan WhatsApp dibuka oleh pengguna; aplikasi tidak mengirim pesan ataupun pengingat otomatis.

## Rilis dan verifikasi

1. Deploy source dan hasil `npm run build` melalui alur deployment yang sudah digunakan.
2. Jalankan `php artisan migrate --force` untuk migrasi tambahan `2026_10_18_000001_create_guardian_follow_ups`. Migrasi ini hanya membuat dua tabel; tidak mengubah penugasan maupun presensi lama.
3. Pastikan akun berperan `wali_kelas` dan telah dipilih pada penugasan kelas. Periksa halaman kelas, detail santri, filter, ekspor, lalu catat dan selesaikan satu kasus percobaan yang sesuai.

Pengujian fitur: `php vendor/bin/phpunit tests/Feature/WaliKelasDashboardTest.php`. Cakupan meliputi pembatasan akses, manipulasi ID, riwayat lintas tahun, saudara sekandung, data lama, kasus berulang, pergantian wali kelas, pembacaan tanpa perubahan database, ekspor, dan jumlah query.

UI menggunakan font lokal, CSS dengan lingkup `wc-page`, dan Livewire/Alpine bawaan. Tidak diperlukan layanan atau paket JavaScript baru. Navigasi halaman menggunakan bahasa Indonesia; banner instalasi mengambang tidak ditambahkan pada halaman wali kelas.

Catatan pemeriksaan tambahan: `WaliPagesTest::test_profile_avatar_contact_validation_and_card_download_are_preserved` memakai fixture `wali@example.test`, sedangkan aturan profil wali santri pada source yang sudah ada mewajibkan Gmail. Tes lama tersebut gagal pada validasi email, terpisah dari perubahan dashboard wali kelas. Fixture dan aturan profil tidak diubah dalam pekerjaan ini.
