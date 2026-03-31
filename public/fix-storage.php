<?php
/**
 * fix-storage.php
 * Membuat folder dan permission yang dibutuhkan untuk file upload Livewire.
 * HAPUS FILE INI SETELAH DIGUNAKAN!
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$results = [];

// 1. Daftar folder yang harus ada dan bisa ditulis
$folders = [
    storage_path('app/public'),
    storage_path('app/public/attendance-proofs'),
    storage_path('app/public/izin-documents'),
    storage_path('app/livewire-tmp'),
    storage_path('framework/cache'),
    storage_path('framework/sessions'),
    storage_path('framework/views'),
    storage_path('logs'),
];

foreach ($folders as $folder) {
    if (!file_exists($folder)) {
        $created = mkdir($folder, 0775, true);
        $results[] = ($created ? '✅ Dibuat' : '❌ Gagal dibuat') . ': ' . $folder;
    } else {
        $writable = is_writable($folder);
        if (!$writable) {
            chmod($folder, 0775);
            $writable = is_writable($folder);
        }
        $results[] = ($writable ? '✅ OK' : '❌ TIDAK BISA DITULIS') . ': ' . $folder;
    }
}

// 2. Buat storage link jika belum ada
$publicStoragePath = __DIR__ . '/storage';
$targetPath = storage_path('app/public');

if (!file_exists($publicStoragePath)) {
    $linked = symlink($targetPath, $publicStoragePath);
    $results[] = ($linked ? '✅ Storage link berhasil dibuat' : '❌ Storage link GAGAL (buat manual di cPanel)');
} else {
    $results[] = '✅ Storage link sudah ada: ' . $publicStoragePath;
}

// 3. Test write ke livewire-tmp
$testFile = storage_path('app/livewire-tmp/test_' . time() . '.txt');
$written = file_put_contents($testFile, 'test');
if ($written !== false) {
    unlink($testFile);
    $results[] = '✅ Test tulis ke livewire-tmp: BERHASIL';
} else {
    $results[] = '❌ Test tulis ke livewire-tmp: GAGAL — Livewire upload tidak akan berfungsi!';
}

// Tampilkan hasil
echo '<!DOCTYPE html><html><head><title>Fix Storage</title>';
echo '<style>body{font-family:sans-serif;padding:30px;max-width:800px;} h2{color:#333;} li{margin:8px 0;font-family:monospace;font-size:14px;} .warn{background:#fff3cd;padding:15px;border-radius:8px;margin-top:20px;}</style>';
echo '</head><body>';
echo '<h2>🔧 Storage Fix Result</h2><hr>';
echo '<ul>';
foreach ($results as $result) {
    echo '<li>' . htmlspecialchars($result) . '</li>';
}
echo '</ul>';
echo '<div class="warn">⚠️ <strong>Hapus file ini setelah selesai!</strong> Jangan biarkan file ini tersedia di server production.</div>';
echo '</body></html>';
