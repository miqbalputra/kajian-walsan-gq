# Audit, Refactoring, Security Hardening & Feature Verification

**Aplikasi:** App Kajian Walsan v2 VPS
**Tanggal audit:** 2026-09-09
**Peran audit:** Lead Full-Stack Architect, Cybersecurity Specialist, Senior UI/UX Designer
**Stack terdeteksi:** Laravel 12.62.0, PHP 8.4.21, Livewire 3.7, Fortify 1.33, Tailwind 4, Vite 7.3.0, MariaDB production target, SQLite test database.

## 1. Ringkasan Eksekutif & Health Check

Konsep produk dan layout utama dipertahankan. Pekerjaan difokuskan pada hardening security, validasi input, session/OAuth, kejelasan feedback UI, agregasi query, index operasional, dan regression testing.

### Status akhir

| Area | Status | Bukti / catatan |
|---|---|---|
| Keamanan aplikasi | **PASS untuk kontrol yang diaudit** | 54 test lulus; secret Hermes body ditolak; security headers diverifikasi; upload dan OAuth diperketat. |
| Fungsionalitas otomatis | **PASS** | `php artisan test --no-coverage`: 54 passed, 266 assertions, 26.79 detik. |
| Build/deployability | **PASS** | `composer validate --no-check-publish` lulus; `npm run build` lulus; 93 modul Vite ditransformasi. |
| Routing/RBAC surface | **PASS statis** | `php artisan route:list` lulus, 89 route terdaftar; group `auth` dan role tetap dipertahankan. |
| Query/performa | **PASS terarah; benchmark produksi CONDITIONAL** | Agregasi SurveyAnalysis dan index baru diterapkan. EXPLAIN produksi belum dapat diambil dari environment lokal karena driver `pdo_mysql` tidak terpasang. |
| UI/UX | **PASS polish terarah** | Konsep visual tidak dirombak; fokus state submit, focus-visible, reduced motion, touch behavior, spacing/feedback yang sudah ada. |
| Production readiness | **CONDITIONAL** | Wajib set `APP_DEBUG=false`, `TRUSTED_PROXIES` ke IP proxy nyata, secret production, HTTPS, dan menjalankan migration pada database production. |

### Rencana perbaikan berjenjang

- **P0 sebelum production:** set environment production yang aman, apply migration index, rotasi semua secret, pastikan HTTPS/proxy whitelist, aktifkan queue/worker yang benar, dan jalankan release gate.
- **P1 pada staging:** smoke test Google/Cloudinary/Telegram/Data Induk, browser QA seluruh role dan breakpoint, EXPLAIN query MariaDB, serta uji rollback migration.
- **P2 hardening lanjutan:** pindahkan bukti ke private object storage/signed delivery, tambahkan CSP nonce/hash, observability dashboard berbasis `X-Request-ID`, dan perluas feature tests untuk setiap CRUD Livewire.

### Batasan verifikasi

Suite otomatis memverifikasi seluruh test yang tersedia di repository dan regresi yang disentuh oleh patch. Verifikasi browser manual lintas seluruh resolusi, EXPLAIN query MariaDB production, dan uji provider eksternal Google/Cloudinary/Telegram tetap perlu dijalankan pada staging dengan credential non-production. Pernyataan “100% verified” di bawah berarti 100% dari suite otomatis yang tersedia, bukan klaim bahwa provider eksternal sudah dipanggil dari environment audit.

Perubahan pengguna yang sudah ada di working tree, termasuk Data Induk pilot, dipertahankan dan tidak di-reset.

### Verifikasi preservasi data lama

- Tidak dijalankan `migrate:fresh`, `db:wipe`, `truncate`, `delete` massal, atau reset database terhadap database aplikasi.
- Migration index baru hanya menambah index pada tabel `attendances`, `kajian_events`, `students`, dan `kajian_feedback`; bagian `down()` hanya menghapus index tersebut, bukan row/data.
- PHPUnit berjalan pada SQLite `:memory:` melalui `phpunit.xml` dan test yang memakai `RefreshDatabase` tidak menyentuh database production.
- `.env` aplikasi tidak diubah. Karena environment lokal tidak memiliki driver `pdo_mysql`, migration production juga tidak pernah dijalankan dari sesi audit ini.
- Perubahan existing working tree milik user tetap dipertahankan.

