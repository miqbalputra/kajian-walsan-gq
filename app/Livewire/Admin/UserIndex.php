<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $perPage = 10;

    // Modal state
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $userId = null;

    // Form fields
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $phone = '';
    public $role_id = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'username', 'email', 'password', 'phone', 'role_id', 'is_active', 'editMode', 'userId']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role_id = $user->role_id;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Validate username uniqueness
        $usernameQuery = User::where('username', $this->username);
        $emailQuery = User::where('email', $this->email);

        if ($this->editMode) {
            $usernameQuery->where('id', '!=', $this->userId);
            $emailQuery->where('id', '!=', $this->userId);
        }

        if ($usernameQuery->exists()) {
            $this->addError('username', 'Username sudah digunakan.');
            return;
        }

        if ($emailQuery->exists()) {
            $this->addError('email', 'Email sudah digunakan.');
            return;
        }

        if ($this->editMode) {
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone,
                'role_id' => $this->role_id,
                'is_active' => $this->is_active,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            $this->dispatch('notify', ['type' => 'success', 'message' => 'User berhasil diperbarui!']);
        } else {
            User::create([
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->password ?: 'password'),
                'phone' => $this->phone,
                'role_id' => $this->role_id,
                'is_active' => $this->is_active,
            ]);

            $this->dispatch('notify', ['type' => 'success', 'message' => 'User berhasil ditambahkan!']);
        }

        $this->showModal = false;
        $this->reset(['name', 'username', 'email', 'password', 'phone', 'role_id', 'is_active', 'editMode', 'userId']);
    }

    public function confirmDelete($id)
    {
        if ($id === auth()->id()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak dapat menghapus akun sendiri!']);
            return;
        }
        $this->userId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $user = User::with(['parentProfile.attendances', 'role'])->findOrFail($this->userId);

        // PROTEKSI: Blokir hapus wali santri yang punya riwayat presensi
        if ($user->isWaliSantri() && $user->parentProfile) {
            $attendanceCount = $user->parentProfile->attendances()->count();
            if ($attendanceCount > 0) {
                $this->showDeleteModal = false;
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "Tidak bisa dihapus! {$user->name} memiliki {$attendanceCount} riwayat presensi. Gunakan fitur Nonaktifkan saja."
                ]);
                return;
            }
        }

        $user->delete();

        $this->showDeleteModal = false;
        $this->userId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'User berhasil dihapus!']);
    }

    /**
     * Admin: lepaskan kaitan akun Google dari user.
     * Berguna jika HP hilang atau user minta reset.
     */
    public function unlinkGoogle($id)
    {
        $user = User::findOrFail($id);

        if (!$user->hasGoogleLinked()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'User ini belum menautkan akun Google.']);
            return;
        }

        $user->update(['google_id' => null, 'google_token' => null]);

        $this->dispatch('notify', ['type' => 'success', 'message' => "Akun Google {$user->name} berhasil dilepaskan. User bisa menautkan ulang."]);
    }

    public function render()
    {
        $query = User::with('role')
            ->where(function ($query) {
                if ($this->search) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                }
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role_id', $this->roleFilter);
            })
            ->orderBy('created_at', 'desc');

        // Handle "all" option
        if ($this->perPage === 'all') {
            $users = $query->get();
        } else {
            $users = $query->paginate((int) $this->perPage);
        }

        $roles = Role::all();

        return view('livewire.admin.user-index', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('components.layouts.admin', ['title' => 'Manajemen User']);
    }
}
