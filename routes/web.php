<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\QrLoginController;
use App\Http\Controllers\HomeController;
use App\Livewire\Admin\AttendanceValidation;
use App\Livewire\Admin\ClassIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\KajianIndex;
use App\Livewire\Admin\ParentIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\StudentIndex;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Panitia\Scanner;
use App\Livewire\WaliSantri\Dashboard as WaliSantriDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// QR Code Login Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/qr-login', [QrLoginController::class, 'showScanner'])->name('qr.login');
    Route::post('/qr-login', [QrLoginController::class, 'login'])->name('qr.login.process');
    Route::post('/qr-validate', [QrLoginController::class, 'validate'])->name('qr.validate');

    // Google OAuth - Login
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard - redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isPanitia()) {
            return redirect()->route('panitia.scanner');
        } elseif ($user->isWaliKelas()) {
            return redirect()->route('wali-kelas.dashboard');
        } elseif ($user->isKepsek()) {
            return view('dashboard.kepsek');
        } else {
            return redirect()->route('wali-santri.dashboard');
        }
    })->name('dashboard');

    // Admin Routes - Only admin can access
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/students', StudentIndex::class)->name('students.index');
        Route::get('/parents', ParentIndex::class)->name('parents.index');
        Route::get('/kajian', KajianIndex::class)->name('kajian.index');
        Route::get('/classes', ClassIndex::class)->name('classes.index');
        Route::get('/reports', ReportIndex::class)->name('reports.index');
        Route::get('/validation', AttendanceValidation::class)->name('validation.index');
        Route::get('/validation/trash', \App\Livewire\Admin\AttendanceTrash::class)->name('validation.trash');
        Route::get('/users', UserIndex::class)->name('users.index');
        Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
        Route::get('/surveys', \App\Livewire\Admin\SurveyAnalysis::class)->name('surveys.index');
    });

    // Panitia Routes - Admin and Panitia can access
    Route::prefix('panitia')->name('panitia.')->middleware('role:admin,panitia')->group(function () {
        Route::get('/scanner', Scanner::class)->name('scanner');
        Route::get('/jadwal', \App\Livewire\Panitia\JadwalKajian::class)->name('jadwal');
    });

    // Wali Kelas Routes - Admin and Wali Kelas can access
    Route::prefix('wali-kelas')->name('wali-kelas.')->middleware('role:admin,wali_kelas')->group(function () {
        Route::get('/', \App\Livewire\WaliKelas\Dashboard::class)->name('dashboard');
        Route::get('/reports', \App\Livewire\WaliKelas\ReportIndex::class)->name('reports');
    });

    // Wali Santri Routes - Only Wali Santri can access
    Route::prefix('wali-santri')->name('wali-santri.')->middleware('role:wali_santri')->group(function () {
        Route::get('/', WaliSantriDashboard::class)->name('dashboard');
        Route::get('/schedule', \App\Livewire\WaliSantri\KajianSchedule::class)->name('schedule');
        Route::get('/profile', \App\Livewire\WaliSantri\Profile::class)->name('profile');
    });

    // Google OAuth - Link/Unlink (untuk user yang sudah login)
    Route::get('/auth/google/link', [GoogleController::class, 'linkRedirect'])->name('google.link');
    Route::get('/auth/google/link/callback', [GoogleController::class, 'linkCallback'])->name('google.link.callback');
    Route::post('/auth/google/unlink', [GoogleController::class, 'unlink'])->name('google.unlink');

    // Web Push Subscription Routes
    Route::post('/push-subscription', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.store');
    Route::delete('/push-subscription', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.destroy');

    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Helper route for cPanel without SSH
Route::get('/artisan-helper', function () {
    try {
        $outputs = [];

        // 1. Run Migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $outputs[] = "Migration: " . \Illuminate\Support\Facades\Artisan::output();

        // 2. Clear Cache
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $outputs[] = "Cache cleared.";

        // 3. Storage Link
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $outputs[] = "Storage link created.";

        // 4. Fix Livewire Temp Uploads Directory & Permissions
        $livewireTmp = storage_path('app/livewire-tmp');
        if (!file_exists($livewireTmp)) {
            mkdir($livewireTmp, 0775, true);
            $outputs[] = "Created livewire-tmp directory for file uploads.";
        } else {
            chmod($livewireTmp, 0775);
            $outputs[] = "Set chmod 0775 on livewire-tmp directory.";
        }
        
        // Also ensure storage/app has correct permissions
        chmod(storage_path('app'), 0775);
        $outputs[] = "Set chmod 0775 on storage/app directory.";

        return "<pre>" . implode("\n", $outputs) . "</pre><br><a href='/dashboard'>Go to Dashboard</a>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Diagnostic route to find out exactly why PHP rejects files
Route::any('/test-upload', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('post')) {
        $file = $request->file('test_file');
        
        if (!$file) {
            $rawError = $_FILES['test_file']['error'] ?? 'No file submitted in raw $_FILES';
        } else {
            $rawError = $file->getError();
        }

        $errorMessages = [
            0 => 'UPLOAD_ERR_OK: Berhasil!',
            1 => 'UPLOAD_ERR_INI_SIZE: File terlalu besar (melebihi upload_max_filesize di php.ini cPanel)',
            2 => 'UPLOAD_ERR_FORM_SIZE: File terlalu besar (melebihi MAX_FILE_SIZE)',
            3 => 'UPLOAD_ERR_PARTIAL: File cuma terupload setengah (koneksi putus)',
            4 => 'UPLOAD_ERR_NO_FILE: Tidak ada file yang diupload',
            6 => 'UPLOAD_ERR_NO_TMP_DIR: Folder /tmp di server cPanel penuh atau permissions PHP salah (Open Basedir)',
            7 => 'UPLOAD_ERR_CANT_WRITE: Server gagal menulis file ke disk (HDD cPanel penuh)',
            8 => 'UPLOAD_ERR_EXTENSION: Ada ekstensi PHP yang memblokir upload'
        ];

        $meaning = $errorMessages[$rawError] ?? "Unknown Error: " . $rawError;

        $info = [
            'Pesan Error' => $meaning,
            'Kode Error PHP' => $rawError,
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'sys_get_temp_dir()' => sys_get_temp_dir(),
            'Sisa Disk cPanel' => function_exists('disk_free_space') ? round(disk_free_space('/') / 1024 / 1024) . ' MB' : 'Unknown',
        ];
        
        return response()->json($info);
    }

    return '<form method="POST" enctype="multipart/form-data">
                '.csrf_field().'
                <h3>Test Upload PHP (Bypass Livewire)</h3>
                <p>Pilih foto sembarang dari HP/Laptop Anda lalu klik Upload untuk mengecek error asli dari server cPanel Anda.</p>
                <input type="file" name="test_file" required>
                <button type="submit">Upload & Cek Error</button>
            </form>';
});

// Jalur khusus reset password dari Chatbot n8n
Route::post('/internal-reset-password', [App\Http\Controllers\Api\PasswordResetController::class, 'reset']);