Kesimpulan: tidak ada indikasi data lama hilang akibat pekerjaan audit ini. Sebelum deployment tetap lakukan backup database dan verifikasi row-count/backup restore pada staging.

## 2. Catatan Optimasi UI/UX

### Prinsip yang dipertahankan

- Tema, navigasi, struktur dashboard, dan layout inti tidak dirombak.
- Tidak ada penggantian komponen besar atau migrasi design system yang berisiko memutus alur kerja admin/panitia/wali.
- Perubahan diarahkan pada micro-interaction, keterbacaan, aksesibilitas, dan state feedback.

### Polish yang diterapkan

1. Form publik Mustawa 1 sekarang memiliki guard submit satu kali, tombol disabled selama request, spinner, dan teks `Mengirim...` sehingga user tidak membuat duplikasi karena double-click.
2. `resources/css/app.css` menambahkan `:focus-visible` yang konsisten, `touch-action: manipulation`, dan dukungan `prefers-reduced-motion`.
3. Upload bukti pada jalur publik, Livewire wali, admin parent/archive, dan Hermes menerima tipe file terkontrol serta pesan validasi yang lebih konsisten.
4. Error server JSON sekarang memiliki `request_id`, sehingga user mendapatkan feedback aman sedangkan administrator dapat mencari korelasi error pada centralized log.
5. Feedback gagal pada Chat AI tidak lagi menampilkan exception mentah ke layar admin; exception dilaporkan ke log dan user menerima pesan yang dapat ditindaklanjuti.
6. State empty/loading/toast yang sudah ada dipertahankan; patch tidak mengubah konsep warna atau layout utama.

### Rekomendasi polish lanjutan saat browser QA

- Pastikan setiap tabel besar mempertahankan empty state, skeleton/loading state, dan reset filter yang terlihat pada viewport mobile.
- Uji keyboard-only untuk modal QR, upload, validation, dan feedback; fokus harus kembali ke trigger setelah modal ditutup.
- Uji breakpoint 320px, 768px, 1024px, dan desktop lebar; khususnya tabel admin, scanner, kartu QR, dan modal upload.
- Uji toast/error untuk koneksi lambat dan refresh Livewire agar tidak menutupi tombol utama.

## 3. Audit Keamanan & Daftar Patch Kerentanan

### Patch yang diterapkan

| Risiko | Temuan | Patch | Status |
|---|---|---|---|
| High | `trustProxies('*')` memungkinkan header forwarded spoofing | Proxy sekarang hanya dipercaya dari `TRUSTED_PROXIES`; default kosong | **Applied** |
| High | Secret Hermes dapat dikirim melalui body dan berpotensi masuk access log | Hanya `X-Hermes-Secret` atau `Authorization: Bearer`; body/query tidak diterima | **Applied + tested** |
| Medium | Endpoint Hermes tidak memiliki throttling khusus | Tambah `throttle:120,1` pada seluruh group Hermes | **Applied** |
| High | CSRF exception global untuk Hermes terlalu luas | Exception dihapus; endpoint memakai header secret dan tetap berada pada alur middleware yang aman | **Applied** |
| High | OAuth memakai `stateless()` dan custom encrypted state | Login/link Google kembali memakai session state Socialite; target link hanya `Auth::user()` | **Applied** |
| High | Session fixation pada login QR | Tambah `$request->session()->regenerate()` setelah login berhasil | **Applied** |
| High | Upload hanya mengandalkan extension/MIME Laravel yang longgar | `File::image()->types(['jpg','jpeg','png'])->max(...)`, MIME aktual, `getimagesize`, random UUID filename | **Applied** |
| Medium | Nama file asli dikirim ke Cloudinary | Nama upload provider diganti UUID + extension yang telah dinormalisasi | **Applied** |
| Medium | Response/error Cloudinary dan stack trace terlalu detail | Body response/trace tidak lagi dilog; exception dibatasi class dan pesan singkat | **Applied** |
| High | Feedback dapat memproses `feedbackEventId` tampered | Event di-resolve ulang berdasarkan attendance milik parent, status hadir, dan event closed/past | **Applied** |
| Medium | Password admin dapat fallback ke password literal `password` | Fallback dihapus; create/update membutuhkan password min. 12 karakter | **Applied** |
| Medium | Avatar profile dapat diisi string arbitrary | Icon dan warna divalidasi terhadap preset allowlist | **Applied** |
| Medium | Validation error dapat mem-flash secret/file/password ke session | `dontFlash` ditambah untuk credential, token, secret, dan field upload | **Applied** |
| Medium | API server error dapat memperlihatkan detail internal saat production | JSON generic message + `request_id` ketika `APP_DEBUG=false` | **Applied** |
| Medium | Header hardening tidak seragam | `SecurityHeaders` diterapkan pada web dan API: nosniff, frame, referrer, permissions, request ID, HSTS production HTTPS | **Applied + tested** |
| Low/Medium | Endpoint deployment membocorkan detail implementasi PWA | Response dipersempit menjadi version + timestamp | **Applied** |
| Medium | Session cookie production belum memiliki default aman | `SESSION_SECURE_COOKIE`, `HTTP_ONLY`, `SAME_SITE`, `PARTITIONED` ditambahkan ke `.env.example`; secure default mengikuti production | **Applied** |

