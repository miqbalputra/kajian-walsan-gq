# Panduan Instalasi Coolify & Deploy App Kajian Walsan

Berikut adalah panduan langkah-demi-langkah untuk menginstal Coolify di VPS dan men-deploy aplikasi Kajian Walsan v2 menggunakan PostgreSQL.

---

## 1. Instalasi Coolify di VPS

Gunakan VPS baru dengan sistem operasi **Ubuntu 22.04 LTS** atau **24.04 LTS**. Hubungkan via SSH dan jalankan perintah satu baris ini:

```bash
curl -fsSL https://get.coollabs.io/coolify/install.sh | bash
```

Setelah instalasi selesai, buka browser dan akses `http://IP_VPS_KAMU:8000`. Selesaikan pendaftaran akun admin pertama.

---

## 2. Setup PostgreSQL di Coolify

1. Di dashboard Coolify, klik **Resources** > **New**.
2. Pilih **Database** > **PostgreSQL**.
3. Isi informasi berikut:
   - **Name**: `kajian-walsan-db`
   - **User**: `postgres` (atau sesuai keinginan)
   - **Password**: (generate password yang kuat)
   - **Database Name**: `kajian_walsan`
4. Klik **Continue**.
5. Setelah terbuat, buka tab **Settings** pada database tersebut dan centang **Make it public** (hanya jika kamu ingin mengimpor database dari PC lokal menggunakan DBeaver/pgAdmin).
6. Catat **Internal Connection String** untuk digunakan di aplikasi.

---

## 3. Impor Database (Opsional tapi Direkomendasikan)

Jika kamu ingin mengimpor data awal (admin, roles, settings) secara manual:
1. Gunakan file `database_postgres.sql` yang sudah saya buatkan.
2. Di Coolify, kamu bisa buka menu **Terminal** pada resource PostgreSQL atau gunakan tool seperti **DBeaver** (jika public access aktif).
3. Jalankan query dari file SQL tersebut ke database `kajian_walsan`.

---

## 4. Deploy Aplikasi Laravel

1. Di Coolify, klik **Resources** > **New** > **Application**.
2. Pilih **GitHub Repository**.
3. Connect-kan akun GitHub kamu dan pilih repo: `miqbalputra/kajian-walsan-gq`.
4. Pilih branch `main`.
5. Coolify akan mendeteksi **Dockerfile**. Pastikan pilih **Build Pack: Dockerfile**.

### Konfigurasi Environment Variables

Buka tab **Environment Variables** di Coolify dan masukkan nilai-nilai berikut:

```env
APP_NAME="Presensi Kajian Walsan"
APP_ENV=production
APP_KEY=base64:EGtvPn/CmtaGp3r+HXVZn5hgBoRwcSPd0DRAogX8vPg=
APP_DEBUG=false
APP_URL=https://tahsin.griyaquran.web.id

# DATABASE (Ambil dari detail PostgreSQL di Coolify)
DB_CONNECTION=pgsql
DB_HOST=postgresql
DB_PORT=5432
DB_DATABASE=kajian_walsan
DB_USERNAME=postgres
DB_PASSWORD=password_db_kamu

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# CLOUDINARY
CLOUDINARY_ENABLED=true
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_FOLDER=kajian-walsan
```

---

## 5. Domain & SSL

1. Pergi ke tab **Settings** pada aplikasi.
2. Masukkan domain kamu di kolom **FQDN**: `https://tahsin.griyaquran.web.id`.
3. Coolify akan otomatis mengurus sertifikat SSL (Let's Encrypt).
4. Klik **Deploy**.

---

## 6. Verifikasi & Troubleshooting

Aplikasi ini sudah dikonfigurasi untuk menjalankan `php artisan migrate --force` secara otomatis saat startup (lewat `entrypoint.sh`).

Jika kamu ingin menjalankan seeder (untuk data dummy/awal):
1. Buka tab **Terminal** pada aplikasi di Coolify.
2. Jalankan perintah:
   ```bash
   php artisan db:seed --force
   ```

---

> [!TIP]
> Pastikan port 80, 443, dan 8000 terbuka di Firewall VPS/Cloud Provider kamu.
