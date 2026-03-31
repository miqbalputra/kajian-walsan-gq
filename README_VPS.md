# Panduan Deploy - App Kajian Walsan v2 VPS

Deploy ke VPS menggunakan **Dokploy** + **GitHub** + **Supabase** (PostgreSQL Free Tier).

---

## Prasyarat

- VPS dengan Dokploy terinstall
- Akun GitHub
- Akun Supabase (free tier)

---

## Langkah 1: Setup Supabase

1. Buka [supabase.com](https://supabase.com) dan buat project baru
2. Setelah project dibuat, pergi ke **Project Settings > Database**
3. Catat informasi berikut:
   - **Host**: `db.hieztlmtxmzjmryuipmm.supabase.co`
   - **Database name**: `postgres`
   - **User**: `postgres`
   - **Password**: (password yang kamu set saat buat project)
   - **Port**: `5432`

---

## Langkah 2: Push ke GitHub

```bash
# Di folder App Kajian Walsan v2 VPS
git init
git add .
git commit -m "Initial commit - VPS deployment"
git branch -M main
git remote add origin https://github.com/USERNAME/REPO_NAME.git
git push -u origin main
```

---

## Langkah 3: Setup di Dokploy

1. Login ke dashboard Dokploy VPS kamu
2. Buat **New Application**
3. Pilih **Docker** sebagai deployment method
4. Connect ke GitHub repository ini
5. Set **Branch**: `main`
6. Set **Dockerfile path**: `Dockerfile`

### Environment Variables di Dokploy

Masukkan satu per satu di `Environment Variables` Dokploy:

```
APP_NAME=Presensi Kajian Walsan
APP_ENV=production
APP_KEY=           <- KOSONGKAN, akan di-generate otomatis
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=db.hieztlmtxmzjmryuipmm.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD="Alhamdulillah`123"
DB_SSLMODE=require

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local

CLOUDINARY_ENABLED=true
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_FOLDER=kajian-walsan

ADMIN_WHATSAPP=6281234567890
```

6. Klik **Deploy**

---

## Langkah 4: Setup Domain (opsional)

1. Di Dokploy, pergi ke **Domains**
2. Tambahkan domain kamu
3. Enable **HTTPS / Let's Encrypt**
4. Update `APP_URL` di environment variables

---

## Catatan Penting

### Supabase Free Tier Limits
- Database size: 500MB
- Bandwidth: 5GB/bulan
- Project akan **pause otomatis** jika tidak aktif 1 minggu

### Agar Supabase Tidak Pause
Gunakan cron job atau layanan seperti [UptimeRobot](https://uptimerobot.com) untuk ping endpoint setiap hari.

### Storage File (Bukti Hadpal/Foto)
Aplikasi ini sudah menggunakan **Cloudinary** untuk penyimpanan file. Kamu tetap perlu mengisi kredensial Cloudinary di environment variables.

### Auto-deploy dari GitHub
Setiap kali kamu `git push`, Dokploy akan otomatis rebuild dan redeploy aplikasi.

---

## Troubleshooting

### Migration gagal
```bash
# Masuk ke container di Dokploy terminal
php artisan migrate --force
```

### Cache bermasalah
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Cek logs
Di Dokploy dashboard > Logs

