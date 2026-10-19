<div class="min-w-0">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Arsip Guru</h1>
            <p class="mt-1 text-sm text-gray-500">Guru yang sudah resign atau keluar. Data arsip hanya dapat dibaca oleh admin.</p>
        </div>
        <a href="{{ route('admin.parents.index', ['typeFilter' => 'teacher']) }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 font-semibold text-gray-700 hover:bg-gray-50">
            <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>Data Guru Aktif
        </a>
    </div>

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-900">
        <span class="material-symbols-rounded" aria-hidden="true">lock</span>
        <p>Guru dalam arsip tidak dihitung sebagai guru aktif. Profil dan riwayat di sini disimpan sesuai kondisi saat diarsipkan, termasuk bila akun orang tuanya masih aktif.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-4 sm:p-6">
            <label for="teacher-archive-search" class="mb-2 block text-sm font-medium text-gray-700">Cari nama guru</label>
            <input id="teacher-archive-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Nama guru dalam arsip..." class="w-full rounded-xl border border-gray-200 px-4 py-3 sm:max-w-md">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr><th class="px-6 py-4">Guru</th><th class="px-6 py-4">Tanggal Keluar</th><th class="px-6 py-4">Alasan</th><th class="px-6 py-4">Diarsipkan oleh</th><th class="px-6 py-4 text-right">Detail</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($archives as $archive)
                        <tr wire:key="teacher-archive-{{ $archive->id }}">
                            <td class="px-6 py-4"><p class="font-semibold text-gray-900">{{ $archive->teacher_name }}</p><p class="mt-1 text-xs text-gray-500">{{ $archive->profile_snapshot['type'] === 'teacher' ? 'Guru murni' : 'Orang tua sekaligus guru' }}</p></td>
                            <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ $archive->departed_on->translatedFormat('d F Y') }}</td>
                            <td class="max-w-xs px-6 py-4 text-gray-600"><p class="line-clamp-2 break-words">{{ $archive->reason }}</p></td>
                            <td class="px-6 py-4 text-gray-600">{{ $archive->archivedBy?->name ?? '-' }}<p class="mt-1 whitespace-nowrap text-xs text-gray-400">{{ $archive->created_at->format('d/m/Y H:i') }}</p></td>
                            <td class="px-6 py-4 text-right"><button type="button" wire:click="showDetail({{ $archive->id }})" class="whitespace-nowrap rounded-lg px-3 py-2 font-semibold text-primary-700 hover:bg-primary-50">Lihat Arsip</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">{{ $search !== '' ? 'Tidak ada arsip guru yang cocok.' : 'Belum ada guru yang diarsipkan.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 sm:p-6">{{ $archives->links() }}</div>
    </div>

    @if($selectedArchive)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="teacher-archive-title" wire:keydown.escape.window="closeDetail">
            <div class="mx-auto my-4 w-full max-w-4xl rounded-2xl bg-white p-5 shadow-xl sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div><p class="mb-1 text-xs font-semibold uppercase tracking-wider text-amber-700">Arsip · Hanya Baca</p><h2 id="teacher-archive-title" class="text-xl font-bold text-gray-900">{{ $selectedArchive->teacher_name }}</h2></div>
                    <button type="button" wire:click="closeDetail" aria-label="Tutup detail arsip" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100"><span class="material-symbols-rounded">close</span></button>
                </div>
                <dl class="mb-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Tanggal keluar</dt><dd class="mt-1 font-medium text-gray-900">{{ $selectedArchive->departed_on->translatedFormat('d F Y') }}</dd></div>
                    <div><dt class="text-gray-500">Diarsipkan oleh</dt><dd class="mt-1 text-gray-900">{{ $selectedArchive->archivedBy?->name ?? '-' }} · {{ $selectedArchive->created_at->format('d/m/Y H:i') }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Alasan resign / keluar</dt><dd class="mt-1 whitespace-pre-line break-words text-gray-900">{{ $selectedArchive->reason }}</dd></div>
                    @foreach(['username' => 'Username', 'email' => 'Email', 'phone' => 'Telepon', 'nik' => 'NIK', 'occupation' => 'Pekerjaan', 'address' => 'Alamat'] as $key => $label)
                        <div><dt class="text-gray-500">{{ $label }}</dt><dd class="mt-1 break-words text-gray-900">{{ $selectedArchive->profile_snapshot[$key] ?: '-' }}</dd></div>
                    @endforeach
                </dl>
                @if($selectedArchive->profile_snapshot['children'])
                    <div class="mb-6 rounded-xl bg-gray-50 p-4 text-sm text-gray-700"><p class="mb-2 font-semibold">Anak saat guru diarsipkan</p>@foreach($selectedArchive->profile_snapshot['children'] as $child)<p>{{ $child['name'] }} · {{ $child['class'] ?? '-' }}</p>@endforeach</div>
                @endif
                <h3 class="mb-3 font-semibold text-gray-900">Riwayat presensi saat diarsipkan</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Kajian</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Validasi</th><th class="px-4 py-3">Bukti</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($selectedArchive->attendance_snapshot as $attendance)
                                <tr><td class="px-4 py-3"><p class="font-medium text-gray-900">{{ $attendance['event_title'] }}</p><p class="mt-1 text-xs text-gray-500">{{ $attendance['event_date'] ?? '-' }}</p>@if($attendance['notes'])<p class="mt-1 break-words text-xs text-gray-500">{{ $attendance['notes'] }}</p>@endif</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $attendance['status_label'] }}@if($attendance['deleted_at'])<p class="text-xs text-gray-400">Dibatalkan sebelum arsip</p>@endif</td>
                                    <td class="px-4 py-3 text-gray-600">{{ ['approved' => 'Disetujui', 'pending' => 'Menunggu validasi', 'rejected' => 'Ditolak'][$attendance['validation_status']] ?? '-' }}</td>
                                    <td class="px-4 py-3">@if($attendance['proof_url'])<a href="{{ $attendance['proof_url'] }}" target="_blank" rel="noopener noreferrer" class="whitespace-nowrap font-semibold text-primary-700 underline">Lihat Bukti</a>@else<span class="text-gray-400">-</span>@endif</td></tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada catatan presensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-end"><button type="button" wire:click="closeDetail" class="rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">Tutup</button></div>
            </div>
        </div>
    @endif
</div>
