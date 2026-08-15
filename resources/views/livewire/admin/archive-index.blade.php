<div>
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arsip Santri & Wali</h1>
            <p class="text-gray-500 dark:text-gray-400">Data pindah, keluar, alumni, dan relasi orang tua yang tetap tersimpan.</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50">
            <span class="material-symbols-rounded">arrow_back</span> Kembali ke Data Aktif
        </a>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-6 md:grid-cols-6">
        <div class="p-4 bg-green-50 rounded-2xl"><p class="text-xs text-green-700">Siswa aktif</p><p class="text-2xl font-bold text-green-800">{{ $stats['activeStudents'] }}</p></div>
        <div class="p-4 bg-amber-50 rounded-2xl"><p class="text-xs text-amber-700">Alumni</p><p class="text-2xl font-bold text-amber-800">{{ $stats['graduatedStudents'] }}</p></div>
        <div class="p-4 bg-blue-50 rounded-2xl"><p class="text-xs text-blue-700">Pindah</p><p class="text-2xl font-bold text-blue-800">{{ $stats['transferredStudents'] }}</p></div>
        <div class="p-4 bg-gray-100 rounded-2xl"><p class="text-xs text-gray-600">Keluar</p><p class="text-2xl font-bold text-gray-800">{{ $stats['withdrawnStudents'] }}</p></div>
        <div class="p-4 bg-violet-50 rounded-2xl"><p class="text-xs text-violet-700">Wali aktif</p><p class="text-2xl font-bold text-violet-800">{{ $stats['activeParents'] }}</p></div>
        <div class="p-4 bg-rose-50 rounded-2xl"><p class="text-xs text-rose-700">Wali arsip</p><p class="text-2xl font-bold text-rose-800">{{ $stats['archivedParents'] }}</p></div>
    </div>

    <div class="p-4 mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
        <div class="flex flex-col gap-3 md:flex-row">
            <div class="flex gap-2 p-1 bg-gray-100 rounded-xl">
                <button wire:click="$set('tab', 'students')" class="px-4 py-2 rounded-lg font-semibold {{ $tab === 'students' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500' }}">Santri</button>
                <button wire:click="$set('tab', 'parents')" class="px-4 py-2 rounded-lg font-semibold {{ $tab === 'parents' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500' }}">Wali</button>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, NIS, wali, username, HP, atau QR..." class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl">
            <select wire:model.live="statusFilter" class="px-4 py-2.5 border border-gray-200 rounded-xl">
                @if($tab === 'students')
                    <option value="archived">Semua Arsip</option>
                    <option value="active">Aktif</option>
                    <option value="graduated">Alumni/Lulus</option>
                    <option value="transferred">Pindah</option>
                    <option value="withdrawn">Keluar</option>
                    <option value="">Semua Status</option>
                @else
                    <option value="archived">Wali Arsip</option>
                    <option value="active">Wali Aktif</option>
                    <option value="">Semua Wali</option>
                @endif
            </select>
        </div>
    </div>

    @error('archive') <div class="p-4 mb-4 text-red-700 bg-red-50 border border-red-200 rounded-xl">{{ $message }}</div> @enderror
    @error('restore') <div class="p-4 mb-4 text-red-700 bg-red-50 border border-red-200 rounded-xl">{{ $message }}</div> @enderror
    @error('parent') <div class="p-4 mb-4 text-red-700 bg-red-50 border border-red-200 rounded-xl">{{ $message }}</div> @enderror

    @if($tab === 'students')
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50"><tr><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Santri</th><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Status</th><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Wali</th><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Detail Keluar</th><th class="px-5 py-4 text-right text-xs uppercase text-gray-500">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $student)
                            @php($exit = $student->exitRecords->first())
                            <tr wire:key="archive-student-{{ $student->id }}" class="hover:bg-gray-50">
                                <td class="px-5 py-4"><p class="font-bold text-gray-900">{{ $student->name }}</p><p class="font-mono text-xs text-gray-500">{{ $student->nis }} · {{ $student->classRoom?->name ?? ($exit?->from_class_name ?? '-') }}</p></td>
                                <td class="px-5 py-4"><span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $student->student_status === 'graduated' ? 'bg-amber-100 text-amber-700' : ($student->student_status === 'transferred' ? 'bg-blue-100 text-blue-700' : ($student->student_status === 'withdrawn' ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-700')) }}">{{ match($student->student_status) { 'graduated' => 'Alumni/Lulus', 'transferred' => 'Pindah', 'withdrawn' => 'Keluar', default => 'Aktif' } }}</span></td>
                                <td class="px-5 py-4"><div class="flex flex-wrap gap-1">@forelse($student->parents as $parent)<span class="px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded-lg">{{ $parent->user?->name }} ({{ $parent->type_display }})</span>@empty<span class="text-sm text-gray-400">-</span>@endforelse</div></td>
                                <td class="px-5 py-4 text-sm text-gray-600">@if($exit)<p>{{ $exit->effective_date?->format('d M Y') ?? 'Tanggal tidak tersedia' }}</p><p class="text-xs text-gray-400">{{ $exit->reason ?: 'Tanpa alasan tercatat' }}@if($exit->destination) · {{ $exit->destination }}@endif</p>@else<span class="text-gray-400">Histori lama/alumni</span>@endif</td>
                                <td class="px-5 py-4"><div class="flex justify-end gap-1"><button wire:click="showStudent({{ $student->id }})" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" title="Detail"><span class="material-symbols-rounded">visibility</span></button>@if(in_array($student->student_status, ['transferred', 'withdrawn']))<button wire:click="openRestoreModal({{ $student->id }})" class="p-2 text-emerald-600 rounded-lg hover:bg-emerald-50" title="Pulihkan"><span class="material-symbols-rounded">restore</span></button>@elseif(($student->student_status ?? 'active') === 'active' && $student->is_active)<button wire:click="openArchiveModal({{ $student->id }})" class="p-2 text-rose-600 rounded-lg hover:bg-rose-50" title="Arsipkan"><span class="material-symbols-rounded">inventory_2</span></button>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-gray-500">Tidak ada data pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())<div class="px-5 py-4 border-t border-gray-100">{{ $students->links() }}</div>@endif
        </div>
    @else
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="overflow-x-auto"><table class="w-full"><thead class="bg-gray-50"><tr><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Wali</th><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Status</th><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Anak</th><th class="px-5 py-4 text-left text-xs uppercase text-gray-500">Login/QR</th><th class="px-5 py-4 text-right text-xs uppercase text-gray-500">Aksi</th></tr></thead><tbody class="divide-y divide-gray-100">
                @forelse($parents as $parent)
                    <tr wire:key="archive-parent-{{ $parent->id }}" class="hover:bg-gray-50"><td class="px-5 py-4"><p class="font-bold text-gray-900">{{ $parent->user?->name }}</p><p class="text-xs text-gray-500">{{ $parent->type_display }} · {{ $parent->user?->phone ?: 'No HP tidak tersedia' }}</p></td><td class="px-5 py-4"><span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $parent->hasActiveChildren() ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">{{ $parent->hasActiveChildren() ? 'Aktif' : 'Arsip' }}</span></td><td class="px-5 py-4"><div class="flex flex-wrap gap-1">@forelse($parent->students as $student)<span class="px-2 py-1 text-xs rounded-lg {{ $student->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $student->name }} ({{ $student->student_status ?? 'active' }})</span>@empty<span class="text-sm text-gray-400">-</span>@endforelse</div></td><td class="px-5 py-4 text-xs"><p class="font-mono">{{ $parent->qr_code_string }}</p><p class="{{ $parent->user?->is_active ? 'text-green-600' : 'text-rose-600' }}">Login: {{ $parent->user?->is_active ? 'aktif' : 'nonaktif' }}</p></td><td class="px-5 py-4"><div class="flex justify-end gap-1"><button wire:click="showParent({{ $parent->id }})" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100"><span class="material-symbols-rounded">visibility</span></button>@if($parent->hasActiveChildren() && !$parent->user?->is_active)<button wire:click="activateParent({{ $parent->id }})" class="p-2 text-emerald-600 rounded-lg hover:bg-emerald-50" title="Aktifkan login"><span class="material-symbols-rounded">lock_open</span></button>@endif</div></td></tr>
                @empty<tr><td colspan="5" class="px-5 py-12 text-center text-gray-500">Tidak ada wali pada filter ini.</td></tr>@endforelse
            </tbody></table></div>
            @if($parents instanceof \Illuminate\Pagination\LengthAwarePaginator && $parents->hasPages())<div class="px-5 py-4 border-t border-gray-100">{{ $parents->links() }}</div>@endif
        </div>
    @endif

    @if($showArchiveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"><div class="w-full max-w-lg p-6 bg-white shadow-xl rounded-2xl"><div class="flex items-center justify-between mb-5"><h2 class="text-xl font-bold">Arsipkan Santri</h2><button wire:click="$set('showArchiveModal', false)" class="text-2xl">&times;</button></div><p class="mb-4 text-sm text-gray-500">Relasi orang tua, presensi, QR, dan histori tidak akan dihapus.</p><div class="grid gap-4"><label class="text-sm font-medium">Jenis arsip<select wire:model="exitType" class="block w-full mt-1 border-gray-200 rounded-xl"><option value="transferred">Pindah</option><option value="withdrawn">Keluar</option></select></label><label class="text-sm font-medium">Tanggal efektif<input type="date" wire:model="effectiveDate" class="block w-full mt-1 border-gray-200 rounded-xl"></label><label class="text-sm font-medium">Alasan<textarea wire:model="reason" rows="2" class="block w-full mt-1 border-gray-200 rounded-xl"></textarea></label><label class="text-sm font-medium">Sekolah/tujuan baru<input wire:model="destination" class="block w-full mt-1 border-gray-200 rounded-xl"></label><label class="text-sm font-medium">Catatan<input wire:model="notes" class="block w-full mt-1 border-gray-200 rounded-xl"></label><label class="text-sm font-medium">Bukti opsional<input type="file" wire:model="evidenceFile" accept=".jpg,.jpeg,.png,.pdf" class="block w-full mt-1"></label></div><div class="flex gap-3 mt-6"><button wire:click="$set('showArchiveModal', false)" class="flex-1 px-4 py-3 border rounded-xl">Batal</button><button wire:click="archiveStudent" class="flex-1 px-4 py-3 font-bold text-white bg-rose-600 rounded-xl">Arsipkan</button></div></div></div>
    @endif

    @if($showRestoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"><div class="w-full max-w-lg p-6 bg-white shadow-xl rounded-2xl"><h2 class="mb-4 text-xl font-bold">Pulihkan Santri</h2><div class="grid gap-4"><label class="text-sm font-medium">Tahun ajaran<select wire:model="restoreAcademicYearId" class="block w-full mt-1 border-gray-200 rounded-xl">@foreach($academicYears as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></label><label class="text-sm font-medium">Kelas<select wire:model="restoreClassId" class="block w-full mt-1 border-gray-200 rounded-xl"><option value="">Pilih kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></label><label class="text-sm font-medium">Catatan pemulihan<textarea wire:model="restoreNotes" rows="2" class="block w-full mt-1 border-gray-200 rounded-xl"></textarea></label></div><div class="flex gap-3 mt-6"><button wire:click="$set('showRestoreModal', false)" class="flex-1 px-4 py-3 border rounded-xl">Batal</button><button wire:click="restoreStudent" class="flex-1 px-4 py-3 font-bold text-white bg-emerald-600 rounded-xl">Pulihkan</button></div></div></div>
    @endif

    @if($showStudentDetail && $selectedStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"><div class="w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 bg-white shadow-xl rounded-2xl"><div class="flex items-center justify-between mb-5"><h2 class="text-xl font-bold">Detail Arsip Santri</h2><button wire:click="$set('showStudentDetail', false)" class="text-2xl">&times;</button></div><div class="grid gap-4 md:grid-cols-2"><div><p class="text-xs text-gray-500">Santri</p><p class="font-bold">{{ $selectedStudent->name }} · {{ $selectedStudent->nis }}</p><p class="text-sm text-gray-500">Status: {{ $selectedStudent->student_status ?? 'active' }}</p></div><div><p class="text-xs text-gray-500">Orang tua</p>@foreach($selectedStudent->parents as $parent)<p class="text-sm">{{ $parent->user?->name }} ({{ $parent->type_display }})</p>@endforeach</div></div><h3 class="mt-6 mb-2 font-bold">Histori keluar/pemulihan</h3><div class="space-y-3">@forelse($selectedStudent->exitRecords as $exit)<div class="p-3 border rounded-xl"><p class="font-semibold">{{ $exit->exit_type === 'transferred' ? 'Pindah' : 'Keluar' }} · {{ $exit->effective_date?->format('d M Y') ?: 'Tanggal tidak tersedia' }}</p><p class="text-sm text-gray-600">{{ $exit->reason ?: 'Tanpa alasan' }} @if($exit->destination) · Tujuan: {{ $exit->destination }}@endif</p><p class="text-xs text-gray-400">{{ $exit->restored_at ? 'Dipulihkan pada '.$exit->restored_at->format('d M Y H:i') : 'Masih diarsipkan' }}</p></div>@empty<p class="text-sm text-gray-500">Belum ada histori keluar; kemungkinan alumni atau histori lama.</p>@endforelse</div><h3 class="mt-6 mb-2 font-bold">Histori enrollment</h3><div class="space-y-1">@foreach($selectedStudent->enrollments->sortByDesc('academic_year_id') as $enrollment)<p class="text-sm text-gray-600">{{ $enrollment->academicYear?->name ?: '-' }} · {{ $enrollment->class_name ?: '-' }} · {{ $enrollment->status }}</p>@endforeach</div></div></div>
    @endif

    @if($showParentDetail && $selectedParent)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"><div class="w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 bg-white shadow-xl rounded-2xl"><div class="flex items-center justify-between mb-5"><h2 class="text-xl font-bold">Detail Arsip Wali</h2><button wire:click="$set('showParentDetail', false)" class="text-2xl">&times;</button></div><p class="font-bold">{{ $selectedParent->user?->name }} · {{ $selectedParent->type_display }}</p><p class="text-sm text-gray-500">Username: {{ $selectedParent->user?->username }} · QR: {{ $selectedParent->qr_code_string }}</p><h3 class="mt-6 mb-2 font-bold">Anak dan status</h3><div class="space-y-2">@foreach($selectedParent->students as $student)<div class="p-3 border rounded-xl"><p class="font-semibold">{{ $student->name }} ({{ $student->nis }})</p><p class="text-sm text-gray-600">{{ $student->student_status ?? 'active' }} · {{ $student->classRoom?->name ?: 'Tidak ada kelas' }}</p>@foreach($student->exitRecords as $exit)<p class="text-xs text-gray-400">{{ $exit->exit_type }} · {{ $exit->effective_date?->format('d M Y') ?: 'legacy' }} · {{ $exit->reason }}</p>@endforeach</div>@endforeach</div><h3 class="mt-6 mb-2 font-bold">Histori akun wali</h3>@foreach($selectedParent->archiveRecords as $record)<p class="text-sm text-gray-600">{{ $record->archived_at?->format('d M Y H:i') }} · {{ $record->restored_at ? 'dipulihkan' : 'diarsipkan' }} · login {{ $record->login_disabled ? 'dinonaktifkan' : 'tidak diubah' }}</p>@endforeach</div></div>
    @endif
</div>
