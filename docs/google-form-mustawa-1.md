# Google Form Presensi Mustawa 1

Integrasi ini memakai satu Google Form permanen. Wali Mustawa 1 mengisi Form untuk menyimak online atau berhalangan; respons masuk ke aplikasi sebagai presensi `pending` dan diperiksa admin.

## Konfigurasi Coolify

Tambahkan environment variable berikut pada aplikasi Laravel:

```ini
GOOGLE_FORM_WEBHOOK_SECRET=secret-acak-panjang
```

Gunakan secret yang sama pada `CONFIG.WEBHOOK_SECRET` di Apps Script. Jangan menaruh secret tersebut di Form atau membagikannya ke wali.

## Memasang Apps Script

1. Buka `script.google.com` menggunakan akun pengelola Form.
2. Buat project baru.
3. Salin isi `integrations/google-forms/mustawa-1/Code.gs` ke editor Apps Script.
4. Isi `APP_URL` dan `WEBHOOK_SECRET`.
5. Jalankan `createOrConfigureForm()` satu kali.
6. Setujui permintaan akses Google Forms dan koneksi HTTP.
7. Buka **Executions/Logs**, lalu ambil `LINK WALI` untuk dibagikan ke Grup Mustawa 1.

Fungsi tersebut membuat pertanyaan, mengambil daftar santri Mustawa 1 aktif dari aplikasi, dan memasang trigger `onFormSubmit`.

## Sinkronisasi daftar santri

Jika ada santri Mustawa 1 baru atau perubahan data, jalankan `syncMustawa1Students()` dari Apps Script. Pilihan Form akan diperbarui dalam format `Nama Ananda — NIS`.

## Pemeriksaan

Endpoint menerima:

```text
POST /api/integrations/google-forms/mustawa-1
GET  /api/integrations/google-forms/mustawa-1/options
```

Aplikasi tidak membuat akun baru. Jika NIS, nama wali, nomor HP, dan jenis wali cocok, sistem membuat presensi `hadir_online` atau `izin` dengan metode `google_form` dan status `pending`.

Jika data tidak cocok, respons disimpan sebagai `unresolved`. Admin dapat memperbaiki data wali lalu menekan **Retry** dari halaman **Admin → Validasi Presensi**.

Untuk uji cepat, kirim satu respons asli dari Form dan cek apakah muncul sebagai sumber `Google Form` di halaman validasi admin.
