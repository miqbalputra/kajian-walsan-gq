<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ReviewAttendanceProof;
use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HermesAgentController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $request->merge([
            'action' => $this->normalizeAction((string) $request->input('action', '')),
        ]);

        $data = $request->validate([
            'action' => ['required', Rule::in([
                'ping',
                'overview',
                'attendances',
                'attendance_detail',
                'read_attendance',
                'create_attendance',
                'manual_attendance',
                'update_attendance',
                'update_proof',
                'delete_attendance',
                'restore_attendance',
            ])],
            'attendance_id' => ['required_if:action,attendance_detail,read_attendance,update_attendance,update_proof,delete_attendance,restore_attendance', 'nullable', 'integer'],
        ]);

        return match ($data['action']) {
            'ping' => $this->ping(),
            'overview' => $this->overview($request),
            'attendances' => $this->attendances($request),
            'attendance_detail' => $this->attendanceDetail($this->findAttendanceForAction($data['attendance_id'], withTrashed: true)),
            'read_attendance' => $this->attendanceDetail($this->findAttendanceForAction($data['attendance_id'], withTrashed: true)),
            'create_attendance' => $this->createAttendance($request),
            'manual_attendance' => $this->storeManualAttendance($request),
            'update_attendance' => $this->updateAttendance($request, $this->findAttendanceForAction($data['attendance_id'])),
            'update_proof' => $this->updateAttendanceProof($request, $this->findAttendanceForAction($data['attendance_id'])),
            'delete_attendance' => $this->deleteAttendance($this->findAttendanceForAction($data['attendance_id'])),
            'restore_attendance' => $this->restoreAttendance($this->findAttendanceForAction($data['attendance_id'], withTrashed: true)),
        };
    }

    public function ping(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Hermes Agent API aktif.',
            'generated_at' => now()->toISOString(),
            'endpoint' => url('/hermes-agent'),
            'actions' => [
                'ping',
                'overview',
                'attendances',
                'read_attendance',
                'create_attendance',
                'update_attendance',
                'delete_attendance',
                'restore_attendance',
            ],
        ]);
    }

    protected function normalizeAction(string $action): string
    {
        $action = str($action)
            ->lower()
            ->replace([' ', '-'], '_')
            ->toString();

        return match ($action) {
            'status', 'health', 'check' => 'ping',
            'data', 'dashboard', 'summary', 'overview_data' => 'overview',
            'attendance', 'presence', 'presensi', 'get_presence', 'get_presensi', 'rekap', 'list_attendance', 'list_presence' => 'attendances',
            'detail', 'detail_attendance', 'detail_presence', 'read_presence' => 'read_attendance',
            'mark_presence', 'mark_attendance', 'input_presensi', 'input_attendance', 'create_presence', 'manual_presence' => 'create_attendance',
            'edit_presence', 'edit_attendance', 'update_presence' => 'update_attendance',
            'delete_presence', 'hapus_presensi' => 'delete_attendance',
            'restore_presence', 'undo_delete', 'restore_presensi' => 'restore_attendance',
            default => $action,
        };
    }

    public function overview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'audience' => ['nullable', Rule::in(['wali_santri', 'guru'])],
            'event_limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'category' => ['nullable', 'string', 'in:kajian,rapor,pertemuan'],
        ]);

        $latestEvent = KajianEvent::whereDate('date', '<=', today())
            ->when($data['category'] ?? null, fn (Builder $q, string $cat) => $q->where('category', $cat))
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->first();

        $latestRows = $latestEvent
            ? $this->completeAttendanceRows($latestEvent, $data['audience'] ?? null)
            : collect();

        return response()->json([
            'success' => true,
            'generated_at' => now()->toISOString(),
            'totals' => [
                'users' => User::count(),
                'wali_santri' => ParentModel::whereIn('type', ['father', 'mother'])->count(),
                'guru' => ParentModel::where(function (Builder $query) {
                    $query->where('type', 'teacher')->orWhere('is_teacher', true);
                })->count(),
                'students' => Student::count(),
                'kajian_events' => KajianEvent::count(),
                'attendances' => Attendance::count(),
            ],
            'attendance_summary' => Attendance::query()
                ->selectRaw('status, validation_status, count(*) as total')
                ->groupBy('status', 'validation_status')
                ->orderBy('status')
                ->orderBy('validation_status')
                ->get(),
            'latest_event' => $latestEvent ? $this->formatEvent($latestEvent) : null,
            'latest_event_summary' => $latestEvent ? $this->summarizeRows($latestRows) : null,
            'recent_events' => KajianEvent::when($data['category'] ?? null, fn (Builder $q, string $cat) => $q->where('category', $cat))
                ->orderByDesc('date')
                ->orderByDesc('time_start')
                ->limit((int) ($data['event_limit'] ?? 10))
                ->get()
                ->map(fn (KajianEvent $event) => $this->formatEvent($event))
                ->values(),
        ]);
    }

    public function attendances(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kajian_event_id' => ['nullable', 'integer', 'exists:kajian_events,id'],
            'audience' => ['nullable', Rule::in(['wali_santri', 'guru'])],
            'status' => ['nullable', Rule::in([
                Attendance::STATUS_HADIR_FISIK,
                Attendance::STATUS_HADIR_ONLINE,
                Attendance::STATUS_IZIN,
                Attendance::STATUS_ALPHA,
            ])],
            'validation_status' => ['nullable', Rule::in([
                Attendance::VALIDATION_APPROVED,
                Attendance::VALIDATION_PENDING,
                Attendance::VALIDATION_REJECTED,
            ])],
            'search' => ['nullable', 'string', 'max:100'],
            'complete' => ['nullable', 'boolean'],
            'category' => ['nullable', 'string', 'in:kajian,rapor,pertemuan'],
            'trashed' => ['nullable', Rule::in(['without', 'with', 'only'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        if ($request->boolean('complete')) {
            $event = isset($data['kajian_event_id'])
                ? KajianEvent::findOrFail($data['kajian_event_id'])
                : KajianEvent::whereDate('date', '<=', today())
                    ->when($data['category'] ?? null, fn (Builder $q, string $cat) => $q->where('category', $cat))
                    ->orderByDesc('date')
                    ->orderByDesc('time_start')
                    ->first();

            if (! $event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada data kajian untuk dibuat presensi lengkap.',
                ], 404);
            }

            $rows = $this->completeAttendanceRows($event, $data['audience'] ?? null, $data['search'] ?? null)
                ->when($data['status'] ?? null, fn (Collection $rows, string $status) => $rows->where('derived_status', $status))
                ->when($data['validation_status'] ?? null, fn (Collection $rows, string $status) => $rows->where('validation_status', $status))
                ->values();

            return response()->json([
                'success' => true,
                'mode' => 'complete_roster',
                'event' => $this->formatEvent($event),
                'summary' => $this->summarizeRows($rows),
                'data' => $rows,
            ]);
        }

        $attendances = Attendance::with(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator'])
            ->when(($data['trashed'] ?? 'without') === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($data['trashed'] ?? 'without') === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->when($data['kajian_event_id'] ?? null, fn (Builder $query, int $eventId) => $query->where('kajian_event_id', $eventId))
            ->when($data['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($data['validation_status'] ?? null, fn (Builder $query, string $status) => $query->where('validation_status', $status))
            ->when($data['audience'] ?? null, function (Builder $query, string $audience) {
                $query->whereHas('parent', fn (Builder $parentQuery) => $this->applyAudienceScope($parentQuery, $audience));
            })
            ->when($data['category'] ?? null, function (Builder $query, string $category) {
                $query->whereHas('kajianEvent', fn (Builder $eventQuery) => $eventQuery->where('category', $category));
            })
            ->when($data['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->whereHas('parent.user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('parent.students', function (Builder $studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc('created_at')
            ->paginate((int) ($data['per_page'] ?? 50));

        return response()->json([
            'success' => true,
            'mode' => 'attendance_records',
            'data' => $attendances->through(fn (Attendance $attendance) => $this->formatAttendance($attendance)),
        ]);
    }

    public function attendanceDetail(Attendance $attendance): JsonResponse
    {
        $attendance->load(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']);

        return response()->json([
            'success' => true,
            'data' => $this->formatAttendance($attendance, detailed: true),
        ]);
    }

    public function storeManualAttendance(Request $request): JsonResponse
    {
        return $this->storeAttendance($request, allowExisting: true);
    }

    public function createAttendance(Request $request): JsonResponse
    {
        return $this->storeAttendance($request, allowExisting: false);
    }

    protected function storeAttendance(Request $request, bool $allowExisting): JsonResponse
    {
        $data = $request->validate($this->attendanceMutationRules(requireStatus: true, requireTarget: true));

        $event = $this->resolveEvent($data['kajian_event_id'] ?? null);
        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kajian aktif. Kirim kajian_event_id untuk memilih kajian tertentu.',
            ], 422);
        }

        $policy = $event->policy;
        $allowedStatuses = $policy['statuses'] ?? ['hadir_fisik', 'hadir_online', 'izin', 'alpha'];
        if (! empty($allowedStatuses) && ! in_array($data['status'], $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => "Status '{$data['status']}' tidak didukung untuk kegiatan ini.",
                'allowed_statuses' => $allowedStatuses,
            ], 422);
        }

        $parent = $this->resolveParent($data);
        if (! $parent) {
            return response()->json([
                'success' => false,
                'message' => 'Wali Santri/Guru tidak ditemukan.',
            ], 404);
        }

        $studentId = $this->resolveStudentId($parent, $data['student_id'] ?? null);
        if (($data['student_id'] ?? null) && ! $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'student_id tidak terhubung dengan Wali Santri/Guru tersebut.',
            ], 422);
        }

        $proofFile = $this->storeProofFile($request, $parent, $data['status'], $policy);
        $method = $proofFile ? Attendance::METHOD_UPLOAD : ($data['method'] ?? Attendance::METHOD_MANUAL);
        $validationStatus = $data['validation_status']
            ?? ($proofFile ? Attendance::VALIDATION_PENDING : Attendance::VALIDATION_APPROVED);

        $attendance = Attendance::withTrashed()
            ->where('kajian_event_id', $event->id)
            ->where('parent_id', $parent->id)
            ->first();

        if ($attendance && ! $attendance->trashed() && ! $allowExisting) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi untuk peserta dan kajian ini sudah ada. Gunakan action update_attendance untuk mengubahnya.',
                'data' => $this->formatAttendance($attendance->load(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']), detailed: true),
            ], 409);
        }

        if (! $attendance) {
            $attendance = new Attendance([
                'kajian_event_id' => $event->id,
                'parent_id' => $parent->id,
            ]);
        } elseif ($attendance->trashed()) {
            $attendance->restore();
        }

        $finalProofFile = $proofFile ?: ($data['keep_existing_proof'] ?? true ? $attendance->proof_file : null);
        $finalNotes = $data['notes'] ?? $attendance->notes;
        $policyErrors = $this->attendancePolicyErrors($policy, $parent, $data['status'], $finalProofFile, $finalNotes);

        if ($policyErrors) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi tidak memenuhi aturan aplikasi.',
                'errors' => $policyErrors,
            ], 422);
        }

        $attendance->fill([
            'student_id' => $studentId,
            'status' => $data['status'],
            'method' => $method,
            'validation_status' => $validationStatus,
            'proof_file' => $finalProofFile,
            'notes' => $finalNotes,
            'validated_by' => null,
            'validated_at' => $validationStatus === Attendance::VALIDATION_APPROVED ? now() : null,
            'rejection_reason' => $validationStatus === Attendance::VALIDATION_REJECTED
                ? ($data['rejection_reason'] ?? $attendance->rejection_reason)
                : null,
            'scanned_at' => $data['status'] === Attendance::STATUS_HADIR_FISIK ? ($attendance->scanned_at ?: now()) : $attendance->scanned_at,
            'device_info' => mb_substr('Hermes Agent API', 0, 255),
        ]);

        // FIX #1: Wrap save dalam try-catch untuk race condition.
        // Dua request Hermes konkuren bisa lewati check withTrashed()->first()
        // bersamaan. Unique index (kajian_event_id, parent_id) akan menangkap
        // di level DB, tapi tanpa catch ini akan jadi 500 error ke Hermes.
        try {
            $attendance->save();
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? null) === '23000' || ($e->errorInfo[1] ?? null) === 1062) {
                $existing = Attendance::where('kajian_event_id', $event->id)
                    ->where('parent_id', $parent->id)
                    ->first();

                return response()->json([
                    'success' => false,
                    'message' => 'Presensi untuk peserta dan kajian ini sudah ada (terdeteksi saat penyimpanan). Gunakan action update_attendance untuk mengubahnya.',
                    'data' => $existing
                        ? $this->formatAttendance($existing->load(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']), detailed: true)
                        : null,
                ], 409);
            }
            throw $e;
        }

        $this->maybeRunAiReview($attendance->fresh(), $request->boolean('run_ai_review', (bool) $proofFile));
        $event->updateAttendanceCount();

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan via Hermes Agent.',
            'data' => $this->formatAttendance($attendance->fresh(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']), detailed: true),
        ]);
    }

    public function updateAttendanceProof(Request $request, Attendance $attendance): JsonResponse
    {
        return $this->updateAttendance($request, $attendance);
    }

    public function updateAttendance(Request $request, Attendance $attendance): JsonResponse
    {
        $data = $request->validate($this->attendanceMutationRules(requireStatus: false, requireTarget: false));

        $attendance->loadMissing(['parent.students', 'kajianEvent']);
        $status = $data['status'] ?? $attendance->status;
        $policy = $attendance->kajianEvent?->policy ?? config('event_categories.kajian');
        $proofFile = $this->storeProofFile($request, $attendance->parent, $status, $policy);

        if (! $proofFile
            && ! $request->filled('notes')
            && ! isset($data['status'])
            && ! isset($data['student_id'])
            && ! isset($data['validation_status'])
            && ! $request->boolean('clear_proof')) {
            return response()->json([
                'success' => false,
                'message' => 'Kirim proof_photo/proof_url, notes, status, student_id, validation_status, atau clear_proof untuk memperbarui presensi.',
            ], 422);
        }

        $studentId = array_key_exists('student_id', $data)
            ? $this->resolveStudentId($attendance->parent, $data['student_id'])
            : $attendance->student_id;
        if (($data['student_id'] ?? null) && ! $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'student_id tidak terhubung dengan Wali Santri/Guru tersebut.',
            ], 422);
        }

        $finalProofFile = $request->boolean('clear_proof')
            ? null
            : ($proofFile ?: $attendance->proof_file);
        $finalNotes = $request->filled('notes') ? $data['notes'] : $attendance->notes;
        $policyErrors = $this->attendancePolicyErrors($policy, $attendance->parent, $status, $finalProofFile, $finalNotes);

        if ($policyErrors) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi tidak memenuhi aturan aplikasi.',
                'errors' => $policyErrors,
            ], 422);
        }

        $validationStatus = $data['validation_status'] ?? ($proofFile ? Attendance::VALIDATION_PENDING : $attendance->validation_status);

        $attendance->update([
            'student_id' => $studentId,
            'status' => $status,
            'method' => $proofFile ? Attendance::METHOD_UPLOAD : $attendance->method,
            'proof_file' => $finalProofFile,
            'notes' => $finalNotes,
            'validation_status' => $validationStatus,
            'validated_by' => null,
            'validated_at' => $validationStatus === Attendance::VALIDATION_APPROVED ? now() : null,
            'rejection_reason' => $validationStatus === Attendance::VALIDATION_REJECTED
                ? ($data['rejection_reason'] ?? $attendance->rejection_reason)
                : null,
        ]);

        $this->maybeRunAiReview($attendance->fresh(), $request->boolean('run_ai_review', (bool) $proofFile));
        $attendance->kajianEvent?->updateAttendanceCount();

        return response()->json([
            'success' => true,
            'message' => 'Foto/catatan presensi berhasil diperbarui via Hermes Agent.',
            'data' => $this->formatAttendance($attendance->fresh(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']), detailed: true),
        ]);
    }

    public function deleteAttendance(Attendance $attendance): JsonResponse
    {
        if ($attendance->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi sudah berada di tempat sampah.',
                'data' => $this->formatAttendance($attendance, detailed: true),
            ], 409);
        }

        $attendance->loadMissing(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']);
        $event = $attendance->kajianEvent;
        $payload = $this->formatAttendance($attendance, detailed: true);

        $attendance->delete();
        $event?->updateAttendanceCount();

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dihapus via Hermes Agent.',
            'data' => $payload,
        ]);
    }

    public function restoreAttendance(Attendance $attendance): JsonResponse
    {
        if (! $attendance->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi ini belum terhapus, jadi tidak perlu direstore.',
                'data' => $this->formatAttendance($attendance, detailed: true),
            ], 409);
        }

        $duplicateExists = Attendance::query()
            ->where('kajian_event_id', $attendance->kajian_event_id)
            ->where('parent_id', $attendance->parent_id)
            ->exists();

        if ($duplicateExists) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa restore karena sudah ada presensi aktif untuk peserta dan kajian yang sama.',
                'data' => $this->formatAttendance($attendance, detailed: true),
            ], 409);
        }

        $attendance->restore();
        $attendance->kajianEvent?->updateAttendanceCount();

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil direstore via Hermes Agent.',
            'data' => $this->formatAttendance($attendance->fresh(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']), detailed: true),
        ]);
    }

    protected function findAttendanceForAction(int $attendanceId, bool $withTrashed = false): Attendance
    {
        $query = Attendance::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($attendanceId);
    }

    protected function attendanceMutationRules(bool $requireStatus, bool $requireTarget): array
    {
        $targetRule = $requireTarget ? 'required_without_all:user_id,qr_code' : 'nullable';

        return [
            'kajian_event_id' => ['nullable', 'integer', 'exists:kajian_events,id'],
            'parent_id' => [$targetRule, 'nullable', 'integer', 'exists:parents,id'],
            'user_id' => [$requireTarget ? 'required_without_all:parent_id,qr_code' : 'nullable', 'nullable', 'integer', 'exists:users,id'],
            'qr_code' => [$requireTarget ? 'required_without_all:parent_id,user_id' : 'nullable', 'nullable', 'string', 'max:255'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'status' => [$requireStatus ? 'required' : 'nullable', Rule::in([
                Attendance::STATUS_HADIR_FISIK,
                Attendance::STATUS_HADIR_ONLINE,
                Attendance::STATUS_IZIN,
                Attendance::STATUS_ALPHA,
            ])],
            'method' => ['nullable', Rule::in([Attendance::METHOD_MANUAL, Attendance::METHOD_UPLOAD])],
            'validation_status' => ['nullable', Rule::in([
                Attendance::VALIDATION_APPROVED,
                Attendance::VALIDATION_PENDING,
                Attendance::VALIDATION_REJECTED,
            ])],
            'proof_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            // FIX #3: proof_url hanya menerima URL Cloudinary (res.cloudinary.com)
            // untuk mencegah phishing/SSRF via URL arbitrer yang dikirim ke AI vision model.
            'proof_url' => ['nullable', 'url', 'max:2048', function ($attribute, $value, $fail) {
                if (! empty($value) && ! CloudinaryService::isCloudinaryUrl($value)) {
                    $fail('proof_url harus berupa URL Cloudinary yang valid (res.cloudinary.com).');
                }
            }],
            'notes' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
            'keep_existing_proof' => ['nullable', 'boolean'],
            'clear_proof' => ['nullable', 'boolean'],
            'run_ai_review' => ['nullable', 'boolean'],
        ];
    }

    protected function attendancePolicyErrors(array $policy, ParentModel $parent, string $status, ?string $proofFile, ?string $notes): array
    {
        $errors = [];
        $hasProof = filled($proofFile);
        $hasNotes = filled(is_string($notes) ? trim($notes) : $notes);
        // $policy sudah di-pass sebagai parameter (merge config defaults + event overrides)

        if ($status === Attendance::STATUS_HADIR_ONLINE
            && ($policy['online_requires_proof'] ?? true)
            && ! $hasProof) {
            $errors['proof_file'][] = 'Hadir online wajib menyertakan file/foto catatan hasil kajian.';
        }

        if ($status === Attendance::STATUS_IZIN) {
            if (($policy['izin_requires_proof'] ?? true) && ! $hasProof) {
                $errors['proof_file'][] = 'Izin wajib menyertakan file/foto surat atau keterangan izin.';
            }

            if (($policy['izin_requires_notes'] ?? true) && ! $hasNotes) {
                $errors['notes'][] = 'Izin wajib menyertakan catatan alasan.';
            }
        }

        if ($parent->isTeacher()
            && $status === Attendance::STATUS_HADIR_FISIK
            && ($policy['guru_hadir_fisik_requires_proof'] ?? true)
            && ! $hasProof) {
            $errors['proof_file'][] = 'Guru hadir fisik wajib menyertakan foto catatan hasil kajian.';
        }

        return $errors;
    }

    protected function resolveEvent(?int $eventId): ?KajianEvent
    {
        if ($eventId) {
            return KajianEvent::find($eventId);
        }

        return KajianEvent::activeForAttendance();
    }

    protected function resolveParent(array $data): ?ParentModel
    {
        $query = ParentModel::with(['user.role', 'students.classRoom']);

        if (! empty($data['parent_id'])) {
            return $query->find($data['parent_id']);
        }

        if (! empty($data['user_id'])) {
            return $query->where('user_id', $data['user_id'])->first();
        }

        if (! empty($data['qr_code'])) {
            return $query->where('qr_code_string', trim($data['qr_code']))->first();
        }

        return null;
    }

    protected function resolveStudentId(ParentModel $parent, ?int $studentId): ?int
    {
        if ($studentId) {
            return $parent->students()->whereKey($studentId)->exists() ? $studentId : null;
        }

        return $parent->students()->first()?->id;
    }

    protected function storeProofFile(Request $request, ParentModel $parent, string $status, array $policy = []): ?string
    {
        if ($request->filled('proof_url')) {
            // Validasi domain sudah di-handle di attendanceMutationRules (hanya Cloudinary).
            return $request->input('proof_url');
        }

        if (! $request->hasFile('proof_photo')) {
            return null;
        }

        $folders = $policy['proof_folders'] ?? [];

        $folder = match (true) {
            $parent->isTeacher() && $status === Attendance::STATUS_IZIN => $folders['teacher_izin'] ?? 'teacher-permission-letters',
            $parent->isTeacher() => $folders['teacher_notes'] ?? 'teacher-attendance-notes',
            $status === Attendance::STATUS_IZIN => $folders['izin'] ?? 'izin-documents',
            default => $folders['attendance'] ?? 'attendance-proofs',
        };

        return app(CloudinaryService::class)->upload($request->file('proof_photo'), $folder)['url'];
    }

    protected function maybeRunAiReview(Attendance $attendance, bool $shouldRun): void
    {
        if (! $shouldRun || ! $attendance->proof_file) {
            return;
        }

        // FIX #2: Dispatch ke queue agar response Hermes tidak blocking.
        // Sebelumnya autoReviewAttendance dipanggil synchronous (bisa hang 90 detik).
        ReviewAttendanceProof::dispatch($attendance);
    }

    protected function completeAttendanceRows(KajianEvent $event, ?string $audience = null, ?string $search = null): Collection
    {
        $attendances = Attendance::with(['validator', 'student.classRoom'])
            ->where('kajian_event_id', $event->id)
            ->get()
            ->keyBy('parent_id');

        $event->loadMissing('targetClasses');

        return ParentModel::with(['user', 'students.classRoom'])
            ->when($audience, fn (Builder $query, string $audience) => $this->applyAudienceScope($query, $audience))
            ->when($audience === 'wali_santri', fn (Builder $query) => $query->targetedByEvent($event))
            ->when($audience === null && ! $event->targetsAllClasses(), function (Builder $query) use ($event) {
                $targetClassIds = $event->targetClassIds()->all();

                $query->where(function (Builder $query) use ($targetClassIds) {
                    $query->where(function (Builder $guardianQuery) use ($targetClassIds) {
                        $guardianQuery->whereIn('type', ['father', 'mother'])
                            ->whereHas('students', fn (Builder $studentQuery) => $studentQuery->whereIn('students.class_id', $targetClassIds));
                    })->orWhere(function (Builder $teacherQuery) {
                        $teacherQuery->where('type', 'teacher')->orWhere('is_teacher', true);
                    });
                });
            })
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->whereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('students', function (Builder $studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('id')
            ->get()
            ->map(function (ParentModel $parent) use ($attendances, $event) {
                $attendance = $attendances->get($parent->id);
                $derivedStatus = $this->deriveStatus($parent, $attendance, $event);

                return [
                    'audience' => $parent->isTeacher() ? 'guru' : 'wali_santri',
                    'parent' => $this->formatParent($parent),
                    'attendance_id' => $attendance?->id,
                    'status' => $attendance?->status,
                    'validation_status' => $attendance?->validation_status,
                    'derived_status' => $derivedStatus,
                    'method' => $attendance?->method,
                    'proof_url' => $attendance?->proof_file ? CloudinaryService::getDisplayUrl($attendance->proof_file) : null,
                    'notes' => $attendance?->notes,
                    'rejection_reason' => $attendance?->rejection_reason,
                    'validated_by' => $attendance?->validator?->name,
                    'submitted_at' => optional($attendance?->created_at)->toISOString(),
                    'updated_at' => optional($attendance?->updated_at)->toISOString(),
                ];
            })
            ->values();
    }

    protected function applyAudienceScope(Builder $query, string $audience): Builder
    {
        if ($audience === 'guru') {
            return $query->where(function (Builder $query) {
                $query->where('type', 'teacher')->orWhere('is_teacher', true);
            });
        }

        return $query->whereIn('type', ['father', 'mother']);
    }

    protected function deriveStatus(ParentModel $parent, ?Attendance $attendance, KajianEvent $event): string
    {
        if (! $attendance) {
            return Attendance::STATUS_ALPHA;
        }

        if ($attendance->validation_status === Attendance::VALIDATION_REJECTED) {
            return Attendance::VALIDATION_REJECTED;
        }

        if ($attendance->validation_status === Attendance::VALIDATION_PENDING) {
            return Attendance::VALIDATION_PENDING;
        }

        if ($parent->isTeacher() && ! $attendance->proof_file && $this->eventHasEnded($event)) {
            return Attendance::STATUS_ALPHA;
        }

        return $attendance->status;
    }

    protected function eventHasEnded(KajianEvent $event): bool
    {
        $end = Carbon::parse($event->date->format('Y-m-d').' '.Carbon::parse($event->time_end)->format('H:i:s'));

        return now()->gt($end) || $event->status === 'closed';
    }

    protected function summarizeRows(Collection $rows): array
    {
        return [
            'total' => $rows->count(),
            'wali_santri' => $rows->where('audience', 'wali_santri')->count(),
            'guru' => $rows->where('audience', 'guru')->count(),
            'hadir_fisik' => $rows->where('derived_status', Attendance::STATUS_HADIR_FISIK)->count(),
            'hadir_online' => $rows->where('derived_status', Attendance::STATUS_HADIR_ONLINE)->count(),
            'izin' => $rows->where('derived_status', Attendance::STATUS_IZIN)->count(),
            'pending' => $rows->where('derived_status', Attendance::VALIDATION_PENDING)->count(),
            'rejected' => $rows->where('derived_status', Attendance::VALIDATION_REJECTED)->count(),
            'alpha' => $rows->where('derived_status', Attendance::STATUS_ALPHA)->count(),
        ];
    }

    protected function formatAttendance(Attendance $attendance, bool $detailed = false): array
    {
        $attendance->loadMissing(['kajianEvent', 'parent.user', 'parent.students.classRoom', 'student.classRoom', 'validator']);

        $data = [
            'id' => $attendance->id,
            'event' => $attendance->kajianEvent ? $this->formatEvent($attendance->kajianEvent) : null,
            'parent' => $attendance->parent ? $this->formatParent($attendance->parent) : null,
            'student' => $attendance->student ? $this->formatStudent($attendance->student) : null,
            'status' => $attendance->status,
            'status_display' => $attendance->status_display,
            'method' => $attendance->method,
            'method_display' => $attendance->method_display,
            'validation_status' => $attendance->validation_status,
            'validation_display' => $attendance->validation_display,
            'proof_file' => $attendance->proof_file,
            'proof_url' => $attendance->proof_file ? CloudinaryService::getDisplayUrl($attendance->proof_file) : null,
            'notes' => $attendance->notes,
            'rejection_reason' => $attendance->rejection_reason,
            'validator' => $attendance->validator?->name,
            'scanned_at' => optional($attendance->scanned_at)->toISOString(),
            'created_at' => optional($attendance->created_at)->toISOString(),
            'updated_at' => optional($attendance->updated_at)->toISOString(),
            'deleted_at' => optional($attendance->deleted_at)->toISOString(),
            'is_deleted' => $attendance->trashed(),
        ];

        if ($detailed) {
            $data['ai_review'] = [
                'status' => $attendance->ai_validation_status,
                'confidence' => $attendance->ai_validation_confidence,
                'reason' => $attendance->ai_validation_reason,
                'model' => $attendance->ai_validation_model,
                'validated_at' => optional($attendance->ai_validated_at)->toISOString(),
            ];
            $data['device_info'] = $attendance->device_info;
            $data['scan_location'] = $attendance->scan_location;
        }

        return $data;
    }

    protected function formatEvent(KajianEvent $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'speaker' => $event->speaker,
            'location' => $event->location,
            'date' => optional($event->date)->toDateString(),
            'formatted_date' => $event->formatted_date,
            'time_start' => optional($event->time_start)->format('H:i'),
            'time_end' => optional($event->time_end)->format('H:i'),
            'time_range' => $event->time_range,
            'status' => $event->status,
            'attendance_count' => $event->attendance_count,
            'category' => $event->category ?? 'kajian',
            'category_display' => $event->category_display,
            'policy' => [
                'statuses' => $event->policy['statuses'] ?? ['hadir_fisik', 'hadir_online', 'izin', 'alpha'],
                'online_enabled' => $event->policy['online_enabled'] ?? false,
                'izin_requires_proof' => $event->policy['izin_requires_proof'] ?? true,
                'izin_requires_notes' => $event->policy['izin_requires_notes'] ?? true,
                'guru_hadir_fisik_requires_proof' => $event->policy['guru_hadir_fisik_requires_proof'] ?? false,
                'ai_review' => $event->policy['ai_review'] ?? false,
            ],
        ];
    }

    protected function formatParent(ParentModel $parent): array
    {
        $parent->loadMissing(['user', 'students.classRoom']);

        return [
            'id' => $parent->id,
            'user_id' => $parent->user_id,
            'name' => $parent->user?->name,
            'username' => $parent->user?->username,
            'email' => $parent->user?->email,
            'phone' => $parent->user?->phone,
            'type' => $parent->type,
            'type_display' => $parent->type_display,
            'is_teacher' => $parent->isTeacher(),
            'qr_code' => $parent->qr_code_string,
            'students' => $parent->students
                ->map(fn (Student $student) => $this->formatStudent($student))
                ->values(),
        ];
    }

    protected function formatStudent(Student $student): array
    {
        $student->loadMissing('classRoom');

        return [
            'id' => $student->id,
            'nis' => $student->nis,
            'name' => $student->name,
            'class_id' => $student->class_id,
            'class_name' => $student->classRoom?->name,
            'gender' => $student->gender,
            'is_active' => $student->is_active,
        ];
    }
}
