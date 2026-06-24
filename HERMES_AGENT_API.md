# Hermes Agent API

Base URL:

```text
https://kajian.griyaquran.web.id
```

API key:

```text
<HERMES_AGENT_SECRET>
```

Kirim API key di setiap request:

```http
X-Hermes-Secret: <HERMES_AGENT_SECRET>
Accept: application/json
```

## Satu Endpoint Utama

Hermes cukup konek ke satu endpoint ini:

```http
POST https://kajian.griyaquran.web.id/hermes-agent
```

Kirim field `action` untuk memilih jalur:

```text
ping
overview
attendances
attendance_detail
read_attendance
create_attendance
manual_attendance
update_attendance
update_proof
delete_attendance
restore_attendance
```

Action dari Hermes juga boleh memakai beberapa alias umum:

```text
status/check/health -> ping
data/dashboard/summary -> overview
attendance/presence/presensi/get-presence/rekap -> attendances
mark-presence/input-presensi -> create_attendance
edit-presence/update-presence -> update_attendance
delete-presence/hapus-presensi -> delete_attendance
restore-presence/undo-delete -> restore_attendance
```

### 1. Cek keseluruhan data lewat satu endpoint

Tes koneksi paling ringan:

```json
{
  "action": "ping"
}
```

```json
{
  "action": "overview",
  "event_limit": 10
}
```

Contoh:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -d "{\"action\":\"overview\",\"event_limit\":10}"
```

### 2. Lihat presensi lengkap lewat satu endpoint

```json
{
  "action": "attendances",
  "complete": true,
  "audience": "guru"
}
```

Contoh:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -d "{\"action\":\"attendances\",\"complete\":true,\"audience\":\"guru\"}"
```

Untuk melihat presensi yang sudah dihapus soft delete:

```json
{
  "action": "attendances",
  "trashed": "only"
}
```

Nilai `trashed`:

```text
without = default, hanya data aktif
with = data aktif dan terhapus
only = hanya data terhapus
```

### 3. Read: lihat detail presensi lewat satu endpoint

```json
{
  "action": "read_attendance",
  "attendance_id": 123
}
```

`attendance_detail` masih didukung sebagai alias lama untuk `read_attendance`.

### 4. Create: input presensi baru lewat satu endpoint

```json
{
  "action": "create_attendance",
  "kajian_event_id": 1,
  "parent_id": 10,
  "status": "hadir_fisik",
  "notes": "Input manual dari Hermes",
  "validation_status": "approved"
}
```

Target peserta bisa pakai salah satu:

```text
parent_id
user_id
qr_code
```

Status yang didukung:

```text
hadir_fisik
hadir_online
izin
alpha
```

Jika presensi untuk `kajian_event_id` dan `parent_id` tersebut sudah ada, `create_attendance` akan menolak dengan HTTP 409. Gunakan `update_attendance` untuk mengubah data yang sudah ada. Race condition (dua request bersamaan) juga akan ditangani dengan 409.

`manual_attendance` masih didukung sebagai mode upsert/kompatibilitas lama.

### 5. Create dengan foto catatan hasil kajian lewat satu endpoint

Gunakan multipart form ke endpoint yang sama:

```http
POST https://kajian.griyaquran.web.id/hermes-agent
```

Field:

```text
action=create_attendance
kajian_event_id=1
parent_id=10
status=hadir_online
notes=Catatan hasil kajian dari Hermes
proof_photo=@catatan.jpg
```

Contoh:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -F "action=create_attendance" \
  -F "kajian_event_id=1" \
  -F "parent_id=10" \
  -F "status=hadir_online" \
  -F "notes=Catatan hasil kajian dari Hermes" \
  -F "proof_photo=@catatan.jpg"
