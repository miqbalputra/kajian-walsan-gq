<?php

namespace App\Livewire\WaliSantri;

use App\Models\ParentModel;
use App\Models\User;
use App\Services\ParentLoginAliasService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
        $isAllowed = collect($this->avatarPresets)->contains(
            fn (array $preset): bool => $preset['icon'] === $icon && $preset['color'] === $color
        );

        if (! $isAllowed) {
            return;
        }

        $this->avatar = "icon:{$icon}|{$color}";
    }

    public function updatedEmail(): void
    {
        $user = auth()->user();
        if (! $user?->isWaliSantri()) {
            return;
        }

        $this->validateOnly('email', [
            'email' => $this->emailRules($user),
        ], [
            'email.required' => 'Masukkan alamat Gmail yang aktif.',
            'email.email' => 'Format email belum benar.',
            'email.unique' => 'Alamat Gmail ini sudah digunakan akun lain.',
        ]);
    }

    private function emailRules(User $user): array
    {
        $rules = ['bail', 'required', 'email'];

        if ($user->isWaliSantri()) {
            $rules[] = function (string $attribute, mixed $value, \Closure $fail): void {
                if (! User::isGmailAddress((string) $value)) {
                    $fail('Email wali santri wajib menggunakan Gmail dengan akhiran @gmail.com.');
                }
            };
        }

        $rules[] = 'unique:users,email,'.$user->id;

        return $rules;
    }

    public function updateProfile()
    {
        $user = auth()->user();
        $this->email = Str::lower(trim((string) $this->email));

        if ($user->isWaliSantri()) {
            $this->username = $user->username;
        }

        if (! collect($this->avatarPresets)->contains(
            fn (array $preset): bool => "icon:{$preset['icon']}|{$preset['color']}" === $this->avatar
        )) {
            $this->avatar = 'icon:mosque|bg-indigo-500';
        }

        $messages = [
            'phone.regex' => 'Nomor telepon harus format Indonesia, contoh 081234567890 atau 6281234567890.',
            'email.required' => $user->isWaliSantri() ? 'Masukkan alamat Gmail yang aktif.' : 'Masukkan alamat email.',
            'email.email' => 'Format email belum benar.',
            'email.unique' => $user->isWaliSantri() ? 'Alamat Gmail ini sudah digunakan akun lain.' : 'Alamat email ini sudah digunakan akun lain.',
        ];

        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$user->id,
            'email' => $this->emailRules($user),
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)?[0-9]{8,13}$/'],
        ], $messages);

        $user->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
        ]);

        session()->flash('message', 'Profil berhasil diperbarui!');
        $this->dispatch('profile-updated');
    }

    public $showPasswordSuccessModal = false;

    public function logoutAfterPasswordChange()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        session()->flash('success', 'Silakan login kembali dengan password baru Anda.');

        return redirect()->route('login');
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
        app(ParentLoginAliasService::class)->syncPassword($user, $this->new_password);

        // Kirim Notifikasi ke n8n (WhatsApp) - Production Mode
        try {
            Http::post('https://automation.tunasilmu.com/webhook/jgduiwg8729782uhebdihb27', [
                'type' => 'password_changed_notification',
                'name' => $user->name,
                'phone' => preg_replace('/^0/', '62', $user->phone),
                'time' => now()->format('d M Y H:i'),
            ]);
        } catch (\Exception $e) {
            // Abaikan jika webhook gagal agar tidak menghambat user
        }

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        // Show success modal instead of immediate logout
        $this->showPasswordSuccessModal = true;
    }

    public function showCard()
    {
        $user = auth()->user();
        $parent = ParentModel::with('user', 'students.classRoom')
            ->where('user_id', $user->id)
            ->first();

        if (! $parent || $parent->isPureTeacher()) {
            return;
        }

        $parent->syncQrCode();

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd
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
