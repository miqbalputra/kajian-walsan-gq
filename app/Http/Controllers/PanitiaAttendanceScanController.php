<?php

namespace App\Http\Controllers;

use App\Models\KajianEvent;
use App\Services\AttendanceScanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PanitiaAttendanceScanController extends Controller
{
    public function store(Request $request, AttendanceScanService $attendanceScanService): JsonResponse
    {
        $data = $request->validate([
            'qr_code' => ['required', 'string', 'max:255'],
        ]);

        $activeEvent = KajianEvent::activeForAttendance();

        if (!$activeEvent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada kajian yang sedang dibuka.',
            ], 422);
        }

        $key = 'scanner-api:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 120)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'status' => 'error',
                'message' => "Terlalu banyak percobaan. Tunggu {$seconds} detik.",
            ], 429);
        }

        RateLimiter::hit($key, 60);

        try {
            $result = $attendanceScanService->process(
                $activeEvent,
                $data['qr_code'],
                auth()->id(),
                $request->userAgent()
            );

            $statusCode = match ($result['status']) {
                'success' => 200,
                'warning' => 409,
                default => 422,
            };

            return response()->json($result, $statusCode);
        } catch (\Throwable $exception) {
            Log::error('Scanner API Error: ' . $exception->getMessage(), [
                'user_id' => auth()->id(),
                'exception' => $exception::class,
                'qr_code_length' => strlen($data['qr_code']),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi gangguan pada server saat memproses scan.',
            ], 500);
        }
    }
}