```

Alternatif jika Hermes sudah punya URL foto dari Cloudinary:

```json
{
  "action": "create_attendance",
  "kajian_event_id": 1,
  "parent_id": 10,
  "status": "hadir_online",
  "notes": "Catatan hasil kajian dari Hermes",
  "proof_url": "https://res.cloudinary.com/dt7ovpkbr/image/upload/v123/kajian-walsan/attendance-proofs/catatan.jpg"
}
```

> **Penting:** `proof_url` hanya menerima URL dari domain `res.cloudinary.com`. URL dari domain lain akan ditolak dengan HTTP 422.

### 6. Update: ubah status/foto/catatan presensi lewat satu endpoint

Body JSON:

```json
{
  "action": "update_attendance",
  "attendance_id": 123,
  "status": "izin",
  "notes": "Izin karena ada keperluan keluarga",
  "proof_url": "https://res.cloudinary.com/dt7ovpkbr/image/upload/v123/kajian-walsan/izin-documents/surat-izin.jpg"
}
```

Field multipart:

```text
action=update_attendance
attendance_id=123
notes=Revisi catatan hasil kajian
proof_photo=@catatan-baru.jpg
```

Contoh multipart:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -F "action=update_attendance" \
  -F "attendance_id=123" \
  -F "notes=Revisi catatan hasil kajian" \
  -F "proof_photo=@catatan-baru.jpg"
```

`update_proof` masih didukung sebagai alias lama untuk `update_attendance`.

### 7. Delete: hapus presensi lewat satu endpoint

```json
{
  "action": "delete_attendance",
  "attendance_id": 123
}
```

Penghapusan memakai soft delete sesuai model `Attendance`, lalu cache jumlah presensi kajian diperbarui.

### 8. Restore: kembalikan presensi yang salah hapus

```json
{
  "action": "restore_attendance",
  "attendance_id": 123
}
```

Restore akan ditolak jika sudah ada presensi aktif lain untuk peserta dan kajian yang sama.

### Aturan Presensi Yang Dijaga API

- Wali Santri `hadir_fisik`: boleh tanpa file, seperti presensi scan/manual panitia.
- Wali Santri `hadir_online`: wajib ada `proof_photo` atau `proof_url` catatan hasil kajian.
- Wali Santri `izin`: wajib ada `proof_photo` atau `proof_url`, dan wajib ada `notes` alasan izin.
- Guru `hadir_fisik`: wajib ada `proof_photo` atau `proof_url` catatan hasil kajian.
- Guru `hadir_online`: wajib ada `proof_photo` atau `proof_url` catatan hasil kajian.
- Guru `izin`: wajib ada `proof_photo` atau `proof_url`, dan wajib ada `notes` alasan izin.
- Jika kirim file/URL bukti, validasi default menjadi `pending` kecuali Hermes mengirim `validation_status`.
- `proof_url` hanya menerima URL Cloudinary (`res.cloudinary.com`). URL lain ditolak dengan HTTP 422.
- Gunakan `clear_proof=true` hanya jika status akhirnya tidak melanggar aturan wajib file.
- `delete_attendance` adalah soft delete, jadi masih bisa dikembalikan dengan `restore_attendance`.
- Hermes tidak diberi akses hapus permanen.

## Endpoint Cadangan

### 1. Cek keseluruhan data

```http
GET https://kajian.griyaquran.web.id/hermes-agent/overview
```

Query opsional:

```text
audience=wali_santri|guru
event_limit=10
```

Contoh:

```bash
curl -X GET "https://kajian.griyaquran.web.id/hermes-agent/overview?event_limit=10" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>"
```

### 2. Lihat presensi lengkap

```http
GET https://kajian.griyaquran.web.id/hermes-agent/attendances?complete=1
```

Query opsional:

```text
kajian_event_id=1
audience=wali_santri|guru
status=hadir_fisik|hadir_online|izin|alpha
validation_status=approved|pending|rejected
search=nama
```

Contoh presensi lengkap guru:

```bash
curl -X GET "https://kajian.griyaquran.web.id/hermes-agent/attendances?complete=1&audience=guru" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>"
```

### 3. Lihat record presensi yang sudah tersimpan

```http
GET https://kajian.griyaquran.web.id/hermes-agent/attendances
```