### Kontrol yang tetap wajib di production

1. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, dan `SESSION_SECURE_COOKIE=true`.
2. Isi `TRUSTED_PROXIES` hanya dengan IP/CIDR reverse proxy yang benar. Jangan memakai `*`.
3. Gunakan secret kuat untuk Hermes, Google, Cloudinary, Telegram, queue, dan database; rotasi secret setelah deployment pertama.
4. Jalankan Cloudinary atau private object storage untuk bukti yang sensitif. Fallback local `public` tetap memakai nama acak dan validasi ketat, tetapi URL public bukan kontrol authorization.
5. CSP sengaja belum dipaksakan karena aplikasi masih memiliki inline script/CDN. Tahap berikutnya adalah migrasi ke nonce/hash lalu menambahkan `Content-Security-Policy` bertahap (`Report-Only` kemudian enforcement).
6. Pastikan TLS termination dan proxy mengirim forwarded headers hanya dari proxy yang sudah di-whitelist.

## 4. Optimasi Arsitektur, Query Database, dan Metrik Kinerja Backend

### Arsitektur dan clean code

- Boundary Data Induk tetap berada di `DataIndukClient` dan `DataIndukRosterSyncService`; mapping kelas pilot dibaca dari `DATA_INDUK_CLASS_MAP`, bukan hard-coded pada controller.
- Upload dipusatkan di `CloudinaryService`; semua caller melewati validasi file dan nama acak yang sama.
- Authorization feedback dipindahkan ke query server-side, bukan mempercayai property Livewire.
- Error internal dikirim ke report/log, sedangkan user menerima response stabil dan aman.
- Middleware security dipisahkan ke `SecurityHeaders`; konfigurasi proxy/session dieksternalisasi ke environment.

### Query dan index

`SurveyAnalysis` sebelumnya mengambil seluruh model feedback dengan `get()` lalu menghitung statistik di memory. Refactor sekarang:

- memakai `COUNT`/`AVG` aggregate untuk summary;
- hanya `pluck('extra_feedback')` untuk dua nilai JSON yang dibutuhkan;
- memakai grouped count untuk bucket rating;
- menerapkan filter yang sama pada trend query sehingga hasil trend tidak lagi mengabaikan filter tanggal.

Migration baru `database/migrations/2026_09_09_000001_add_operational_query_indexes.php` menambah index idempotent:

| Tabel | Index | Sasaran |
|---|---|---|
| `attendances` | `(parent_id, validation_status, updated_at)` | pending/rejected review dan history wali |
| `attendances` | `(parent_id, created_at)` | history/paginasi attendance |
| `kajian_events` | `(status, date, time_start)` | event open/upcoming dan ordering |
| `students` | `(class_id, is_active, student_status)` | target class dan roster |
| `kajian_feedback` | `(created_at)` | filter rentang tanggal survey |

Index existing dan eager loading yang telah dipakai aplikasi tidak dihapus. Query plan MariaDB tetap harus diukur dengan `EXPLAIN` setelah migration production diterapkan.

### Metrik verifikasi aktual

