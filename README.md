<p align="center">
  <img src="public/img/logo.png" width="150" alt="Griya Quran Logo" onerror="this.src='https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg'">
</p>

# 🕌 Aplikasi Presensi Kajian Wali Santri v2

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4e56a6?style=for-the-badge&logo=livewire&logoColor=white)](https://laravel-livewire.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![n8n](https://img.shields.io/badge/n8n-EA4B71?style=for-the-badge&logo=n8n&logoColor=white)](https://n8n.io/)
[![OpenAI](https://img.shields.io/badge/OpenAI-412991?style=for-the-badge&logo=openai&logoColor=white)](https://openai.com/)
[![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)](https://mariadb.org/)
[![Coolify](https://img.shields.io/badge/Coolify-2C303E?style=for-the-badge&logo=linux&logoColor=white)](https://coolify.io/)

**Kajian Walsan v2** adalah ekosistem aplikasi berbasis *web* interaktif rancangan khusus yang dikembangkan untuk manajemen administrasi dan presensi (kehadiran) Wali Santri di Griya Qur'an Tunas Ilmu. 

Aplikasi ini mendigitalisasi proses verifikasi kehadiran dengan integrasi geolokasi (GPS), kode QR, integrasi Google Workspace (OAuth), hingga *Automated AI Chatbot* untuk bantuan pengguna tingkat lanjut.

---

## 🌟 Fitur Unggulan

- 📍 **Presensi Geolocation & QR Code** - Pencatatan kehadiran digital secara mutakhir dan akurat.
- 🔐 **Google OAuth Login** - Eksekusi Single Sign-On (SSO) agar Wali Santri dapat memadukan akun belajar.id / Gmail.
- 📱 **PWA (Progressive Web App)** - Bisa dipasang *langsung* ke layar *Home* HP (Android & iOS).
- 🔑 **AI Password Recovery Assistant** - Solusi lupa *password* otomatis 24 Jam dibantu oleh AI (*ditenagai oleh n8n*).
- 🏷️ **Role-Based Access (Multi-Peran)** - Hierarki otorisasi ketat (Super Admin, Admin, Guru Kelas, dan Wali Santri).
- ⚡ **SPA-like Experience** - Navigasi halus secepat kilat dengan *Livewire Navigation* / *Blaze*.

---

## 🤖 Automasi N8N & AI Chatbot

Aplikasi ini dilengkapi dengan asisten pintar pendamping *login* yang sangat canggih yang dirangkai mandiri dalam jaringan **n8n**. Fitur utamanya meliputi:

1. **Pemulihan Akun Mandiri:** Mengidentifikasi secara cerdas dan mereset profil wali santri hanya dengan memberikan instruksi berbasis NIK (16 angka) dan NIS Anak (10 Angka).
2. **LangChain & GPT-4o-mini:** Chatbot ditenagai mesin *Large Language Model* super efisien dari OpenAI (`Temperature: 0`) agar AI berfokus pada logika perhitungan matematis dan ekstraksi pola, menghilangkan misinformasi/basa-basi.
3. **Advanced Safeguard / Guardrails:** Filter keamanan aktif yang langsung mencekal kata-kata kasar/makian (dengan *threshold* pelindung spesifik), membuat UI kembali otomatis menasehati pengguna.
4. **Context Window Memory:** AI tidak akan amnesia. Jika Wali Santri memisah chat NIK dan NIS, Bot dapat menyatukan kepingan 3-5 kalimat log terakhir secara cerdas.
5. **Real-time Master Logging:** Setiap *log* obrolan (sukses, gagal, maupun serangan *prompt*) tertulis secara rahasia dan *asynchronous* ke dalam **Google Sheets** staf internal seketika (*0 delay* pada UI).

> File *Blueprint* dari kecerdasan buatan N8N ini terbuka untuk dipelajari di root project dengan nama: `final-n8n-workflow-reset.json`.

---

## 🛠️ Stack Teknologi

- **Framework:** Laravel 11 (PHP 8.2+)
- **Frontend / Styling:** Livewire 3 + Alpine.js + Tailwind CSS + Glassmorphism UI
- **Database Utama:** MariaDB / PostgreSQL
- **Orkestrasi Otomasi:** N8N (Self-Hosted)
- **Deployment & VPS:** Docker Image & Coolify Control Panel (Domain: `kajian.griyaquran.web.id`)

---

## 🚀 Panduan Instalasi Lokal

Untuk melangsungkan pengembangan di komputer (*Localhost*):

1. **Clone repositori**
   ```bash
   git clone https://github.com/miqbalputra/kajian-walsan-gq.git
   cd kajian-walsan-gq
   ```

2. **Instal seluruh Dependensi (PHP & Node)**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Konfigurasi Environment**
   Salin `.env.example` ke `.env` kemudian sesuaikan konfigurasi *Database* lokal Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database Utama**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi siap berlayar di `http://localhost:8000`.

---

## 💡 Keamanan dan Penjagaan Kualitas
Semua akses jalur belakang, API pemulihan akun dari N8N, token Google OAuth, dan sandi telah dirancang terenkripsi. Silakan tinjau `routes/web.php` untuk validasi Endpoint Rahasia, dan letakkan kode rahasia N8N hanya di berkas `.env` perangkat produksi!

<br>
<p align="center">
  <i>Dikembangkan dengan sepenuh hati ❤️ untuk Griya Qur'an Tunas Ilmu</i>
</p>
