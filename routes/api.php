<?php

use App\Http\Controllers\Api\HermesAgentController;
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
