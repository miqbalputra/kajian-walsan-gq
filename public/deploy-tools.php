<?php
/**
 * Artisan Command Wizard for cPanel/Shared Hosting without Terminal
 * Copy this file to your public/ directory
 * Access it via yourdomain.com/deploy-tools.php
 * REMOVE THIS FILE AFTER USE!
 */

use Illuminate\Support\Facades\Artisan;

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Simple auth check (optional, but recommended)
// In this case, I'll just keep it simple with a warning.

$output = "";
$message = "";

if (isset($_POST['command'])) {
    $command = $_POST['command'];
    
    try {
        switch ($command) {
            case 'migrate':
                Artisan::call('migrate', ['--force' => true]);
                $message = "Database migrated successfully!";
                break;
            case 'key:generate':
                Artisan::call('key:generate');
                $message = "App Key generated!";
                break;
            case 'storage:link':
                // Check if directory already exists
                $link = public_path('storage');
                if (file_exists($link)) {
                    @unlink($link);
                }
                Artisan::call('storage:link');
                $message = "Storage link created!";
                break;
            case 'optimize':
                Artisan::call('optimize:clear');
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');
                $message = "Optimization complete!";
                break;
            case 'seed':
                Artisan::call('db:seed', ['--force' => true]);
                $message = "Database seeded!";
                break;
            default:
                $message = "Invalid command.";
        }
        $output = Artisan::output();
    } catch (\Exception $e) {
        $message = "Error: " . $e->getMessage();
        $output = $e->getTraceAsString();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Deployment Toolkit - Kajian Walsan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-emerald-600 p-6 text-white text-center">
            <h1 class="text-2xl font-bold italic">Kajian Walsan</h1>
            <p class="text-emerald-100 mt-1">Deployment Tool (Tanpa Terminal)</p>
        </div>

        <div class="p-8">
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <p class="font-bold"><?php echo $message; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($output): ?>
                <div class="mb-6 p-4 rounded-lg bg-slate-900 text-slate-100 overflow-auto max-h-60 text-xs font-mono">
                    <pre><?php echo htmlspecialchars($output); ?></pre>
                </div>
            <?php endif; ?>

            <div class="space-y-4">
                <form method="POST" class="flex items-center justify-between p-4 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                    <div>
                        <h3 class="font-semibold text-slate-800">1. Generate Application Key</h3>
                        <p class="text-xs text-slate-500 italic">Jalankan jika .env baru dibuat (APP_KEY kosong)</p>
                    </div>
                    <button name="command" value="key:generate" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 shadow-sm">Jalankan</button>
                </form>

                <form method="POST" class="flex items-center justify-between p-4 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                    <div>
                        <h3 class="font-semibold text-slate-800">2. Migrasi Database</h3>
                        <p class="text-xs text-slate-500 italic">Jalankan untuk membuat tabel database</p>
                    </div>
                    <button name="command" value="migrate" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 shadow-sm">Jalankan</button>
                </form>

                <form method="POST" class="flex items-center justify-between p-4 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                    <div>
                        <h3 class="font-semibold text-slate-800">3. Link Storage</h3>
                        <p class="text-xs text-slate-500 italic">Agar logo dan file dapat diakses publik</p>
                    </div>
                    <button name="command" value="storage:link" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 shadow-sm">Jalankan</button>
                </form>

                <form method="POST" class="flex items-center justify-between p-4 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                    <div>
                        <h3 class="font-semibold text-slate-800">4. Optimasi Cache</h3>
                        <p class="text-xs text-slate-500 italic">Config, Route, dan View cache</p>
                    </div>
                    <button name="command" value="optimize" class="bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-800 shadow-sm">Optimasi</button>
                </form>

                <form method="POST" class="flex items-center justify-between p-4 bg-red-50 rounded-xl hover:bg-red-100 transition border border-red-100">
                    <div>
                        <h3 class="font-semibold text-red-800">5. Seed Database (Hati-hati)</h3>
                        <p class="text-xs text-red-500 italic">Isi data awal/dummy</p>
                    </div>
                    <button name="command" value="seed" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 shadow-sm">Seed</button>
                </form>
            </div>
            
            <div class="mt-10 border-t pt-6">
                <p class="text-center text-red-500 text-xs font-bold uppercase tracking-widest">⚠️ PENTING: Hapus file ini segera setelah aplikasi berjalan! ⚠️</p>
            </div>
        </div>
    </div>
</body>
</html>
