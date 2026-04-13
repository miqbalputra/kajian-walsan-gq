<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
            <p class="text-gray-500">Kelola semua akun login sistem (Admin, Panitia, Kepsek, Wali Kelas, Wali Santri)
            </p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors shadow-lg shadow-primary-500/25">
            <span class="material-symbols-rounded">add</span>
            Tambah User
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <span class="material-symbols-rounded">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <span
                        class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, username, atau email..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            <div class="sm:w-40">
                <select wire:model.live="roleFilter"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-36">
                <select wire:model.live="perPage"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="10">10 / hal</option>
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                    <option value="100">100 / hal</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Terakhir Dibuat</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="material-symbols-rounded text-primary-600">person</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-primary-600 font-semibold mb-1">@ {{ $user->username }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $user->role?->display_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($user->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                    @if($user->hasGoogleLinked())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-600 w-fit">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/></svg>
                                            Google
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $user->id }})"
                                        class="p-2 text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                        title="Edit User">
                                        <span class="material-symbols-rounded text-xl">edit_square</span>
                                    </button>
                                    @if($user->hasGoogleLinked())
                                        <button wire:click="unlinkGoogle({{ $user->id }})"
                                            wire:confirm="Yakin ingin melepaskan akun Google dari {{ $user->name }}?"
                                            class="p-2 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                            title="Lepas Akun Google">
                                            <span class="material-symbols-rounded text-xl">link_off</span>
                                        </button>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus User">
                                            <span class="material-symbols-rounded text-xl">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">group</span>
                                <p class="mt-2">Belum ada data user</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit User' : 'Tambah User' }}
                        </h3>
                        <button wire:click="$set('showModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Masukkan nama lengkap">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="username"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="cth: fulan123">
                                    @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" wire:model="email"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="nama@email.com">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="role_id"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="">Pilih Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                    <input type="text" wire:model="phone"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password
                                    {{ $editMode ? '(kosongkan jika tidak diubah)' : '' }}</label>
                                <input type="password" wire:model="password"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="{{ $editMode ? '••••••••' : 'Minimal 8 karakter' }}">
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="bg-gray-50 p-4 rounded-xl">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" wire:model="is_active"
                                        class="w-5 h-5 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                                    <div>
                                        <p class="font-semibold text-gray-900">User Aktif</p>
                                        <p class="text-sm text-gray-500">Hanya user aktif yang dapat masuk ke sistem.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                                {{ $editMode ? 'Simpan Perubahan' : 'Tambah User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showDeleteModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-rounded text-red-600 text-3xl">delete</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus User?</h3>
                    <p class="text-gray-500 mb-6">Akun dan data user akan dihapus permanen. Aksi ini tidak dapat dibatalkan.
                    </p>

                    <div class="flex gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>