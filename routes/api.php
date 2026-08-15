<?php

use App\Http\Controllers\Api\HermesAgentController;
use App\Http\Controllers\Api\GoogleFormIntegrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Jalur khusus Hermes Agent - akses data dan aksi presensi Wali Santri/Guru.
Route::prefix('hermes-agent')
    ->name('hermes-agent.')
    ->middleware('hermes.agent')
    ->group(function () {
        Route::post('/', [HermesAgentController::class, 'handle'])->name('handle');
        Route::get('/overview', [HermesAgentController::class, 'overview'])->name('overview');
        Route::get('/attendances', [HermesAgentController::class, 'attendances'])->name('attendances.index');
        Route::get('/attendances/{attendance}', [HermesAgentController::class, 'attendanceDetail'])->name('attendances.show');
        Route::post('/attendances/manual', [HermesAgentController::class, 'storeManualAttendance'])->name('attendances.manual');
        Route::post('/attendances/{attendance}/proof', [HermesAgentController::class, 'updateAttendanceProof'])->name('attendances.proof');
    });

Route::prefix('api/integrations/google-forms/mustawa-1')
    ->middleware('throttle:30,1')
    ->name('google-forms.mustawa-1.')
    ->group(function () {
        Route::get('/options', [GoogleFormIntegrationController::class, 'options'])->name('options');
        Route::post('/', [GoogleFormIntegrationController::class, 'store'])->name('store');
    });
