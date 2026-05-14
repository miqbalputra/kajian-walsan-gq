<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div
                class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg">
                <span class="material-symbols-rounded text-white text-2xl">settings</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pengaturan Aplikasi</h1>
                <p class="text-gray-500 text-sm">Kelola konfigurasi dan preferensi aplikasi</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if($saved)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <span class="material-symbols-rounded text-green-600">check_circle</span>
            <p class="text-green-700 font-medium">Pengaturan berhasil disimpan!</p>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <!-- WhatsApp Settings Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-white text-2xl">chat</span>
                    <div>
                        <h2 class="text-lg font-bold text-white">Pengaturan WhatsApp</h2>
                        <p class="text-green-100 text-sm">Konfigurasi integrasi WhatsApp admin</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label for="admin_whatsapp" class="block text-sm font-bold text-gray-700 mb-2">
                        Nomor WhatsApp Admin
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <span class="material-symbols-rounded">phone</span>
                        </span>
                        <input type="text" id="admin_whatsapp" wire:model="admin_whatsapp"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all outline-none text-gray-900 font-medium"
                            placeholder="628xxxxxxxxxx">
                    </div>
                    <p class="text-gray-500 text-xs mt-2">
                        Format: 628xxxxxxxxxx (tanpa tanda + atau spasi). Contoh: 6281234567890
                    </p>
                    @error('admin_whatsapp')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-amber-600 mt-0.5">info</span>
                        <div>
                            <p class="text-amber-800 text-sm font-medium">Informasi</p>
                            <p class="text-amber-700 text-xs mt-1">
                                Nomor ini akan menerima pesan WhatsApp ketika wali santri meminta reset password.
                                Pastikan nomor yang Anda masukkan adalah nomor yang aktif dan dapat menerima pesan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Settings Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-primary-500 to-primary-600">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-white text-2xl">business</span>
                    <div>
                        <h2 class="text-lg font-bold text-white">Pengaturan Umum</h2>
                        <p class="text-primary-100 text-sm">Informasi dasar aplikasi dan lembaga</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="app_name" class="block text-sm font-bold text-gray-700 mb-2">
                        Nama Aplikasi
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <span class="material-symbols-rounded">apps</span>
                        </span>
                        <input type="text" id="app_name" wire:model="app_name"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                            placeholder="Nama Aplikasi">
                    </div>
                    @error('app_name')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution_name" class="block text-sm font-bold text-gray-700 mb-2">
                        Nama Lembaga
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <span class="material-symbols-rounded">mosque</span>
                        </span>
                        <input type="text" id="institution_name" wire:model="institution_name"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                            placeholder="Nama Lembaga">
                    </div>
                    <p class="text-gray-500 text-xs mt-2">
                        Nama lembaga yang akan ditampilkan di halaman depan dan footer.
                    </p>
                    @error('institution_name')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- AI Settings Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-violet-600">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-white text-2xl">psychology</span>
                    <div>
                        <h2 class="text-lg font-bold text-white">Pengaturan AI</h2>
                        <p class="text-indigo-100 text-sm">Konfigurasi LLM untuk validasi bukti dan Chat AI</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <label class="flex items-center justify-between gap-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                    <div>
                        <p class="font-bold text-gray-900">Aktifkan AI Review Otomatis</p>
                        <p class="text-xs text-gray-500 mt-1">Jika aktif, upload catatan kajian dan surat izin akan dicek AI. AI hanya auto-approve jika yakin.</p>
                    </div>
                    <input type="checkbox" wire:model="ai_enabled"
                        class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </label>

                <div>
                    <label for="ai_endpoint_url" class="block text-sm font-bold text-gray-700 mb-2">
                        URL Endpoint AI
                    </label>
                    <input type="url" id="ai_endpoint_url" wire:model="ai_endpoint_url"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-gray-900 font-medium"
                        placeholder="https://api.openai.com/v1">
                    <p class="text-gray-500 text-xs mt-2">Gunakan endpoint OpenAI-compatible. Contoh: https://api.openai.com/v1 atau endpoint provider LLM lain.</p>
                    @error('ai_endpoint_url')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ai_api_key" class="block text-sm font-bold text-gray-700 mb-2">
                        API Key AI
                    </label>
                    <input type="password" id="ai_api_key" wire:model="ai_api_key"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-gray-900 font-medium"
                        placeholder="{{ $has_ai_api_key ? 'API key sudah tersimpan. Isi untuk mengganti.' : 'Masukkan API key' }}">
                    <p class="text-gray-500 text-xs mt-2">
                        API key disimpan terenkripsi dan tidak akan ditampilkan ulang.
                    </p>
                    @error('ai_api_key')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" wire:click="loadAiModels"
                        class="px-5 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-black transition-all inline-flex items-center justify-center gap-2"
                        wire:loading.attr="disabled" wire:target="loadAiModels">
                        <span class="material-symbols-rounded" wire:loading.remove wire:target="loadAiModels">sync</span>
                        <span class="material-symbols-rounded animate-spin" wire:loading wire:target="loadAiModels">progress_activity</span>
                        Muat List Model
                    </button>
                    <div class="flex-1">
                        <label for="ai_model" class="sr-only">Model AI</label>
                        @if(count($ai_models) > 0)
                            <select id="ai_model" wire:model="ai_model"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-gray-900 font-medium">
                                @foreach($ai_models as $model)
                                    <option value="{{ $model }}">{{ $model }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" id="ai_model" wire:model="ai_model"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-gray-900 font-medium"
                                placeholder="Nama model, contoh: gpt-4o-mini">
                        @endif
                    </div>
                </div>
                @if($aiModelMessage)
                    <p class="text-xs {{ str_contains(strtolower($aiModelMessage), 'berhasil') ? 'text-green-600' : 'text-amber-600' }} font-medium">
                        {{ $aiModelMessage }}
                    </p>
                @endif
                @error('ai_model')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror

                <div>
                    <label for="ai_auto_approve_min_confidence" class="block text-sm font-bold text-gray-700 mb-2">
                        Ambang Auto-Approve AI: {{ $ai_auto_approve_min_confidence }}%
                    </label>
                    <input type="range" id="ai_auto_approve_min_confidence" wire:model.live="ai_auto_approve_min_confidence"
                        min="50" max="100" step="5" class="w-full accent-indigo-600">
                    <p class="text-gray-500 text-xs mt-2">Rekomendasi: 80-90%. Jika AI di bawah ambang ini, presensi tetap pending untuk dicek admin.</p>
                    @error('ai_auto_approve_min_confidence')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit"
                class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl
                           hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-500/30
                           active:scale-[0.98] transition-all duration-200 flex items-center gap-2 shadow-lg shadow-primary-600/20"
                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait">
                <span class="material-symbols-rounded" wire:loading.remove>save</span>
                <span class="material-symbols-rounded animate-spin" wire:loading>progress_activity</span>
                <span wire:loading.remove>Simpan Pengaturan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
