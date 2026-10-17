<?php

namespace App\Livewire\WaliKelas;

use App\Livewire\WaliKelas\Concerns\WithAttendanceData;
use App\Models\GuardianFollowUpCase;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StudentProfile extends Component
{
    use WithAttendanceData, WithPagination;

    #[Locked]
    public int $studentId;

    #[Url(as: 'tab', history: true)]
    public string $tab = 'summary';

    #[Url(as: 'kembali')]
    public string $returnQuery = '';

    #[Url(as: 'asal')]
    public string $returnSource = 'dashboard';

    public ?int $followParentId = null;

    public ?int $followEventId = null;

    public string $followStatus = 'in_progress';

    public string $contactedAt = '';

    public string $channel = 'whatsapp';

    public string $result = '';

    public string $nextAction = '';

    public string $nextFollowUpAt = '';

    public function mount(int $student): void
    {
        $this->studentId = $student;
        $this->reportService()->authorizedStudent($this->studentId);
    }

    #[Computed]
    public function dataset(): array
    {
        return $this->reportService()->dataset($this->studentId);
    }

    public function selectTab(string $tab): void
    {
        $this->reportService()->authorizedStudent($this->studentId);
        abort_unless(in_array($tab, ['summary', 'history', 'followups'], true), 422);
        $this->tab = $tab;
        $this->resetPage();
    }

    public function openFollowUp(int $parentId, int $eventId): void
    {
        $data = $this->dataset;
        $key = $this->studentId.':'.$parentId.':'.$eventId;
        abort_unless($data['attention']->contains('key', $key) || $data['cases']->has($key), 404);
        $this->resetValidation();
        $this->reset(['result', 'nextAction']);
        $this->followParentId = $parentId;
        $this->followEventId = $eventId;
        $this->followStatus = $data['cases']->get($key)?->status ?? 'in_progress';
        $this->nextFollowUpAt = $data['cases']->get($key)?->next_follow_up_at?->format('Y-m-d') ?? '';
        $this->contactedAt = now()->format('Y-m-d\TH:i');
        $this->tab = 'followups';
        $this->dispatch('follow-up-opened');
    }

    public function closeFollowUp(): void
    {
        $this->reportService()->authorizedStudent($this->studentId);
        $this->reset(['followParentId', 'followEventId']);
        $this->resetValidation();
        $this->dispatch('follow-up-closed');
    }

    public function saveFollowUp(): void
    {
        $this->reportService()->authorizedStudent($this->studentId);
        $this->result = trim($this->result);
        if ($this->followStatus === 'completed') {
            $this->nextFollowUpAt = '';
        }
        $this->validate([
            'followParentId' => 'required|integer', 'followEventId' => 'required|integer',
            'followStatus' => 'required|in:open,in_progress,completed',
            'contactedAt' => 'required|date|before_or_equal:now', 'channel' => 'required|in:whatsapp,phone,meeting,note,other',
            'result' => 'required|string|min:3|max:2000', 'nextAction' => 'nullable|string|max:2000',
            'nextFollowUpAt' => 'nullable|date|after_or_equal:'.substr($this->contactedAt, 0, 10),
        ], ['result.required' => 'Tuliskan hasil komunikasi, termasuk saat menyelesaikan kasus.', 'result.min' => 'Hasil komunikasi minimal 3 karakter.',
            'contactedAt.before_or_equal' => 'Waktu komunikasi tidak boleh di masa depan.', 'nextFollowUpAt.after_or_equal' => 'Tenggat tidak boleh sebelum tanggal komunikasi.']);
        $this->reportService()->saveFollowUp($this->studentId, $this->followParentId, $this->followEventId, [
            'status' => $this->followStatus, 'contacted_at' => $this->contactedAt, 'channel' => $this->channel,
            'result' => trim($this->result), 'next_action' => trim($this->nextAction), 'next_follow_up_at' => $this->nextFollowUpAt,
        ]);
        unset($this->dataset);
        $this->closeFollowUp();
        session()->flash('follow_up_saved', 'Catatan tindak lanjut berhasil disimpan. Data presensi tetap tersimpan seperti sebelumnya.');
    }

    public function backUrl(): string
    {
        parse_str($this->returnQuery, $query);
        $query = array_intersect_key($query, array_flip(['tahun', 'kajian', 'status', 'q', 'page', 'attentionPage']));
        $query = array_filter($query, fn ($value) => is_scalar($value));

        return route($this->returnSource === 'reports' ? 'wali-kelas.reports' : 'wali-kelas.dashboard', $query);
    }

    public function render()
    {
        $data = $this->dataset;
        $student = $data['students']->first();
        $groups = $this->filteredRows()->groupBy('event_id')->values();

        return view('livewire.wali-kelas.student-profile', $data + [
            'student' => $student, 'historyPage' => $this->paginateRows($groups, 10),
            'summary' => $this->reportService()->summary($data['rows']->where('event_state', 'ended')),
            'caseStatuses' => GuardianFollowUpCase::STATUSES, 'channels' => GuardianFollowUpCase::CHANNELS,
        ])->layout('components.layouts.wali-kelas', ['title' => 'Riwayat santri']);
    }
}
