<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\QrLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PanitiaAttendanceScanController;
use App\Livewire\Admin\AnnouncementIndex;
use App\Livewire\Admin\AttendanceValidation;
use App\Livewire\Admin\ChatAi;
use App\Livewire\Admin\ClassIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\KajianIndex;
use App\Livewire\Admin\ParentIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\StudentIndex;
use App\Livewire\Admin\TeacherAttendanceIndex;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Kepsek\Dashboard as KepsekDashboard;
use App\Livewire\Kepsek\GuardianAttendanceReport as KepsekGuardianAttendanceReport;
use App\Livewire\Kepsek\SurveyAnalysis as KepsekSurveyAnalysis;
use App\Livewire\Kepsek\TeacherAttendanceReport as KepsekTeacherAttendanceReport;
use App\Livewire\Panitia\Scanner;
use App\Livewire\WaliSantri\Dashboard as WaliSantriDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/__deploy-version', function () {
    return response()->json([
        'version' => '2026-05-19-push-notification-loader-v4',
        'commit_hint' => 'push-notification-loader',
        'pwa_middleware_install' => class_exists(\App\Http\Middleware\InjectPwaInstallPrompt::class),
        'pwa_push_script' => file_exists(public_path('js/pwa-push.js')),
        'timestamp' => now()->toISOString(),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
})->name('deploy.version');

Route::get('/', [HomeController::class, 'index'])->name('home');

// QR Code Login Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/qr-login', [QrLoginController::class, 'showScanner'])->name('qr.login');
    Route::post('/qr-login', [QrLoginController::class, 'login'])->name('qr.login.process');
    Route::post('/qr-validate', [QrLoginController::class, 'validate'])->name('qr.validate');

    // Google OAuth - Login
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
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
            return redirect()->route('kepsek.dashboard');
        } elseif ($user->isGuru()) {
            return redirect()->route('wali-santri.dashboard');
        } else {
            return redirect()->route('wali-santri.dashboard');
        }
    })->name('dashboard');

    // Admin Routes - Only admin can access
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/students', StudentIndex::class)->name('students.index');
        Route::get('/parents', ParentIndex::class)->name('parents.index');
        Route::get('/parents/{parent}/kartu/download', function (\App\Models\ParentModel $parent) {
            $parent->loadMissing('user', 'students');
            abort_if($parent->isPureTeacher(), 404);

            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $qrSvg = $writer->writeString($parent->qr_code_string);

            return view('pdf.kartu-identitas', [
                'parent'   => $parent,
                'qrSvg'    => $qrSvg,
                'isMother' => $parent->type === 'mother',
            ]);
        })->name('parents.kartu.download');
        Route::get('/kajian', KajianIndex::class)->name('kajian.index');
        Route::get('/announcements', AnnouncementIndex::class)->name('announcements.index');
        Route::get('/chat-ai', ChatAi::class)->name('chat-ai');
        Route::get('/teacher-attendance', TeacherAttendanceIndex::class)->name('teacher-attendance.index');
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
        Route::post('/scan', [PanitiaAttendanceScanController::class, 'store'])->name('scan.store');
        Route::get('/jadwal', \App\Livewire\Panitia\JadwalKajian::class)->name('jadwal');
    });

    // Kepala Sekolah Routes - Read-only monitoring
    Route::prefix('kepsek')->name('kepsek.')->middleware('role:kepsek')->group(function () {
        Route::get('/', KepsekDashboard::class)->name('dashboard');
        Route::get('/guardian-attendance', KepsekGuardianAttendanceReport::class)->name('guardian-attendance.index');
        Route::get('/teacher-attendance', KepsekTeacherAttendanceReport::class)->name('teacher-attendance.index');
        Route::get('/surveys', KepsekSurveyAnalysis::class)->name('surveys.index');
    });

    // Wali Kelas Routes - Admin and Wali Kelas can access
    Route::prefix('wali-kelas')->name('wali-kelas.')->middleware('role:admin,wali_kelas')->group(function () {
        Route::get('/', \App\Livewire\WaliKelas\Dashboard::class)->name('dashboard');
        Route::get('/reports', \App\Livewire\WaliKelas\ReportIndex::class)->name('reports');
    });

    // Wali Santri Routes - Only Wali Santri and Guru can access
    Route::prefix('wali-santri')->name('wali-santri.')->middleware('role:wali_santri,guru')->group(function () {
        Route::get('/', WaliSantriDashboard::class)->name('dashboard');
        Route::get('/schedule', \App\Livewire\WaliSantri\KajianSchedule::class)->name('schedule');
        Route::get('/profile', \App\Livewire\WaliSantri\Profile::class)->name('profile');

        // Kartu Identitas - Print/Download Page (same approach as admin, proven to work)
        Route::get('/kartu/download', function () {
            $user = auth()->user();
            $parent = \App\Models\ParentModel::with('user', 'students')
                ->where('user_id', $user->id)
                ->firstOrFail();
            abort_if($parent->isPureTeacher(), 404);

            // Generate QR SVG (same as admin approach)
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $qrSvg = $writer->writeString($parent->qr_code_string);

            return view('pdf.kartu-identitas', [
                'parent'   => $parent,
                'qrSvg'    => $qrSvg,
                'isMother' => $parent->type === 'mother',
            ]);
        })->name('kartu.download');
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

// Jalur khusus reset password dari Chatbot n8n
Route::post('/internal-reset-password', [App\Http\Controllers\Api\PasswordResetController::class, 'reset']);