Query opsional:

```text
kajian_event_id=1
audience=wali_santri|guru
status=hadir_fisik|hadir_online|izin|alpha
validation_status=approved|pending|rejected
search=nama
per_page=50
```

### 4. Lihat detail satu presensi

```http
GET https://kajian.griyaquran.web.id/hermes-agent/attendances/{attendance_id}
```

Contoh:

```bash
curl -X GET "https://kajian.griyaquran.web.id/hermes-agent/attendances/123" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>"
```

### 5. Input presensi manual tertentu

```http
POST https://kajian.griyaquran.web.id/hermes-agent/attendances/manual
```

Body JSON minimal:

```json
{
  "kajian_event_id": 1,
  "parent_id": 10,
  "status": "hadir_fisik",
  "notes": "Input manual dari Hermes",
  "validation_status": "approved"
}
```

Target peserta bisa pakai salah satu:

```text
parent_id
user_id
qr_code
```

Status yang didukung:

```text
hadir_fisik
hadir_online
izin
alpha
```

Jika `kajian_event_id` tidak dikirim, sistem memakai kajian yang sedang dibuka.

Contoh:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent/attendances/manual" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -d "{\"kajian_event_id\":1,\"parent_id\":10,\"status\":\"hadir_fisik\",\"notes\":\"Input manual dari Hermes\",\"validation_status\":\"approved\"}"
```

### 6. Input foto catatan hasil kajian saat membuat presensi

Gunakan multipart form:

```http
POST https://kajian.griyaquran.web.id/hermes-agent/attendances/manual
```

Field:

```text
kajian_event_id=1
parent_id=10
status=hadir_online
notes=Catatan hasil kajian dari Hermes
proof_photo=@catatan.jpg
```

Contoh:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent/attendances/manual" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -F "kajian_event_id=1" \
  -F "parent_id=10" \
  -F "status=hadir_online" \
  -F "notes=Catatan hasil kajian dari Hermes" \
  -F "proof_photo=@catatan.jpg"
```

Alternatif jika Hermes sudah punya URL foto dari Cloudinary:

```json
{
  "kajian_event_id": 1,
  "parent_id": 10,
  "status": "hadir_online",
  "notes": "Catatan hasil kajian dari Hermes",
  "proof_url": "https://res.cloudinary.com/dt7ovpkbr/image/upload/v123/kajian-walsan/attendance-proofs/catatan.jpg"
}
```

> **Penting:** `proof_url` hanya menerima URL dari domain `res.cloudinary.com`.

### 7. Update foto/catatan presensi yang sudah ada

```http
POST https://kajian.griyaquran.web.id/hermes-agent/attendances/{attendance_id}/proof
```

Contoh multipart:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent/attendances/123/proof" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -F "notes=Revisi catatan hasil kajian" \
  -F "proof_photo=@catatan-baru.jpg"
```

## Catatan Integrasi

- File foto harus JPG/JPEG/PNG.
- Ukuran maksimal upload API Hermes: 4 MB.
- `proof_url` hanya menerima URL Cloudinary (`res.cloudinary.com`). URL dari domain lain akan ditolak dengan HTTP 422 dan pesan: `proof_url harus berupa URL Cloudinary yang valid (res.cloudinary.com).`
- Kalau kirim `proof_photo` atau `proof_url`, status validasi default menjadi `pending`.
- Tambahkan `run_ai_review=false` jika tidak ingin AI langsung mengecek foto.
- AI review berjalan **asynchronous** di queue worker — response API langsung selesai tanpa menunggu AI. Hasil review akan muncul di field `ai_review` pada response detail presensi setelah queue worker selesai memproses (biasanya 5-30 detik).
- Jika dua request `create_attendance` dikirim bersamaan untuk peserta yang sama, salah satu akan dapat HTTP 409 dengan data presensi yang sudah tersimpan.
- Endpoint berlaku untuk jalur Wali Santri dan Guru melalui parameter `audience`.