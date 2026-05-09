<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use App\Services\WebPushService;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementIndex extends Component
{
    use WithPagination;

    public string $title = '';
    public string $body = '';
    public string $url = '/wali-santri';

    protected array $rules = [
        'title' => 'required|string|max:120',
        'body' => 'required|string|max:500',
        'url' => 'required|string|max:255',
    ];

    public function send(): void
    {
        $this->validate();

        $result = app(WebPushService::class)->sendToAllWali(
            $this->title,
            $this->body,
            $this->url
        );

        Announcement::create([
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'sent_by' => auth()->id(),
            'sent_count' => $result['sent'],
            'failed_count' => $result['failed'],
        ]);

        $this->reset(['title', 'body']);
        $this->url = '/wali-santri';
        $this->resetPage();

        $this->dispatch('notify', [
            'type' => $result['sent'] > 0 ? 'success' : 'warning',
            'message' => "Pengumuman dikirim ke {$result['sent']} perangkat. Gagal: {$result['failed']}."
        ]);
    }

    public function render()
    {
        return view('livewire.admin.announcement-index', [
            'announcements' => Announcement::with('sender')
                ->latest()
                ->paginate(10),
        ])->layout('components.layouts.admin', ['title' => 'Pengumuman Push']);
    }
}
