<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kenaikan Kelas & Kelulusan</h1>
            <p class="text-gray-500">Pindahkan siswa ke tahun ajaran baru tanpa menghapus histori.</p>
        </div>
        @if($step > 1)
            <button wire:click="resetWizard" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold hover:bg-gray-50">
                <span class="material-symbols-rounded">restart_alt</span> Mulai Ulang
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('message') }}</div>
    @endif

    @if($latestBatch)
        <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-slate-700"><span class="font-semibold">Batch terakhir:</span> {{ $latestBatch->sourceAcademicYear?->name }} → {{ $latestBatch->targetAcademicYear?->name }} · diterapkan {{ $latestBatch->applied_at?->format('d/m/Y H:i') }}</div>
            <button wire:click="rollbackBatch({{ $latestBatch->id }})" wire:confirm="Rollback akan mengembalikan kelas dan status siswa seperti sebelum batch terakhir. Lanjutkan?" class="rounded-xl border border-red-200 bg-white px-4 py-2.5 font-semibold text-red-600 hover:bg-red-50">Rollback Batch Terakhir</button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        @foreach(['Tahun Ajaran', 'Mapping Kelas', 'Preview & Konfirmasi'] as $index => $label)
            <div class="rounded-xl border p-4 {{ $step === $index + 1 ? 'border-primary-500 bg-primary-50' : ($step > $index + 1 ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-white') }}">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $step > $index + 1 ? 'bg-green-500 text-white' : ($step === $index + 1 ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-500') }}">{{ $index + 1 }}</span>
                    <span class="font-semibold text-gray-800">{{ $label }}</span>
                </div>
            </div>
        @endforeach
    </div>

    @if($step === 1)
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">Tentukan tahun sumber dan tujuan</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <label class="block text-sm font-medium text-gray-700">Tahun sumber
                    <select wire:model="sourceAcademicYearId" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}{{ $year->is_active ? ' (aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium text-gray-700">Tahun tujuan yang sudah ada (opsional)
                    <select wire:model="targetAcademicYearId" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3">
                        <option value="">Buat tahun tujuan baru</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium text-gray-700">Nama tahun tujuan
                    <input wire:model="targetYearName" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3" placeholder="2026/2027">
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block text-sm font-medium text-gray-700">Mulai
                        <input type="date" wire:model="targetStartDate" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3">
                    </label>
                    <label class="block text-sm font-medium text-gray-700">Selesai
                        <input type="date" wire:model="targetEndDate" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3">
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button wire:click="prepareMapping" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-xl bg-primary-500 px-5 py-3 font-semibold text-white hover:bg-primary-600">
                    <span class="material-symbols-rounded">arrow_forward</span> Buat Mapping Otomatis
                </button>
            </div>
        </div>
    @elseif($step === 2)
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-gray-900">Mapping kelas tujuan</h2>
                <p class="text-sm text-gray-500">Sistem mencocokkan level dan label rombel. Pastikan semua kelas 1–5 memiliki tujuan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-sm">
                    <thead class="border-b border-gray-100 text-left text-xs uppercase text-gray-500"><tr><th class="px-3 py-3">Kelas asal</th><th class="px-3 py-3">Level</th><th class="px-3 py-3">Kelas tujuan</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($classes as $class)
                            <tr>
                                <td class="px-3 py-3 font-medium">{{ $class->name }}</td>
                                <td class="px-3 py-3">{{ $class->level }}</td>
                                <td class="px-3 py-3">
                                    @if((int) $class->level >= 6)
                                        <span class="inline-flex rounded-lg bg-amber-50 px-3 py-2 font-semibold text-amber-700">Lulus</span>
                                    @else
                                        <select wire:model="classMapping.{{ $class->id }}" class="w-full max-w-md rounded-xl border-gray-200 px-3 py-2.5">
                                            <option value="">-- Pilih kelas tujuan --</option>
                                            @foreach($classes->where('level', (string) ((int) $class->level + 1)) as $target)
                                                <option value="{{ $target->id }}">{{ $target->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-between gap-3">
                <button wire:click="$set('step', 1)" class="rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">Kembali</button>
                <button wire:click="preparePreview" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-xl bg-primary-500 px-5 py-3 font-semibold text-white hover:bg-primary-600"><span class="material-symbols-rounded">preview</span> Tampilkan Preview</button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 gap-3 md:grid-cols-6">
            @foreach([['total','Total'],['promoted','Naik'],['retained','Tetap'],['moved','Pindah'],['graduated','Lulus'],['deferred','Ditunda']] as [$key, $label])
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><p class="text-xs uppercase text-gray-500">{{ $label }}</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary[$key] ?? 0 }}</p></div>
            @endforeach
        </div>

        @if($warnings)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"><p class="mb-2 font-bold">Perlu diperiksa sebelum konfirmasi:</p><ul class="list-disc list-inside space-y-1">@foreach($warnings as $warning)<li>{{ $warning }}</li>@endforeach</ul></div>
        @endif

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div><h2 class="text-lg font-bold text-gray-900">Preview per siswa</h2><p class="text-sm text-gray-500">Gunakan override hanya bila siswa tidak mengikuti mapping standar.</p></div>
                <button wire:click="refreshPreview" class="rounded-xl border border-gray-200 px-4 py-2.5 font-semibold text-gray-700 hover:bg-gray-50">Refresh Preview</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-sm">
                    <thead class="border-b border-gray-100 text-left text-xs uppercase text-gray-500"><tr><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Kelas asal</th><th class="px-3 py-3">Aksi</th><th class="px-3 py-3">Kelas tujuan</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($previewRows as $row)
                            <tr wire:key="promotion-student-{{ $row['student_id'] }}">
                                <td class="px-3 py-3"><p class="font-semibold text-gray-900">{{ $row['name'] }}</p><p class="text-xs text-gray-500">{{ $row['nis'] }}</p></td>
                                <td class="px-3 py-3">{{ $row['source_class_name'] ?? '-' }}</td>
                                <td class="px-3 py-3"><select wire:model="studentDecisions.{{ $row['student_id'] }}.action" class="rounded-xl border-gray-200 px-3 py-2"><option value="promote">Naik</option><option value="retain">Tetap</option><option value="move">Pindah</option><option value="graduate">Lulus</option><option value="defer">Tunda</option></select></td>
                                <td class="px-3 py-3">
                                    <select wire:model="studentDecisions.{{ $row['student_id'] }}.target_class_id" class="w-full rounded-xl border-gray-200 px-3 py-2" @disabled($row['action'] === 'graduate')>
                                        <option value="">-- Tidak ada --</option>
                                        @foreach($classes as $target)<option value="{{ $target->id }}">{{ $target->name }}</option>@endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex flex-col-reverse justify-between gap-3 md:flex-row">
                <button wire:click="backToMapping" class="rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">Kembali ke Mapping</button>
                <button wire:click="applyPromotion" wire:confirm="Proses ini akan mengubah kelas siswa, menandai kelas 6 sebagai alumni, dan mengaktifkan tahun ajaran baru. Lanjutkan?" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700"><span class="material-symbols-rounded">check_circle</span> Konfirmasi & Terapkan</button>
            </div>
        </div>
    @endif
</div>
