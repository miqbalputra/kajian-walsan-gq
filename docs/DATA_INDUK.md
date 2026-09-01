# Integrasi Data Induk Yayasan

Kajian Walsan adalah aplikasi konsumen. Data Induk Yayasan menjadi sumber
identitas siswa; aplikasi Kajian hanya menyimpan *read model* lokal untuk
mempertahankan relasi presensi, QR, dan histori Kajian.

## Konfigurasi

1. Pada Data Induk, buat API key bernama **Kajian Walsan** dengan scope
   `students.roster.read`. Batasi key tersebut ke unit sekolah Kajian bila
   memungkinkan.
2. Isi environment aplikasi Kajian:

   ```dotenv
   DATA_INDUK_ENABLED=true
   DATA_INDUK_BASE_URL=https://data.contoh-yayasan.id/api/v1
   DATA_INDUK_API_KEY=mdi_xxx
   DATA_INDUK_UNIT_ID=uuid-unit-kajian
   DATA_INDUK_CLASS_MAP={"uuid-kelas-data-induk":12}
   ```

   `DATA_INDUK_CLASS_MAP` memasangkan UUID kelas dari Data Induk dengan
   `id` kelas yang sudah ada pada database Kajian. Gunakan `{}` bila Kajian
   belum membedakan kelas; data siswa tetap dapat disinkronkan tanpa mengganti
   kelas lokal yang telah ada.
3. Bersihkan cache konfigurasi setelah mengubah environment:

   ```bash
   php artisan config:clear
   ```

## Menjalankan sinkronisasi

Admin dapat menekan **Sinkronkan Data Induk** di menu Manajemen Siswa atau
menjalankan perintah berikut dari server Kajian:

```bash
php artisan data-induk:sync-roster
```

Perintah hanya memanggil `GET /integrations/students` dan menggunakan ID
master respons sebagai `students.data_induk_id`. Perubahan data pokok selalu
dilakukan di aplikasi Data Induk.

## Batasan yang disengaja

- Sinkronisasi hanya menambah atau memperbarui roster aktif; tidak menghapus
  siswa lokal yang menghilang dari roster, agar absensi dan histori tetap utuh.
- Aplikasi Kajian tidak menyalin atau membuat kredensial wali dari API. Akun
  wali, QR, dan transaksi presensi tetap dikelola oleh Kajian.
- Konflik NIS atau kelas yang belum dipetakan dilaporkan, bukan ditimpa.
