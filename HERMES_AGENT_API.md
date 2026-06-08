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
overview
attendances
attendance_detail
manual_attendance
update_proof
```

### 1. Cek keseluruhan data lewat satu endpoint

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

### 3. Lihat detail presensi lewat satu endpoint

```json
{
  "action": "attendance_detail",
  "attendance_id": 123
}
```

### 4. Input presensi manual lewat satu endpoint

```json
{
  "action": "manual_attendance",
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

### 5. Input foto catatan hasil kajian lewat satu endpoint

Gunakan multipart form ke endpoint yang sama:

```http
POST https://kajian.griyaquran.web.id/hermes-agent
```

Field:

```text
action=manual_attendance
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
  -F "action=manual_attendance" \
  -F "kajian_event_id=1" \
  -F "parent_id=10" \
  -F "status=hadir_online" \
  -F "notes=Catatan hasil kajian dari Hermes" \
  -F "proof_photo=@catatan.jpg"
```

Alternatif jika Hermes sudah punya URL foto:

```json
{
  "action": "manual_attendance",
  "kajian_event_id": 1,
  "parent_id": 10,
  "status": "hadir_online",
  "notes": "Catatan hasil kajian dari Hermes",
  "proof_url": "https://example.com/catatan.jpg"
}
```

### 6. Update foto/catatan presensi lewat satu endpoint

```text
action=update_proof
attendance_id=123
notes=Revisi catatan hasil kajian
proof_photo=@catatan-baru.jpg
```

Contoh multipart:

```bash
curl -X POST "https://kajian.griyaquran.web.id/hermes-agent" \
  -H "Accept: application/json" \
  -H "X-Hermes-Secret: <HERMES_AGENT_SECRET>" \
  -F "action=update_proof" \
  -F "attendance_id=123" \
  -F "notes=Revisi catatan hasil kajian" \
  -F "proof_photo=@catatan-baru.jpg"
```

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

Alternatif jika Hermes sudah punya URL foto:

```json
{
  "kajian_event_id": 1,
  "parent_id": 10,
  "status": "hadir_online",
  "notes": "Catatan hasil kajian dari Hermes",
  "proof_url": "https://example.com/catatan.jpg"
}
```

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
- Kalau kirim `proof_photo` atau `proof_url`, status validasi default menjadi `pending`.
- Tambahkan `run_ai_review=false` jika tidak ingin AI langsung mengecek foto.
- Endpoint berlaku untuk jalur Wali Santri dan Guru melalui parameter `audience`.
