<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\GoogleFormAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class GoogleFormIntegrationController extends Controller
{
    public function __construct(private GoogleFormAttendanceService $attendanceService)
    {
    }

    public function options(Request $request): JsonResponse
    {
        if (! $this->hasValidSignature($request)) {
            return response()->json(['message' => 'Signature tidak valid.'], 401);
        }

        $year = AcademicYear::active();
        $students = Student::query()
            ->active()
            ->with('classRoom')
            ->whereHas('classRoom', fn ($query) => $query->where('level', '1'))
            ->orderBy('name')
            ->get();

        return response()->json([
            'academic_year' => $year?->name,
            'students' => $students->map(fn (Student $student) => [
                'reference' => $student->nis,
                'name' => $student->name,
                'label' => $student->name.' — '.$student->nis,
                'class' => $student->classRoom?->name,
            ])->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->hasValidSignature($request)) {
            return response()->json(['message' => 'Signature tidak valid.'], 401);
        }

        $payload = $request->validate([
            'response_id' => ['required', 'string', 'max:191'],
            'form_id' => ['nullable', 'string', 'max:191'],
            'submitted_at' => ['nullable', 'date'],
            'event_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['hadir_online', 'izin'])],
            'student_reference' => ['required', 'string', 'max:100'],
            'student_name' => ['required', 'string', 'max:255'],
            'parent_type' => ['required', Rule::in(['father', 'mother'])],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000', 'required_if:status,izin'],
        ]);

        try {
            $result = $this->attendanceService->receive($payload, $payload['form_id'] ?? null);
        } catch (\Throwable $exception) {
            Log::error('[Google Form] Submission processing failed', [
                'response_id' => $payload['response_id'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Respons diterima tetapi gagal diproses.'], 500);
        }

        $httpStatus = in_array($result['code'], ['duplicate_response', 'duplicate_attendance'], true)
            ? 200
            : match ($result['status']) {
                'processed' => 201,
                'duplicate' => 200,
                'unresolved' => 202,
                default => 200,
            };

        return response()->json($result, $httpStatus);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = (string) config('services.google_forms.webhook_secret');
        $provided = (string) $request->header('X-Google-Form-Signature');

        if ($secret === '' || $provided === '') {
            return false;
        }

        $provided = preg_replace('/^sha256=/i', '', $provided) ?: $provided;
        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $provided);
    }
}