| Pemeriksaan | Hasil |
|---|---:|
| PHPUnit/Laravel test | **54 passed** |
| Assertion | **266** |
| Durasi test suite | **26.79 s** |
| PHP lint | **23 file passed** |
| Composer manifest | **valid** |
| Vite production build | **93 modules**, lulus dalam **3.40 s** |
| Output JS gzip terbesar | `panitia-scanner`: **100.10 kB** |
| Route registration | **89 routes**, lulus |

`php artisan optimize:clear` dicoba, tetapi pembersihan cache database lokal tidak dapat selesai karena environment audit tidak memiliki driver `pdo_mysql` sementara `.env` lokal menunjuk MariaDB. Ini bukan failure aplikasi production; jalankan perintah tersebut pada container/runtime production yang memiliki ekstensi database yang benar.

## 5. Kode Lengkap Hasil Refactoring/Optimasi

Seluruh implementasi tersimpan di working tree pada file-file di bawah. Block berikut memuat kode lengkap untuk artifact baru dan method inti security; file existing lainnya mencantumkan patch lengkap secara fungsional agar dapat direview tanpa menebak perubahan.

### 5.1 Middleware security headers dan correlation ID

File: `app/Http/Middleware/SecurityHeaders.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);

        $response = $next($request);
        $headers = [
            'X-Request-ID' => $requestId,
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(self), geolocation=(), microphone=()',
        ];

        if (app()->isProduction() && $request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000';
        }

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }
}
```

`bootstrap/app.php` mendaftarkan middleware ini pada web/API, menghapus CSRF bypass Hermes, membatasi proxy, dan menambahkan `dontFlash` serta generic JSON exception response.

### 5.2 Hermes header-only authentication

File: `app/Http/Middleware/VerifyHermesAgent.php`

```php
$configuredSecret = config('hermes.secret');
$providedSecret = $request->header('X-Hermes-Secret') ?: $request->bearerToken();

if (! is_string($providedSecret) || ! hash_equals($configuredSecret, $providedSecret)) {
    return response()->json([
        'success' => false,
        'message' => 'Unauthorized Hermes Agent.',
    ], 401);
}
```

Route group `routes/api.php` menambahkan `throttle:120,1`.

### 5.3 Upload validation dan random filename

File: `app/Services/CloudinaryService.php`

```php
private function assertSafeImage(UploadedFile $file): void
{
    if (! $file->isValid()) {
        throw new RuntimeException('File upload tidak valid.');
    }

    $allowedMimes = ['image/jpeg', 'image/png'];
    if (! in_array($file->getMimeType(), $allowedMimes, true)
        || @getimagesize($file->getRealPath()) === false) {
        throw new RuntimeException('File upload harus berupa gambar JPG atau PNG yang valid.');
    }
}

// Nama yang dikirim ke provider tidak pernah memakai nama client.
$extension = strtolower($file->extension() ?: 'jpg');
$response = Http::timeout(30)
    ->attach('file', file_get_contents($file->getRealPath()), Str::uuid()->toString().'.'.$extension)
    ->post($uploadUrl, $payload);
```

Caller juga memakai `File::image()->types(['jpg', 'jpeg', 'png'])->max(2048)` atau max 4096 untuk Hermes. Archive evidence memakai whitelist image/PDF dan max 5120.

### 5.4 Authorization feedback server-side

File: `app/Livewire/WaliSantri/Dashboard.php`

```php
$event = KajianEvent::query()
    ->whereKey((int) $this->feedbackEventId)
    ->whereHas('attendances', function ($query) {
        $query->where('parent_id', $this->parent?->id)
            ->whereIn('status', [
                Attendance::STATUS_HADIR_FISIK,
                Attendance::STATUS_HADIR_ONLINE,
                Attendance::STATUS_IZIN,
            ]);
    })
    ->where(function ($query) {
        $query->whereDate('date', '<', today())
            ->orWhere('status', 'closed');
    })
    ->first();

if (! $event) {
    $this->addError('feedbackEventId', 'Feedback hanya dapat dikirim untuk kajian yang Anda ikuti.');
    return;
}

KajianFeedback::updateOrCreate(
    ['kajian_event_id' => $event->id, 'user_id' => auth()->id()],
    [/* rating dan comment tervalidasi */]
);
```

### 5.5 Aggregate query SurveyAnalysis

File: `app/Livewire/Admin/SurveyAnalysis.php`

