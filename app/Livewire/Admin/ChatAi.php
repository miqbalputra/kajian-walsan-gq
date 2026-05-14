<?php

namespace App\Livewire\Admin;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
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
    public array $sessions = [];
    public ?int $activeSessionId = null;
    public string $activeSessionTitle = 'Chat baru';
    public bool $fullscreen = false;

    public function mount(): void
    {
        $this->loadSessions();

        if ($this->activeSessionId === null) {
            $latest = AiChatSession::where('user_id', auth()->id())
                ->latest('last_accessed_at')
                ->latest()
                ->first();

            $latest ? $this->selectSession($latest->id) : $this->newSession();
        }
    }

    public function ask(?string $text = null): void
    {
        if ($text !== null) {
            $this->question = trim($text);
        }

        $this->validate([
            'question' => 'required|string|max:1000',
        ]);

        $session = $this->activeSession();
        $question = trim($this->question);
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $question,
        ]);

        if ($session->title === 'Chat baru') {
            $session->update(['title' => str($question)->limit(55, '')->toString()]);
        }

        $this->messages[] = ['role' => 'user', 'content' => $question];
        $this->question = '';

        try {
            $answer = app(AiProviderService::class)->chatWithDatabase($question, $this->messages);
        } catch (\Throwable $exception) {
            $answer = 'AI belum bisa menjawab: ' . $exception->getMessage();
        }

        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $answer ?: 'AI tidak memberi jawaban.',
        ]);

        $session->touchAccessed();
        $this->messages[] = ['role' => 'assistant', 'content' => $answer ?: 'AI tidak memberi jawaban.'];
        $this->loadSessions();
        $this->activeSessionTitle = $session->fresh()->title;
    }

    public function newSession(): void
    {
        $session = AiChatSession::create([
            'user_id' => auth()->id(),
            'title' => 'Chat baru',
            'last_accessed_at' => now(),
        ]);

        $this->selectSession($session->id);
    }

    public function selectSession(int $sessionId): void
    {
        $session = AiChatSession::where('user_id', auth()->id())->findOrFail($sessionId);
        $session->touchAccessed();

        $this->activeSessionId = $session->id;
        $this->activeSessionTitle = $session->title;
        $this->question = '';
        $this->messages = $session->messages()
            ->oldest()
            ->get(['role', 'content'])
            ->map(fn (AiChatMessage $message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->all();

        $this->loadSessions();
    }

    public function toggleFullscreen(): void
    {
        $this->fullscreen = ! $this->fullscreen;
    }

    protected function activeSession(): AiChatSession
    {
        if ($this->activeSessionId) {
            return AiChatSession::where('user_id', auth()->id())->findOrFail($this->activeSessionId);
        }

        $this->newSession();

        return AiChatSession::where('user_id', auth()->id())->findOrFail($this->activeSessionId);
    }

    protected function loadSessions(): void
    {
        $this->sessions = AiChatSession::where('user_id', auth()->id())
            ->withCount('messages')
            ->latest('last_accessed_at')
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn (AiChatSession $session) => [
                'id' => $session->id,
                'title' => $session->title,
                'messages_count' => $session->messages_count,
                'last_accessed' => optional($session->last_accessed_at)->translatedFormat('d M Y H:i'),
            ])
            ->all();
    }

    public function render()
    {
        return view('livewire.admin.chat-ai');
    }
}
