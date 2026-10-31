# Persetujuan foto kajian online dan surat izin melalui n8n

Workflow: https://auto.griyaquran.web.id/workflow/ZHncw01FO27caDT1

Bot: **GQ Admin Bot**. Tujuan dan reviewer: **1024607302**.

## Alur

1. Wali mengunggah catatan kajian online atau foto surat izin. Status tetap `pending`.
2. Observer aplikasi membuat satu permintaan review untuk bukti tersebut. Job mengirim payload ke webhook n8n.
3. n8n mengirim foto dengan nama wali, nama anak beserta kelas, ID presensi, judul/tanggal kajian, dan keterangan. Tombolnya **Setujui** / **Tolak**.
4. ID pesan Telegram dikembalikan ke aplikasi. Respons webhook kosong tidak dianggap sebagai pengiriman berhasil.
5. Telegram Trigger meneruskan klik admin ke endpoint tetap aplikasi. Backend memeriksa secret, akun admin aktif yang terhubung, chat tujuan, token permintaan, status pending, dan kesesuaian bukti.
6. Setujui langsung mencatat keputusan. Tolak menampilkan pilihan foto buram / tidak terbaca / bukti tidak sesuai. Keputusan memperbarui status presensi dan notifikasi wali; caption diperbarui dan tombol dinonaktifkan.

Pengiriman foto dan penerimaan klik dikelola n8n. Validasi keputusan, audit, notifikasi wali, jawaban callback, dan pembaruan caption tetap menggunakan layanan aplikasi. Token bot di aplikasi harus sama dengan credential Telegram n8n.

## Dua credential yang perlu diisi di n8n

MCP dapat membuat workflow dan menggunakan credential Telegram yang tersedia, tetapi tidak menyediakan alat untuk mengisi credential secret baru. Workflow tersimpan sebagai draft sampai konfigurasi selesai.

- **Webhook Bukti Kajian**: pilih/buat credential **Header Auth**, nama `Kajian Review - Header Webhook`. Header Name `X-Kajian-Review-Secret`; Value sama dengan `TELEGRAM_REVIEW_N8N_SECRET` di VPS.
- **Simpan Keputusan di Aplikasi**: buat credential **Simplified Custom Auth** (tipe internal `httpTemplatedCustomAuth`), nama `Kajian Review - Header Callback`. Gunakan template berikut. Pada pengaturan field `api_key`, isi Label dengan `Secret Callback`, pilih Secret, kemudian isi nilai credential dengan `TELEGRAM_REVIEW_WEBHOOK_SECRET` di VPS:

```json
{"headers":{"X-Telegram-Bot-Api-Secret-Token":"{{api_key}}"}}
```

Simpan kedua secret yang berbeda sebagai nilai acak panjang. Isi melalui credential n8n dan environment Coolify, bukan parameter node, Git, atau pesan chat.

## Konfigurasi aplikasi di Coolify

Deploy perubahan layanan `TelegramReviewService` dan `config/services.php`, kemudian isi environment:

```dotenv
TELEGRAM_REVIEW_ENABLED=true
TELEGRAM_REVIEW_DELIVERY=n8n
TELEGRAM_REVIEW_BOT_TOKEN=<token yang sama dengan GQ Admin Bot>
TELEGRAM_REVIEW_CHAT_ID=1024607302
TELEGRAM_REVIEW_WEBHOOK_SECRET=<secret credential Header Callback>
TELEGRAM_REVIEW_N8N_URL=https://auto.griyaquran.web.id/webhook/kajian-walsan-review
TELEGRAM_REVIEW_N8N_SECRET=<secret credential Header Webhook>
```

Gunakan URL **production**, bukan `/webhook-test/`. Endpoint callback yang benar adalah `https://kajian.griyaquran.web.id/telegram/review/webhook` (tanpa `/api`, karena aplikasi menetapkan `apiPrefix: ''`).

Pada **Manajemen User**, hubungkan `telegram_user_id=1024607302` ke akun **admin aktif** milik reviewer. Buka bot di Telegram dan tekan **Start** terlebih dahulu. ID Telegram saja tidak memberi hak persetujuan jika tidak terhubung ke akun admin aktif.

Setelah credential dan aplikasi siap, **Publish** workflow di n8n. Satu bot Telegram hanya boleh memiliki satu webhook aktif. Jangan jalankan `php artisan telegram:review-webhook` saat mode n8n: perintah itu diblokir untuk mencegah Telegram Trigger tertimpa. Bot Revisi GeoPresensi memakai credential berbeda dan tidak diubah.

Queue worker aplikasi harus berjalan untuk job pengiriman dan sinkronisasi caption. Setelah perubahan environment, refresh cache konfigurasi dan restart worker sesuai proses deploy yang biasa digunakan.

## Verifikasi setelah aktivasi

Gunakan satu pengajuan contoh yang jelas ditandai uji. Pastikan foto dan identitas masuk ke admin terpilih, status tetap pending, Setujui memperbarui presensi, Tolak meminta alasan, unggah ulang menonaktifkan bukti lama, dan akun Telegram lain tidak dapat mengambil keputusan.

Validasi workflow melalui MCP dan pengujian HTTP tiruan aplikasi tidak mengirim foto atau keputusan ke Telegram sungguhan. Pengujian produksi dilakukan setelah credential terpasang.

Tidak ada migrasi database baru, perubahan data lama, atau impor bukti lama otomatis. Mode awal tetap `direct`; ubah ke `n8n` setelah workflow siap. Riwayat keputusan tetap tersimpan di tabel aplikasi yang sudah ada. Draft workflow dikonfigurasi tidak menyimpan payload eksekusi sukses maupun gagal untuk membatasi salinan foto dan identitas di n8n.

Salinan workflow untuk pemulihan/impor: `n8n/kajian-telegram-review.workflow.json`. Sumber SDK: `n8n/kajian-telegram-review.workflow.ts`. Setelah impor, pilih kembali credential dan periksa dua secret; salinan file tidak memuat nilainya.