```php
$summary = (clone $query)
    ->selectRaw('COUNT(*) as total_responses, AVG(rating) as average_rating')
    ->first();

$totalResponses = (int) ($summary?->total_responses ?? 0);
$averageRating = $totalResponses > 0
    ? round((float) $summary->average_rating, 2)
    : 0;

foreach ((clone $query)->whereNotNull('extra_feedback')->pluck('extra_feedback') as $extraFeedback) {
    $extraFeedback = is_string($extraFeedback)
        ? json_decode($extraFeedback, true)
        : $extraFeedback;
    if (! is_array($extraFeedback)) {
        continue;
    }
    // Hitung hanya JSON field yang diperlukan.
}
```

### 5.6 Migration index operasional

File lengkap: `database/migrations/2026_09_09_000001_add_operational_query_indexes.php`.

```php
foreach ($indexes as $table => $tableIndexes) {
    foreach ($tableIndexes as $name => $columns) {
        if (! Schema::hasIndex($table, $name)) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
                $blueprint->index($columns, $name);
            });
        }
    }
}
```

Migration menggunakan `Schema::hasIndex` dan `down()` yang simetris agar aman dijalankan ulang/dirollback.

### 5.7 Regression security test

File: `tests/Feature/SecurityHardeningTest.php` mencakup:

```php
public function test_hermes_secret_is_not_accepted_from_request_body(): void
{
    config(['hermes.secret' => 'test-hermes-secret']);

    $this->postJson('/hermes-agent', [
        'secret' => 'test-hermes-secret',
        'action' => 'ping',
    ])->assertUnauthorized();
}
```

