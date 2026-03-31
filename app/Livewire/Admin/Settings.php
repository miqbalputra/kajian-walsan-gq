<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
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

    public bool $saved = false;

    public function mount(): void
    {
        $this->admin_whatsapp = Setting::get('admin_whatsapp', '6281234567890');
        $this->institution_name = Setting::get('institution_name', 'Kelompok Tahfidz Griya Qur\'an "Tunas Ilmu"');
        $this->app_name = Setting::get('app_name', 'Kajian Walsan');
    }

    public function save(): void
    {
        $this->validate([
            'admin_whatsapp' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'institution_name' => 'required|string|max:255',
            'app_name' => 'required|string|max:100',
        ], [
            'admin_whatsapp.required' => 'Nomor WhatsApp admin wajib diisi.',
            'admin_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'admin_whatsapp.min' => 'Nomor WhatsApp minimal 10 digit.',
            'admin_whatsapp.max' => 'Nomor WhatsApp maksimal 15 digit.',
            'institution_name.required' => 'Nama lembaga wajib diisi.',
            'app_name.required' => 'Nama aplikasi wajib diisi.',
        ]);

        Setting::set('admin_whatsapp', $this->admin_whatsapp);
        Setting::set('institution_name', $this->institution_name);
        Setting::set('app_name', $this->app_name);

        $this->saved = true;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pengaturan berhasil disimpan!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
