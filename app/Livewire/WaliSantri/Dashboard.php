<?php

namespace App\Livewire\WaliSantri;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Services\CloudinaryService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    public $showQrModal = false;
    public $showOnlineModal = false;
    public $showIzinModal = false;
    public $showReuploadModal = false;
    public $showFeedbackModal = false;
    public $isUsingDefaultPassword = false;

    public function mount()
    {
        $user = auth()->user();
        if (\Illuminate\Support\Facades\Hash::check($user->username, $user->password)) {
            $this->isUsingDefaultPassword = true;
        }
    }

    public $feedbackEventId = null;
    public $ratingMateri = 0;
    public $ratingOperasional = 0;
    public $feedbackComment = '';
    public $proofPhoto = null;
    public $izinDocument = null;
    public $reuploadFile = null;
    public $reuploadAttendanceId = null;
    public $reuploadIsPendingReplace = false; // true jika mengganti foto pending (bukan rejected)
    public $notes = '';

    protected $rules = [
        'proofPhoto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'notes' => 'nullable|string|max:500',
    ];

    public function getParentProperty()
    {
        return ParentModel::where('user_id', auth()->id())->first();
    }

    public function getActiveEventProperty()
    {
        return KajianEvent::where('status', 'open')
            ->whereDate('date', today())
            ->first();
    }

    public function getQrCodeSvgProperty()
    {
        if (!$this->parent) {
            return '';
        }

        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($this->parent->qr_code_string);
    }

    public function getMyAttendanceTodayProperty()
    {
        if (!$this->parent || !$this->activeEvent) {
            return null;
        }

        return Attendance::where('kajian_event_id', $this->activeEvent->id)
            ->where('parent_id', $this->parent->id)
            ->first();
    }

    /**
     * Ambil semua attendance yang ditolak (untuk re-upload).
     */
    public function getRejectedAttendancesProperty()
    {
        if (!$this->parent) {
            return collect();
        }

        return Attendance::with('kajianEvent')
            ->where('parent_id', $this->parent->id)
            ->where('validation_status', 'rejected')
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getAttendanceHistoryProperty()
    {
        if (!$this->parent) {
            return collect();
        }

        return Attendance::with('kajianEvent')
            ->where('parent_id', $this->parent->id)
            ->whereHas('kajianEvent', function ($q) {
                $q->whereDate('date', '<=', today());
            })
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
    }

    /**
     * Get past events that the parent attended but hasn't given feedback for yet.
     */
    public function getPendingFeedbackEventsProperty()
    {
        if (!$this->parent) {
            return collect();
        }

        // Get attended events that are either in the past or closed
        return KajianEvent::whereHas('attendances', function ($q) {
            $q->where('parent_id', $this->parent->id)
                ->whereIn('status', ['hadir_fisik', 'hadir_online', 'izin']);
        })
            ->where(function ($query) {
                $query->where('date', '<', today())
                    ->orWhere('status', 'closed');
            })
            ->whereDoesntHave('feedbacks', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Cari event terdekat yang akan datang (minimal besok).
     */
    public function getUpcomingEventProperty()
    {
        return KajianEvent::where('date', '>', today())
            ->whereIn('status', ['draft', 'open', 'ongoing']) // draft di masa depan dianggap scheduled
            ->orderBy('date')
            ->orderBy('time_start')
            ->first();
    }

    public function submitOnlineAttendance()
    {
        $this->validate([
            'proofPhoto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (!$this->activeEvent || !$this->parent) {
            session()->flash('error', 'Tidak dapat melakukan presensi.');
            return;
        }

        // Check if already attended
        if ($this->myAttendanceToday) {
            session()->flash('error', 'Anda sudah tercatat pada kajian ini.');
            $this->showOnlineModal = false;
            return;
        }

        // Upload via CloudinaryService (or local fallback)
        $cloudinary = app(CloudinaryService::class);
        $result = $cloudinary->upload($this->proofPhoto, 'attendance-proofs');
        $path = $result['url'];

        // Create attendance
        Attendance::create([
            'kajian_event_id' => $this->activeEvent->id,
            'parent_id' => $this->parent->id,
            'student_id' => $this->parent->students()->first()?->id,
            'status' => 'hadir_online',
            'method' => 'upload',
            'validation_status' => 'pending',
            'proof_file' => $path,
            'notes' => $this->notes,
        ]);

        $this->showOnlineModal = false;
        $this->reset(['proofPhoto', 'notes']);
        session()->flash('message', 'Presensi online berhasil dikirim. Menunggu validasi.');
    }

    public function submitIzin()
    {
        $this->validate([
            'izinDocument' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'required|string|max:500',
        ]);

        if (!$this->activeEvent || !$this->parent) {
            session()->flash('error', 'Tidak dapat mengirim izin.');
            return;
        }

        // Check if already attended
        if ($this->myAttendanceToday) {
            session()->flash('error', 'Anda sudah tercatat pada kajian ini.');
            $this->showIzinModal = false;
            return;
        }

        // Upload via CloudinaryService (or local fallback)
        $cloudinary = app(CloudinaryService::class);
        $result = $cloudinary->upload($this->izinDocument, 'izin-documents');
        $path = $result['url'];

        // Create attendance with izin status
        Attendance::create([
            'kajian_event_id' => $this->activeEvent->id,
            'parent_id' => $this->parent->id,
            'student_id' => $this->parent->students()->first()?->id,
            'status' => 'izin',
            'method' => 'upload',
            'validation_status' => 'pending',
            'proof_file' => $path,
            'notes' => $this->notes,
        ]);

        $this->showIzinModal = false;
        $this->reset(['izinDocument', 'notes']);
        session()->flash('message', 'Izin berhasil dikirim. Menunggu validasi.');
    }

    /**
     * Buka modal re-upload untuk attendance yang ditolak.
     */
    public function openReuploadModal($attendanceId, bool $isPendingReplace = false)
    {
        $this->reuploadAttendanceId = $attendanceId;
        $this->reuploadFile = null;
        $this->reuploadIsPendingReplace = $isPendingReplace;
        $this->showReuploadModal = true;
    }

    /**
     * Submit file ulang untuk attendance yang ditolak ATAU ganti foto yang masih pending.
     */
    public function reuploadProof()
    {
        $this->validate([
            'reuploadFile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'reuploadFile.required' => 'File bukti wajib diupload.',
            'reuploadFile.mimes'    => 'Format file harus JPG atau PNG.',
            'reuploadFile.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        // Boleh replace jika status rejected ATAU pending (belum diapprove)
        $attendance = Attendance::where('id', $this->reuploadAttendanceId)
            ->where('parent_id', $this->parent->id)
            ->whereIn('validation_status', ['rejected', 'pending'])
            ->first();

        if (!$attendance) {
            session()->flash('error', 'Data presensi tidak ditemukan atau sudah divalidasi.');
            $this->showReuploadModal = false;
            return;
        }

        // Hapus foto lama dari Cloudinary (jika ada)
        if ($attendance->proof_file) {
            $this->deleteOldProofFile($attendance->proof_file);
        }

        // Upload file baru via Cloudinary/local
        $cloudinary = app(CloudinaryService::class);
        $folder = $attendance->status === 'izin' ? 'izin-documents' : 'attendance-proofs';
        $result  = $cloudinary->upload($this->reuploadFile, $folder);
        $path    = $result['url'];

        // Update record
        $attendance->update([
            'proof_file'       => $path,
            'validation_status' => 'pending', // reset ke pending (baik dari rejected maupun replace)
            'rejection_reason' => null,
            'validated_by'     => null,
            'validated_at'     => null,
        ]);

        $this->showReuploadModal = false;
        $this->reset(['reuploadFile', 'reuploadAttendanceId', 'reuploadIsPendingReplace']);

        $message = $this->reuploadIsPendingReplace
            ? 'Foto bukti berhasil diganti. Menunggu validasi admin.'
            : 'Bukti berhasil diupload ulang. Menunggu validasi admin.';
        session()->flash('message', $message);
    }

    /**
     * Hapus file lama dari Cloudinary (jika URL Cloudinary).
     * Untuk local file, biarkan saja (tidak kritis).
     */
    protected function deleteOldProofFile(string $url): void
    {
        if (!CloudinaryService::isCloudinaryUrl($url)) {
            return;
        }

        // Extract public_id dari Cloudinary URL
        // Format: https://res.cloudinary.com/{cloud}/image/upload/v{ver}/{public_id}.{ext}
        if (preg_match('#/upload/(?:v\d+/)?(.+?)(?:\.[a-z0-9]+)?$#i', $url, $m)) {
            $publicId = $m[1];
            app(CloudinaryService::class)->delete($publicId);
        }
    }

    public function openFeedbackModal($eventId)
    {
        $this->feedbackEventId = $eventId;
        $this->ratingMateri = 0;
        $this->ratingOperasional = 0;
        $this->feedbackComment = '';
        $this->showFeedbackModal = true;
    }

    public function submitFeedback()
    {
        $this->validate([
            'ratingMateri' => 'required|integer|min:1|max:5',
            'ratingOperasional' => 'required|integer|min:1|max:5',
            'feedbackComment' => 'nullable|string|max:1000',
        ], [
            'ratingMateri.min' => 'Beri penilaian untuk Isi Materi.',
            'ratingOperasional.min' => 'Beri penilaian untuk Kualitas Teknis/Fasilitas.',
        ]);

        $overallRating = round(($this->ratingMateri + $this->ratingOperasional) / 2, 1);

        \App\Models\KajianFeedback::updateOrCreate(
            ['kajian_event_id' => $this->feedbackEventId, 'user_id' => auth()->id()],
            [
                'rating' => $overallRating,
                'comment' => $this->feedbackComment,
                'extra_feedback' => [
                    'materi' => $this->ratingMateri,
                    'operasional' => $this->ratingOperasional,
                ]
            ]
        );

        $this->showFeedbackModal = false;
        $this->reset(['ratingMateri', 'ratingOperasional', 'feedbackComment', 'feedbackEventId']);
        session()->flash('message', 'Terima kasih atas masukan Anda!');
    }
    public function render()
    {
        return view('livewire.wali-santri.dashboard')
            ->layout('components.layouts.wali-santri', ['title' => 'Dashboard']);
    }
}
