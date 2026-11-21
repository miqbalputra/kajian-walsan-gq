# Perbaikan laporan keamanan — deployment tanpa perubahan data

## Status

Perubahan disiapkan di repository; belum diterapkan ke produksi. Pemeriksaan
read-only domain pada 21 November 2026 menemukan Cloudflare di depan aplikasi,
HTTP masih 200, CSP belum ada, dan X-Powered-By masih mengungkap versi PHP.
Pemeriksaan ini tidak menguji negosiasi TLS; versi TLS pada laporan awal harus
diverifikasi ulang setelah pengaturan Cloudflare diperbarui.

## Perubahan aplikasi

- CSP membatasi sumber aset, menolak object/embed, dan membatasi framing,
  base URL serta tujuan form ke origin sendiri. Inline script/style dan evaluasi
  Alpine masih diizinkan untuk kompatibilitas Livewire yang sudah dipakai.
  Ini baseline CSP, bukan kebijakan nonce yang menutup seluruh kemungkinan XSS.
  CSP tidak diberlakukan dalam environment local agar Vite tetap berfungsi.
- PHP tidak lagi mengirim X-Powered-By; Nginx tidak mengungkap nomor versi.
- Request HTTP produksi dialihkan ke APP_URL HTTPS: 301 untuk GET/HEAD dan 308
  untuk metode lain agar body tidak hilang. Health probe /up tetap tersedia.
  Trusted proxy tetap digunakan untuk mencegah redirect loop dan header spoofing.
- Cookie XSRF-TOKEN kini HttpOnly. CSRF tetap diperiksa lewat token form,
  Livewire, dan X-CSRF-TOKEN; Axios membaca meta token untuk origin sendiri.
  Nama, nilai, scope, dan penyimpanan sesi tidak diubah.
- Google Fonts diganti font lokal dengan cakupan ikon lengkap. Script CDN
  ApexCharts, SweetAlert2 dan QR scanner dipin dan diberi SRI + crossorigin.
- Startup kini melewati migrasi secara default. RUN_MIGRATIONS=true hanya untuk
  deployment skema terpisah yang sudah direview dan dicadangkan.

## Cloudflare dan proxy — wajib diterapkan terpisah

Kode PHP tidak dapat menonaktifkan TLS 1.0/1.1 pada koneksi pengunjung ke
Cloudflare. Pada zona griyaquran.web.id, buka SSL/TLS → Edge Certificates:

1. Set Minimum TLS Version ke TLS 1.2 (TLS 1.3 tetap dapat digunakan).
2. Aktifkan Always Use HTTPS agar request HTTP tidak mencapai aplikasi.
3. Gunakan SSL/TLS Full (strict) setelah memastikan sertifikat origin valid.
   Jangan memakai Flexible bersama redirect HTTPS di origin.

Pengaturan zona dapat memengaruhi subdomain lain. Jika redirect harus khusus
kajian.griyaquran.web.id, gunakan Redirect Rule yang mencocokkan hostname itu
dan skema HTTP, status 301, serta mempertahankan path dan query. Minimum TLS
per hostname memerlukan Advanced Certificate Manager menurut dokumentasi
Cloudflare; minimum TLS zona sebaiknya ditinjau untuk domain lain yang dipakai.

Untuk origin Coolify berbasis Traefik, gabungkan
`docker/traefik/kajian-security.yaml` ke dynamic configuration proxy yang sudah
dipantau file provider. Tambahkan label berikut pada router HTTPS aplikasi yang
sudah ada (ganti placeholder dengan nama router nyata, jangan buat router baru):

```text
traefik.http.routers.<existing-https-router>.tls.options=kajian-modern@file
```

Jangan menimpa konfigurasi proxy global atau mengubah sertifikat/volume. Nginx
container hanya listen port 80 internal; TLS publik berakhir di Cloudflare dan
proxy, sehingga menambah ssl_protocols ke Nginx ini tidak memperbaiki TLS publik.

