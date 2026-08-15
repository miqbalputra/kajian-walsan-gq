<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PublicAttendanceLink;
use App\Services\PublicMustawaOneAttendanceService;
use App\Services\PublicMustawaOneFormException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicMustawaOneAttendanceController extends Controller
{
    public function __construct(private PublicMustawaOneAttendanceService $attendanceService)
    {
    }

    public function show(string $token): View
    {
        $link = $this->activeLink($token);
        $availability = $this->attendanceService->availability();

        return view('public.mustawa-one-attendance', [
            'link' => $link,
            'availability' => $availability,
            'students' => $availability['available']
                ? $this->attendanceService->eligibleStudentOptions()
                : collect(),
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $this->activeLink($token);

        $validated = $request->validate([
            'student_id' => ['required', 'integer'],
            'parent_type' => ['required', Rule::in(['father', 'mother'])],
            'status' => ['required', Rule::in([Attendance::STATUS_HADIR_ONLINE, Attendance::STATUS_IZIN])],
            'proof_file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500', 'required_if:status,'.Attendance::STATUS_IZIN],
            'website' => ['nullable', 'max:0'],
        ], [
            'proof_file.required' => 'Foto bukti wajib diupload.',
            'proof_file.image' => 'Bukti harus berupa foto JPG atau PNG.',
            'proof_file.mimes' => 'Format foto harus JPG atau PNG.',
            'proof_file.max' => 'Ukuran foto maksimal 2 MB.',
            'notes.required_if' => 'Alasan izin wajib diisi.',
        ]);

        // Bots commonly fill every input. Return a neutral response without
        // storing a record or revealing the form's protection mechanism.
        if (! empty($validated['website'] ?? null)) {
            return redirect()->route('public.mustawa-one-form.show', ['token' => $token])
                ->with('success', 'Pengajuan diterima. Panitia akan melakukan pemeriksaan.');
        }

        try {
            $this->attendanceService->submit($validated, $request->file('proof_file'));
        } catch (PublicMustawaOneFormException $exception) {
            return back()->withInput()->withErrors(['form' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            Log::error('[Public Mustawa 1 Form] Submission failed', [
                'ip' => $request->ip(),
                'student_id' => $validated['student_id'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'form' => 'Bukti belum dapat dikirim. Silakan periksa koneksi lalu coba kembali.',
            ]);
        }

        return redirect()->route('public.mustawa-one-form.show', ['token' => $token])
            ->with('success', 'Bukti berhasil dikirim dan sedang diperiksa oleh sistem/panitia.');
    }

    private function activeLink(string $token): PublicAttendanceLink
    {
        return PublicAttendanceLink::query()
            ->active()
            ->forMustawaOneNew()
            ->where('token', $token)
            ->firstOrFail();
    }
}
