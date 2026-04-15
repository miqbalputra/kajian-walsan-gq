<?php

namespace App\Livewire\WaliSantri;

use App\Models\ParentModel;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Profile extends Component
{
    public $name;
    public $username;
    public $email;
    public $phone;
    public $avatar; // Ini akan menyimpan string seperti "icon:mosque|bg-indigo-500"

    // Card modal state
    public $showCardModal = false;
    public $qrCodeSvg = '';

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Daftar ikon yang tersedia (Benda/Abstrak saja)
    public $avatarPresets = [
        ['icon' => 'mosque', 'color' => 'bg-indigo-500'],
        ['icon' => 'menu_book', 'color' => 'bg-emerald-500'],
        ['icon' => 'auto_stories', 'color' => 'bg-blue-500'],
        ['icon' => 'lightbulb', 'color' => 'bg-amber-500'],
        ['icon' => 'verified', 'color' => 'bg-cyan-500'],
        ['icon' => 'stars', 'color' => 'bg-purple-500'],
        ['icon' => 'favorite', 'color' => 'bg-rose-500'],
        ['icon' => 'history_edu', 'color' => 'bg-teal-500'],
        ['icon' => 'school', 'color' => 'bg-orange-500'],
        ['icon' => 'diamond', 'color' => 'bg-slate-700'],
        ['icon' => 'auto_awesome', 'color' => 'bg-fuchsia-500'],
        ['icon' => 'import_contacts', 'color' => 'bg-lime-600'],
    ];

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->avatar = $user->avatar ?? 'icon:mosque|bg-indigo-500';
    }

    public function selectAvatar($icon, $color)
    {
        $this->avatar = "icon:{$icon}|{$color}";
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
        ]);

        session()->flash('message', 'Profil berhasil diperbarui!');
        $this->dispatch('profile-updated');
    }

    public function updatePassword()
    {
        $user = auth()->user();

        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        // Kirim Notifikasi ke n8n (WhatsApp)
        try {
            Http::post('https://automation.tunasilmu.com/webhook/jkbsabhjabchbc82u9u991', [
                'type' => 'password_changed_notification',
                'name' => $user->name,
                'phone' => $user->phone,
                'time' => now()->format('d M Y H:i')
            ]);
        } catch (\Exception $e) {
            // Abaikan jika webhook gagal agar tidak menghambat user
        }

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password-message', 'Password berhasil diperbarui!');
    }

    public function showCard()
    {
        $user = auth()->user();
        $parent = ParentModel::with('user', 'students.classRoom')
            ->where('user_id', $user->id)
            ->first();

        if (!$parent) {
            return;
        }

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $this->qrCodeSvg = $writer->writeString($parent->qr_code_string);
        $this->showCardModal = true;
    }

    public function render()
    {
        $user = auth()->user();
        $parentData = ParentModel::with('user', 'students.classRoom')
            ->where('user_id', $user->id)
            ->first();

        return view('livewire.wali-santri.profile', [
            'parentData' => $parentData,
        ])->layout('components.layouts.wali-santri', ['title' => 'Profil Saya']);
    }
}
