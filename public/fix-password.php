<?php
/**
 * TEMPORARY SCRIPT - Reset admin password to bcrypt
 * HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!
 * 
 * Compatible with FLAT cPanel deployment (no /public subfolder)
 */

// Try flat structure first (cPanel), then standard structure
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    // Flat structure: fix-password.php is in same dir as vendor/
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Standard structure: fix-password.php is in public/
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
} else {
    die('ERROR: Cannot find vendor/autoload.php. Check file location.');
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$newPassword = 'admin123';

echo "<html><head><title>Fix Password</title></head><body style='font-family:Arial;padding:40px;max-width:600px;margin:auto;'>";
echo "<h2>🔧 Fix Admin Password</h2><hr><br>";

// Cari semua user untuk debugging
$allUsers = User::select('id', 'name', 'username', 'email', 'is_active', 'password', 'role_id')->get();

echo "<h3>Semua user di database:</h3>";
echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;width:100%;font-size:13px;'>";
echo "<tr style='background:#eee;'><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Active</th><th>Role ID</th><th>Hash OK?</th></tr>";

foreach ($allUsers as $user) {
    $hashInfo = password_get_info($user->password);
    $isBcrypt = ($hashInfo['algoName'] !== 'unknown') ? '✅ Yes' : '❌ No';
    echo "<tr>";
    echo "<td>{$user->id}</td>";
    echo "<td>{$user->name}</td>";
    echo "<td>{$user->username}</td>";
    echo "<td>{$user->email}</td>";
    echo "<td>" . ($user->is_active ? '✅' : '❌') . "</td>";
    echo "<td>{$user->role_id}</td>";
    echo "<td>{$isBcrypt}</td>";
    echo "</tr>";
}
echo "</table><br>";

// Cari admin - coba beberapa kemungkinan
$admin = User::where('username', 'admin')
    ->orWhere('email', 'admin@griyaquran.web.id')
    ->orWhere('email', 'admin@kajianwalsan.test')
    ->first();

// Kalau ga ketemu, ambil user pertama dengan role_id = 1
if (!$admin) {
    $admin = User::where('role_id', 1)->first();
}

if (!$admin) {
    echo "<h3 style='color:red;'>❌ Tidak ada user admin ditemukan!</h3>";
    echo "</body></html>";
    die();
}

echo "<h3>User admin yang akan di-reset:</h3>";
echo "<ul>";
echo "<li><strong>ID:</strong> {$admin->id}</li>";
echo "<li><strong>Name:</strong> {$admin->name}</li>";
echo "<li><strong>Username:</strong> {$admin->username}</li>";
echo "<li><strong>Email:</strong> {$admin->email}</li>";
echo "<li><strong>Old password hash (20 char):</strong> <code>" . substr($admin->password, 0, 20) . "...</code></li>";
echo "</ul>";

// Update password
$admin->password = Hash::make($newPassword);
$admin->save();

// Verify
$verify = Hash::check($newPassword, $admin->fresh()->password);

echo "<h3 style='color:" . ($verify ? 'green' : 'red') . ";'>" . ($verify ? '✅ Password berhasil di-reset dan TERVERIFIKASI!' : '❌ Password di-update tapi verifikasi GAGAL!') . "</h3>";
echo "<div style='background:#f0fff0;border:2px solid #4CAF50;padding:20px;border-radius:8px;margin:20px 0;'>";
echo "<p><strong>Login dengan:</strong></p>";
echo "<ul>";
echo "<li>Username: <strong>{$admin->username}</strong></li>";
echo "<li>Email: <strong>{$admin->email}</strong></li>";
echo "<li>Password: <strong>{$newPassword}</strong></li>";
echo "</ul>";
echo "</div>";
echo "<p style='color:red;font-size:18px;font-weight:bold;'>⚠️ HAPUS FILE fix-password.php SETELAH BERHASIL LOGIN!</p>";
echo "</body></html>";
