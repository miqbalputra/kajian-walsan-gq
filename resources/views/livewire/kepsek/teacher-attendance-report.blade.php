<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Presensi Guru</h1>
            <p class="text-gray-500 dark:text-gray-400">Pantau performa Ustadz/Ustadzah dan buka file catatan/izin yang diupload.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="fromDate"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="toDate"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Kajian Detail</label>
                <select wire:model.live="kajianId"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Kajian Upload</option>
                    @foreach($kajians as $kajian)
                        <option value="{{ $kajian->id }}">{{ $kajian->title }} ({{ $kajian->date?->format('d/m/Y') }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="hadir_fisik">Hadir Langsung</option>
                    <option value="hadir_online">Menyimak Online</option>
                    <option value="izin">Izin</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Ditolak</option>
                    <option value="not_started">Belum Mulai</option>
                    <option value="alpha">Alfa</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Nama, username, email, kajian...">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-2xl font-bold text-gray-900">{{ $this->summary['total'] }}</p>
            <p class="text-sm text-gray-500">Total Baris</p>
        </div>
        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
            <p class="text-2xl font-bold text-emerald-700">{{ $this->summary['hadir_fisik'] }}</p>
            <p class="text-sm text-emerald-600">Hadir Langsung</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
            <p class="text-2xl font-bold text-blue-700">{{ $this->summary['hadir_online'] }}</p>
            <p class="text-sm text-blue-600">Online</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-700">{{ $this->summary['izin'] }}</p>
            <p class="text-sm text-yellow-600">Izin</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
            <p class="text-2xl font-bold text-amber-700">{{ $this->summary['pending'] }}</p>
            <p class="text-sm text-amber-600">Pending</p>
        </div>
        <div class="bg-rose-50 rounded-xl p-4 border border-rose-100">
            <p class="text-2xl font-bold text-rose-700">{{ $this->summary['rejected'] }}</p>
            <p class="text-sm text-rose-600">Ditolak</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-100">
            <p class="text-2xl font-bold text-red-700">{{ $this->summary['alpha'] }}</p>
            <p class="text-sm text-red-600">Alfa</p>
        </div>
        <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
            <p class="text-2xl font-bold text-primary-700">{{ $this->summary['uploaded'] }}</p>
            <p class="text-sm text-primary-600">File Upload</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 dark:border-slate-800">
            <h2 class="font-bold text-gray-900 dark:text-white">Performa Guru Berdasarkan Rentang Tanggal</h2>
            <p class="text-sm text-gray-500 mt-1">Persentase dihitung dari presensi, izin, pending, dan ditolak dibanding kajian yang sudah selesai.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px]">
                <thead class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Performa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Hadir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Online</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Izin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pending</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Ditolak</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Alfa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">File</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($performanceRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $row['teacher_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $row['teacher_type'] }}</p>
                                <p class="text-xs text-gray-400">{{ $row['username'] }} - {{ $row['email'] }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary-500" style="width: {{ min($row['participation_rate'], 100) }}%"></div>
                                    </div>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $row['participation_rate'] }}%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $row['completed_events'] }} kajian selesai</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-emerald-700 font-bold">{{ $row['hadir_fisik'] }}</td>
                            <td class="px-4 py-4 text-sm text-blue-700 font-bold">{{ $row['hadir_online'] }}</td>
                            <td class="px-4 py-4 text-sm text-yellow-700 font-bold">{{ $row['izin'] }}</td>
                            <td class="px-4 py-4 text-sm text-amber-700 font-bold">{{ $row['pending'] }}</td>
                            <td class="px-4 py-4 text-sm text-rose-700 font-bold">{{ $row['rejected'] }}</td>
                            <td class="px-4 py-4 text-sm text-red-700 font-bold">{{ $row['alpha'] }}</td>
                            <td class="px-4 py-4 text-sm text-primary-700 font-bold">{{ $row['uploaded'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-gray-500">Tidak ada data performa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-slate-800">
            <h2 class="font-bold text-gray-900 dark:text-white">Detail Presensi dan File Upload</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if($this->selectedKajian)
                    Menampilkan semua guru pada kajian: {{ $this->selectedKajian->title }}.
                @else
                    Menampilkan semua data presensi guru dalam rentang tanggal.
                @endif
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px]">
                <thead class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Kajian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">File</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $row['teacher_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $row['teacher_type'] }}</p>
                                <p class="text-xs text-gray-400">{{ $row['username'] }} - {{ $row['email'] }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $row['kajian_title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $row['kajian_date'] }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $row['derived_badge'] }}">
                                    {{ $row['derived_label'] }}
                                </span>
                                <p class="text-xs text-gray-400 mt-1">Validasi: {{ $row['validation_status'] ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <p>{{ $row['method'] ?? '-' }}</p>
                                @if($row['scanned_at'])
                                    <p class="text-xs text-gray-400">Scan: {{ $row['scanned_at'] }}</p>
                                @endif
                                @if($row['submitted_at'])
                                    <p class="text-xs text-gray-400">Kirim: {{ $row['submitted_at'] }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($row['proof_url'])
                                    <a href="{{ $row['proof_url'] }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-primary-50 text-primary-700 rounded-xl font-bold text-xs hover:bg-primary-100 transition-colors">
                                        <span class="material-symbols-rounded text-sm">visibility</span>
                                        Lihat {{ $row['document_label'] }}
                                    </a>
                                @else
                                    <span class="text-sm text-red-500 font-medium">Belum upload</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 max-w-md">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $row['reason'] }}</p>
                                @if($row['notes'])
                                    <p class="text-xs text-gray-500 mt-1"><strong>Catatan:</strong> {{ $row['notes'] }}</p>
                                @endif
                                @if($row['validated_by'])
                                    <p class="text-xs text-gray-400 mt-1">Validator: {{ $row['validated_by'] }}</p>
                                @endif
                                @if($row['ai_status'])
                                    <p class="text-xs text-gray-400 mt-1">
                                        AI: {{ $row['ai_status'] }}
                                        @if($row['ai_confidence'])
                                            ({{ $row['ai_confidence'] }}%)
                                        @endif
                                    </p>
                                @endif
                                @if($row['ai_reason'])
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $row['ai_reason'] }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">assignment</span>
                                <p class="mt-2">Tidak ada data presensi guru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-800">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>
