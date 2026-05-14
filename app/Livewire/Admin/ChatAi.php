<?php

namespace App\Livewire\Admin;

use App\Services\AiProviderService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Chat AI')]
class ChatAi extends Component
{
    public string $question = '';
    public array $messages = [];

    public function mount(): void
    {
        $this->messages = [
            [
                'role' => 'assistant',
                'content' => 'Assalamu\'alaikum. Saya siap bantu membaca ringkasan data aplikasi. Saya bisa bantu cari presensi, status validasi, dan menampilkan link foto bukti/catatan/surat yang ada di database.',
            ],
        ];
    }

    public function ask(): void
    {
        $this->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = trim($this->question);
        $this->messages[] = ['role' => 'user', 'content' => $question];
        $this->question = '';

        try {
            $answer = app(AiProviderService::class)->chatWithDatabase($question);
        } catch (\Throwable $exception) {
            $answer = 'AI belum bisa menjawab: ' . $exception->getMessage();
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $answer ?: 'AI tidak memberi jawaban.'];
    }

    public function clearChat(): void
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.chat-ai');
    }
}
