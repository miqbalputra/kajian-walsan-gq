<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Services\AiProviderService;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Pengaturan Aplikasi')]
class Settings extends Component
{
    // WhatsApp Settings
    public string $admin_whatsapp = '';

    // General Settings
    public string $institution_name = '';
    public string $app_name = '';

    // AI Settings
    public bool $ai_enabled = false;
    public string $ai_endpoint_url = '';
    public string $ai_api_key = '';
    public string $ai_model = '';
    public int $ai_auto_approve_min_confidence = 80;
    public bool $has_ai_api_key = false;
    public array $ai_models = [];
    public ?string $aiModelMessage = null;

    public bool $saved = false;

    public function mount(): void
    {
        $this->admin_whatsapp = Setting::get('admin_whatsapp', '6281234567890');
        $this->institution_name = Setting::get('institution_name', 'Kelompok Tahfidz Griya Qur\'an "Tunas Ilmu"');
        $this->app_name = Setting::get('app_name', 'Presensi Wali Santri');
        $this->ai_enabled = (bool) Setting::get('ai_enabled', false);
        $this->ai_endpoint_url = Setting::get('ai_endpoint_url', '');
        $this->ai_model = Setting::get('ai_model', '');
        $this->ai_auto_approve_min_confidence = (int) Setting::get('ai_auto_approve_min_confidence', 80);
        $this->has_ai_api_key = filled(Setting::get('ai_api_key_encrypted'));

        if ($this->ai_model) {
            $this->ai_models = [$this->ai_model];
        }
    }

    public function save(): void
    {
        $this->validate([
            'admin_whatsapp' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'institution_name' => 'required|string|max:255',
            'app_name' => 'required|string|max:100',
            'ai_enabled' => 'boolean',
            'ai_endpoint_url' => 'nullable|url|max:255',
            'ai_api_key' => 'nullable|string|max:1000',
            'ai_model' => 'nullable|string|max:150',
            'ai_auto_approve_min_confidence' => 'required|integer|min:50|max:100',
        ], [
            'admin_whatsapp.required' => 'Nomor WhatsApp admin wajib diisi.',
            'admin_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'admin_whatsapp.min' => 'Nomor WhatsApp minimal 10 digit.',
            'admin_whatsapp.max' => 'Nomor WhatsApp maksimal 15 digit.',
            'institution_name.required' => 'Nama lembaga wajib diisi.',
            'app_name.required' => 'Nama aplikasi wajib diisi.',
        ]);

        if ($this->ai_enabled) {
            if (blank($this->ai_endpoint_url)) {
                $this->addError('ai_endpoint_url', 'Endpoint AI wajib diisi saat AI diaktifkan.');
                return;
            }

            if (! $this->has_ai_api_key && blank($this->ai_api_key)) {
                $this->addError('ai_api_key', 'API key AI wajib diisi saat AI diaktifkan.');
                return;
            }

            if (blank($this->ai_model)) {
                $this->addError('ai_model', 'Pilih atau isi model AI saat AI diaktifkan.');
                return;
            }
        }

        Setting::set('admin_whatsapp', $this->admin_whatsapp);
        Setting::set('institution_name', $this->institution_name);
        Setting::set('app_name', $this->app_name);
        Setting::set('ai_enabled', $this->ai_enabled ? '1' : '0');
        Setting::set('ai_endpoint_url', rtrim($this->ai_endpoint_url, '/'));
        Setting::set('ai_model', $this->ai_model);
        Setting::set('ai_auto_approve_min_confidence', (string) $this->ai_auto_approve_min_confidence);

        if (filled($this->ai_api_key)) {
            Setting::set('ai_api_key_encrypted', Crypt::encryptString($this->ai_api_key));
            $this->ai_api_key = '';
            $this->has_ai_api_key = true;
        }

        $this->saved = true;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pengaturan berhasil disimpan!'
        ]);
    }

    public function loadAiModels(): void
    {
        $this->validate([
            'ai_endpoint_url' => 'required|url|max:255',
            'ai_api_key' => $this->has_ai_api_key ? 'nullable|string|max:1000' : 'required|string|max:1000',
        ], [
            'ai_endpoint_url.required' => 'Endpoint AI wajib diisi.',
            'ai_api_key.required' => 'API key wajib diisi untuk mengambil model.',
        ]);

        try {
            $apiKey = filled($this->ai_api_key) ? $this->ai_api_key : null;
            $this->ai_models = app(AiProviderService::class)->listModels($this->ai_endpoint_url, $apiKey);
            $this->aiModelMessage = count($this->ai_models) > 0
                ? 'Model berhasil dimuat.'
                : 'Endpoint tersambung, tetapi tidak ada model yang dikembalikan.';

            if (! $this->ai_model && count($this->ai_models) > 0) {
                $this->ai_model = $this->ai_models[0];
            }
        } catch (\Throwable $exception) {
            $this->ai_models = $this->ai_model ? [$this->ai_model] : [];
            $this->aiModelMessage = $exception->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
