# n8n Email Reminder Kajian

Aplikasi mengirim pengingat email melalui webhook n8n pada pukul 20.30 WIB
untuk wali sasaran kajian yang belum memiliki record presensi.

## Konfigurasi aplikasi

Tambahkan nilai berikut pada environment production. Secret harus sama dengan
credential/header authentication yang dipakai pada Webhook node n8n.

```dotenv
N8N_EMAIL_WEBHOOK_ENABLED=true
N8N_EMAIL_WEBHOOK_URL=https://n8n.example.com/webhook/kajian-email-reminder
N8N_EMAIL_WEBHOOK_SECRET=isi-secret-random-yang-panjang
```

Setelah mengubah environment pada deployment, refresh konfigurasi Laravel
sesuai prosedur deployment yang berlaku.

## Workflow n8n

Buat workflow dengan urutan berikut:

1. **Webhook**
   - Method: `POST`
   - Path: sesuai nilai `N8N_EMAIL_WEBHOOK_URL`
   - Authentication: Header Auth dengan nama header `X-N8N-Webhook-Secret`
   - Nilai header: sama dengan `N8N_EMAIL_WEBHOOK_SECRET`
   - Terima JSON request body.
2. **Split Out**
   - Pecah array `body.recipients` menjadi satu item per penerima.
3. **Send Email**
   - Gunakan credential SMTP yang dikelola di n8n.
   - To: `{{$json.email}}`
   - Subject: `{{$json.subject}}` atau subject dari item webhook yang diteruskan.
   - Text/HTML: gunakan field `body.text` dan `body.html` dari payload webhook.
4. **Response**
   - Kembalikan respons HTTP `2xx` setelah workflow menerima dan memproses
     payload.

Payload yang dikirim aplikasi berbentuk seperti berikut:

```json
{
  "channel": "email",
  "notification_type": "email_missing_attendance_2030",
  "dispatch_id": 123,
  "idempotency_key": "kajian-email:123",
  "event": {
    "id": 45,
    "title": "Kajian Wali Santri",
    "date": "2026-09-14",
    "time_start": "18:00",
    "time_end": "19:00"
  },
  "subject": "Pengingat - Anda Belum Presensi Kajian Wali Santri",
  "text": "...",
  "html": "...",
  "login_url": "https://kajian.griyaquran.web.id/login",
  "recipients": [
    {
      "user_id": 10,
      "name": "Nama Wali",
      "email": "wali@example.com"
    }
  ]
}
```

`dispatch_id`/`idempotency_key` harus diteruskan dalam workflow. Jika workflow
memiliki retry setelah email sebagian terkirim, gunakan pasangan
`idempotency_key + recipients.user_id` sebagai kunci deduplikasi agar email
penerima yang sudah berhasil tidak dikirim ulang.