Test yang sama memverifikasi header `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `X-Request-ID`, dan keberhasilan Hermes dengan header resmi.

### 5.8 Inventaris file implementasi

| File | Perubahan |
|---|---|
| `bootstrap/app.php` | trusted proxy, middleware, CSRF, safe exception response |
| `config/app.php`, `config/session.php`, `config/hermes.php`, `config/data-induk.php` | environment/security defaults dan pilot mapping |
| `app/Http/Middleware/SecurityHeaders.php` | middleware baru |
| `app/Http/Middleware/VerifyHermesAgent.php`, `routes/api.php` | header authentication + throttle |
| `app/Http/Controllers/Auth/GoogleController.php` | stateful OAuth dan generic error |
| `app/Http/Controllers/Auth/QrLoginController.php` | session regeneration |
| `app/Http/Controllers/PublicMustawaOneAttendanceController.php`, `app/Http/Controllers/Api/HermesAgentController.php` | file rule ketat |
| `app/Services/CloudinaryService.php` | MIME/image signature, random name, safe logging |
| `app/Livewire/WaliSantri/Dashboard.php` | upload rules dan feedback authorization |
| `app/Livewire/WaliSantri/Profile.php` | avatar allowlist |
| `app/Livewire/Admin/UserIndex.php`, `ParentIndex.php`, `ArchiveIndex.php` | password/upload hardening |
| `app/Livewire/Admin/SurveyAnalysis.php` | aggregate query dan filter trend |
| `app/Livewire/Admin/ChatAi.php` | generic user-facing error |
| `resources/css/app.css`, view admin/public | UI polish dan submit feedback |
| `database/migrations/2026_09_09_000001_add_operational_query_indexes.php` | index query |
| `tests/Feature/SecurityHardeningTest.php` | regression security tests |
| `.env.example` | production security knobs |

## 6. Checklist Verifikasi & Testing Plan

### Automated verification yang sudah dijalankan

| No. | Area / skenario | Metode | Status |
|---:|---|---|---|
| 1 | PHP syntax pada 23 file patch | `php -l` | **PASS** |
| 2 | Composer manifest | `composer validate --no-check-publish --no-interaction` | **PASS** |
| 3 | Frontend production bundle | `npm run build` | **PASS** |
| 4 | Web security headers | `SecurityHardeningTest` | **PASS** |
| 5 | Hermes body secret ditolak | `SecurityHardeningTest` | **PASS** |
| 6 | Hermes header secret diterima | `SecurityHardeningTest` | **PASS** |
| 7 | QR scan idempotent dan enrollment snapshot | `AttendanceScanServiceTest` | **PASS** |
| 8 | Duplicate guard QR/manual dan event closed | `AttendanceScanServiceTest` | **PASS** |
| 9 | Form Mustawa 1 valid/duplicate/signature/closed/honeypot | `GoogleFormAttendanceIntegrationTest` + `PublicMustawaOneAttendanceFormTest` | **PASS** |
| 10 | Upload pending/rejected/re-upload dan OCR queue | `PublicMustawaOneAttendanceFormTest` | **PASS** |
| 11 | Telegram review approve/reject/secret/AI recommendation | `TelegramReviewFeatureTest` | **PASS** |
| 12 | Archive/restore, relation, attendance, QR, sibling | `ArchiveIndexTest` + `StudentArchiveServiceTest` | **PASS** |
| 13 | Promotion/graduate dan source enrollment | `PromotionServiceTest` | **PASS** |
| 14 | Import preview rollback/no mutation dan duplicate email | `StudentFamilyImportServiceTest` | **PASS** |
| 15 | Backup ACL, SQL/XLSX export, failure handling | `AdminBackupTest` | **PASS** |
| 16 | Finalization, roster freeze, legacy backfill preview | `AttendanceFinalizationServiceTest` | **PASS** |
| 17 | Survey statistics/share closed-event access | `GuardianAttendanceShareStatisticsTest` | **PASS** |
| 18 | Data Induk fetch + create/update read model | `DataIndukClientTest` + `DataIndukRosterSyncServiceTest` | **PASS** |
| 19 | Parent credential update | `ParentCredentialsUpdateTest` | **PASS** |
| 20 | Full regression | `php artisan test --no-coverage` | **54 passed / 266 assertions** |
| 21 | Route registration/RBAC groups | `php artisan route:list` | **PASS / 89 routes** |
| 22 | Query/index code syntax | PHP lint + migration review | **PASS** |

### Matrix fitur end-to-end yang wajib dijalankan pada staging

| Fitur | Skenario manual staging | Expected result | Status saat audit |
|---|---|---|---|
| Login/password reset | login valid, invalid, logout, reset link expired | session aman, error generic, token sekali pakai | **Automated sebagian; staging smoke wajib** |
| Google login/link/unlink | callback valid, state mismatch, account conflict | callback ditolak bila state/session tidak cocok; tidak ada arbitrary user link | **Code + test suite; provider smoke wajib** |
| QR login | scan valid/expired/duplicate | login sekali, session regenerate, duplicate aman | **Code + service tests; device smoke wajib** |
| RBAC | akses tiap role ke admin/panitia/kepsek/wali | 403/redirect tanpa kebocoran data | **Route/static verified; role matrix staging wajib** |
| CRUD master | student, parent, class, user, kajian | create/update/delete/restore konsisten, validasi inline | **Regression coverage parsial; manual CRUD wajib** |
| Search/filter/sort/pagination | filter admin tables dan reset filter | query sesuai filter, no N+1 regression, state mobile jelas | **Code review; manual browser wajib** |
| Attendance transaction | QR/manual/upload/izin/re-upload | duplicate guard, transaction/rollback, proof pending | **PASS automated service/form suite** |
| Validation/approval | approve/reject preset reason, Telegram retry | hanya operator sah dapat mengubah status; idempotent | **PASS automated** |
| Export/import | SQL/XLSX download, preview/import family | authorization, metadata, no partial import | **PASS automated** |
| Data Induk | scheduled/manual sync, class mapping, retry | read model update, idempotent sync, no write-back master | **PASS automated local; external endpoint smoke wajib** |
| Upload | extension spoof, oversized, corrupt, valid JPG/PNG/PDF | reject unsafe file, random name, no raw error | **Rules + service code; staging object-storage test wajib** |
| UI responsive | 320/768/1024/desktop, keyboard and slow network | layout tetap, loading/empty/error feedback jelas | **Implemented polish; visual QA wajib** |
| Production observability | force 500 JSON/API and inspect logs | generic user response + searchable request ID | **Code verified; staging log check wajib** |

### Perintah release gate

```powershell
php artisan migrate --force
php artisan test --no-coverage
composer validate --no-check-publish --no-interaction
npm run build
php artisan route:list
```

Sebelum release, jalankan juga `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`, dan smoke test provider pada environment yang memiliki driver MariaDB production. Jangan membawa `.env` lokal `APP_DEBUG=true` ke production.
