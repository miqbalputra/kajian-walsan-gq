# Panduan Deploy - App Kajian Walsan v2 VPS

Panduan deployment kini telah diperbarui untuk menggunakan **Coolify** dan **PostgreSQL Lokal di VPS**.

---

## 🚀 Panduan Baru

Untuk panduan instalasi lengkap, silakan buka file: 
👉 **[COOLIFY_GUIDE.md](./COOLIFY_GUIDE.md)**

---

## 🗄️ Database PostgreSQL

Jika Anda memerlukan file SQL untuk diimpor secara manual ke database PostgreSQL di VPS, gunakan file:
👉 **[database_postgres.sql](./database_postgres.sql)**

File ini berisi:
1. Seluruh struktur tabel (Schema)
2. Roles default
3. Akun Admin (Email: `admin@kajianwalsan.com`, Pass: `password`)
4. Pengaturan default aplikasi

---

## ⚡ Ringkasan Cepat Coolify

1. **Instal Coolify**: `curl -fsSL https://get.coollabs.io/coolify/install.sh | bash`
2. **Setup Resource**: Buat `PostgreSQL` dan `GitHub Application`.
3. **Environment Variables**: Sesuaikan `DB_HOST`, `DB_PASSWORD`, dll di dashboard Coolify.
4. **Deploy**: Biarkan Dockerfile menangani build dan auto-migration.

---

*Panduan sebelumnya (Dokploy + Supabase) sudah dianggap usang dan digantikan dengan metode self-hosted yang lebih stabil di VPS.*