Referensi:
- https://developers.cloudflare.com/ssl/edge-certificates/additional-options/minimum-tls/
- https://developers.cloudflare.com/ssl/edge-certificates/additional-options/always-use-https/
- https://doc.traefik.io/traefik/reference/routing-configuration/http/tls/tls-options/
- https://laravel.com/docs/12.x/csrf#x-csrf-token

## Urutan deployment aman

1. Cadangkan database produksi dan storage/upload dengan mekanisme backup yang
   sudah digunakan. Pastikan backup dapat dibaca/dipulihkan; simpan di luar
   container aplikasi. Jangan menimpa backup sebelumnya.
2. Catat jumlah record tabel bisnis sebelum deployment dan bandingkan setelahnya
   pada jendela tanpa penulisan pengguna/job. Untuk kepastian isi tidak berubah,
   bandingkan snapshot/dump tabel bisnis pada jendela yang sama.
3. Pertahankan APP_KEY, seluruh DB_* env, cookie/session config, persistent
   database volume, dan storage mount yang sekarang. Jangan mengganti resource
   MariaDB, mengimpor SQL awal, menjalankan seeder, migrate:fresh, atau reset.
4. Set RUN_MIGRATIONS=false secara eksplisit di Coolify. Pastikan APP_ENV=production,
   APP_DEBUG=false, APP_URL=https://kajian.griyaquran.web.id,
   SESSION_SECURE_COOKIE=true, SESSION_HTTP_ONLY=true, dan TRUSTED_PROXIES sesuai
   hop proxy nyata. Jangan membuka port 80 container langsung ke internet.
5. Review perubahan lain yang sudah ada di workspace sebelum membangun rilis.
   Workspace memuat perubahan Data Induk yang berada di luar perbaikan keamanan;
   jangan ikut merilisnya tanpa review terpisah.
6. Deploy image aplikasi dengan mount lama dan konfigurasi proxy di atas.
   Log startup harus menunjukkan migrasi dilewati. Tidak ada migrasi atau
   seeder baru yang dibutuhkan untuk perbaikan ini.
7. Verifikasi header HTTPS: CSP ada, X-Powered-By tidak ada, kedua cookie HttpOnly
   dan Secure. HTTP harus 301 ke path/query HTTPS yang sama.
8. Dengan TLS scanner yang mendukung protokol lama, pastikan TLS 1.0/1.1 ditolak
   dan TLS 1.2/1.3 berhasil. Kegagalan client lokal karena cipher/protokol tidak
   didukung tidak cukup untuk membuktikan server telah menolak protokol lama.
9. Smoke test login, form/Livewire, upload foto, QR scanner, dashboard admin,
   cetak kartu dan PWA. Bandingkan snapshot data, lalu ulangi pemindaian keamanan.

Rollback menggunakan image rilis sebelumnya serta konfigurasi proxy yang telah
dicadangkan, tetap memakai database, APP_KEY dan volume yang sama. Tidak perlu
rollback skema karena deployment ini tidak menjalankan migrasi.

## Validasi lokal

Pengujian PHP menggunakan phpunit.xml: SQLite :memory:, session array, cache
array; tidak memakai database produksi. Uji regresi mencakup cookie HttpOnly,
CSRF valid/tidak valid, redirect, trusted proxy, header spoofing, dan HSTS.
Build Vite memaketkan font lokal. Validasi runtime konfigurasi Nginx/Traefik serta
pengaturan Cloudflare tetap harus dilakukan saat deployment karena akses
pengelolaan produksi tidak tersedia dalam sesi ini.

Hasil: 39 pengujian PHP (278 assertion) dan 4 pengujian JavaScript lulus;
build produksi Vite dan pemeriksaan format PHP lulus. Setelah pemisahan aset
font, 31 pengujian PHP terkait dijalankan ulang dan tetap lulus. Pengujian
JavaScript memastikan token CSRF dikirim ke origin sendiri dan tidak bocor ke
origin eksternal. Checksum font lokal juga terverifikasi.
