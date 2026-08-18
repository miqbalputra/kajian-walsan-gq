<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Kehadiran Wali Santri</h1>
            <p class="text-gray-500">Filter dan export data presensi wali santri. Presensi guru ada di menu Presensi Guru.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportExcel"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition-colors">
                <span class="material-symbols-rounded">table_chart</span>
                Export Excel
            </button>
            <button wire:click="exportPdf"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition-colors">
                <span class="material-symbols-rounded">picture_as_pdf</span>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-2xl font-bold text-gray-900">{{ $this->summary['total'] }}</p>
            <p class="text-sm text-gray-500">Total Sasaran</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 border border-green-100">
            <p class="text-2xl font-bold text-green-700">{{ $this->summary['hadir_fisik'] }}</p>
            <p class="text-sm text-green-600">Hadir Fisik</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
            <p class="text-2xl font-bold text-blue-700">{{ $this->summary['hadir_online'] }}</p>
            <p class="text-sm text-blue-600">Hadir Online</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-700">{{ $this->summary['izin'] }}</p>
            <p class="text-sm text-yellow-600">Izin</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-100">
            <p class="text-2xl font-bold text-red-700">{{ $this->summary['alpha'] }}</p>
            <p class="text-sm text-red-600">Alpha</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Academic Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                <select wire:model.live="academicYearId"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Tahun</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kajian -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kajian</label>
                <select wire:model.live="kajianId"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Kajian</option>
                    @foreach($this->kajians as $kajian)
                        <option value="{{ $kajian->id }}">{{ $kajian->title }} ({{ $kajian->date->format('d/m') }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Class -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas Siswa</label>
                <select wire:model.live="classId"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran</label>
                <select wire:model.live="status"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="hadir_fisik">Hadir Fisik</option>
                    <option value="hadir_online">Hadir Online</option>
                    <option value="izin">Izin</option>
                    <option value="pending">Menunggu Validasi</option>
                    <option value="rejected">Ditolak</option>
                    <option value="not_started">Presensi Dibuka</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Shareable final attendance statistics -->
    @if($shareStatistics)
        @php
            $selectedShareClass = collect($shareStatistics['classes'])->firstWhere('key', $shareClassKey);
            $shareFilenameBase = 'statistik-kehadiran-kajian-'.$shareStatistics['event']['id'].'-'.now()->format('Ymd');
        @endphp
        <section class="mb-6" x-data="{ downloading: false }">
            <div class="overflow-hidden rounded-3xl border border-primary-100 bg-white shadow-sm">
                <div class="border-b border-primary-100 bg-gradient-to-r from-primary-50 via-white to-emerald-50 px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-2xl">
                            <div class="mb-2 flex items-center gap-2 text-primary-700">
                                <span class="material-symbols-rounded">share</span>
                                <span class="text-xs font-black uppercase tracking-[0.16em]">Siap dibagikan</span>
                            </div>
                            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Statistik Kehadiran per Kelas</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $shareStatistics['event']['title'] }} · {{ $shareStatistics['event']['date'] }}
                            </p>
                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Persentase menggunakan total sasaran Bapak atau Ibu di setiap kelas. Alfa hanya dihitung setelah presensi ditutup; pengajuan pending atau ditolak ditandai terpisah.
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap lg:max-w-md lg:justify-end">
                            @foreach([
                                'hadir_fisik' => ['Hadir Langsung', 'bg-emerald-500'],
                                'hadir_online' => ['Online', 'bg-blue-500'],
                                'izin' => ['Izin', 'bg-amber-400'],
                                'alpha' => ['Alfa', 'bg-red-500'],
                            ] as $status => [$label, $dotClass])
                                <div class="rounded-xl border border-white bg-white/80 px-3 py-2 text-xs shadow-sm">
                                    <span class="flex items-center gap-1.5 text-slate-500">
                                        <span class="h-2 w-2 rounded-full {{ $dotClass }}"></span>{{ $label }}
                                    </span>
                                    <p class="mt-0.5 text-base font-extrabold text-slate-900">{{ $shareStatistics['summary']['counts'][$status] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                        <div class="w-full xl:max-w-md">
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Pilih kelas untuk grup kelas</label>
                            <select wire:model.live="shareClassKey"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500">
                                <option value="">Pilih satu kelas</option>
                                @foreach($shareStatistics['classes'] as $classStat)
                                    <option value="{{ $classStat['key'] }}">{{ $classStat['name'] }} ({{ $classStat['summary']['total'] }} sasaran)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <button type="button"
                                @click="downloading = true; window.downloadAttendanceShareImage('attendance-share-general', '{{ $shareFilenameBase }}-grup-umum').finally(() => downloading = false)"
                                :disabled="downloading"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-700 disabled:cursor-wait disabled:opacity-60">
                                <span class="material-symbols-rounded text-lg">download</span>
                                <span x-text="downloading ? 'Menyiapkan gambar…' : 'Unduh untuk Grup Umum'"></span>
                            </button>
                            <button type="button"
                                @click="downloading = true; window.downloadAttendanceShareImage('attendance-share-{{ $shareClassKey }}', '{{ $shareFilenameBase }}-kelas-{{ $shareClassKey }}').finally(() => downloading = false)"
                                :disabled="downloading || !{{ $selectedShareClass ? 'true' : 'false' }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-4 py-2.5 text-sm font-bold text-primary-700 transition hover:bg-primary-100 disabled:cursor-not-allowed disabled:opacity-50">
                                <span class="material-symbols-rounded text-lg">image</span>
                                Unduh untuk Grup Kelas
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-2">
                        @forelse($shareStatistics['classes'] as $classStat)
                            <x-reports.attendance-share-class-card :class-stat="$classStat" />
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500 xl:col-span-2">
                                Tidak ada sasaran wali santri pada kajian ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Fixed-width light canvases: kept off-screen but rendered so html-to-image can export crisp PNGs. --}}
            <div class="fixed top-0 -left-[12000px] z-[-1] w-[1080px]" aria-hidden="true">
                <div id="attendance-share-general" class="attendance-share-export w-[1080px] bg-[#f5faf7] font-sans text-slate-900">
                    <div class="bg-primary-700 px-12 py-10 text-white">
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-primary-100">Presensi Wali Santri</p>
                        <h2 class="mt-2 text-4xl font-extrabold tracking-tight">Statistik Kehadiran per Kelas</h2>
                        <p class="mt-3 text-xl text-primary-100">{{ $shareStatistics['event']['title'] }}</p>
                        <p class="mt-1 text-base text-primary-200">{{ $shareStatistics['event']['date'] }}{{ $shareStatistics['event']['time'] ? ' · '.$shareStatistics['event']['time'] : '' }}</p>
                    </div>
                    <div class="p-10">
                        <div class="mb-7 grid grid-cols-4 gap-3">
                            @foreach([
                                'hadir_fisik' => ['Hadir Langsung', 'bg-emerald-50', 'text-emerald-700'],
                                'hadir_online' => ['Menyimak Online', 'bg-blue-50', 'text-blue-700'],
                                'izin' => ['Izin', 'bg-amber-50', 'text-amber-700'],
                                'alpha' => ['Alfa', 'bg-red-50', 'text-red-700'],
                            ] as $status => [$label, $bgClass, $textClass])
                                <div class="rounded-2xl {{ $bgClass }} p-4">
                                    <p class="text-sm font-semibold {{ $textClass }}">{{ $label }}</p>
                                    <p class="mt-1 text-3xl font-extrabold {{ $textClass }}">{{ $shareStatistics['summary']['counts'][$status] }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if($shareStatistics['summary']['counts']['perlu_validasi'] > 0)
                            <div class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 px-5 py-3 text-sm font-semibold text-orange-800">
                                {{ $shareStatistics['summary']['counts']['perlu_validasi'] }} pengajuan perlu validasi; tidak dimasukkan ke Alfa.
                            </div>
                        @endif

                        <div class="space-y-5">
                            @foreach($shareStatistics['classes'] as $classStat)
                                <x-reports.attendance-share-class-card :class-stat="$classStat" />
                            @endforeach
                        </div>
                    </div>
                    <div class="border-t border-slate-200 px-10 py-5 text-sm text-slate-500">
                        Dibuat {{ $shareStatistics['generated_at'] }} · Persentase dihitung dari total sasaran Bapak atau Ibu pada tiap kelas.
                    </div>
                </div>

                @foreach($shareStatistics['classes'] as $classStat)
                    <div id="attendance-share-{{ $classStat['key'] }}" class="attendance-share-export mt-10 w-[1080px] bg-[#f5faf7] font-sans text-slate-900">
                        <div class="bg-primary-700 px-12 py-10 text-white">
                            <p class="text-sm font-bold uppercase tracking-[0.2em] text-primary-100">Presensi Wali Santri</p>
                            <h2 class="mt-2 text-4xl font-extrabold tracking-tight">Statistik Kehadiran</h2>
                            <p class="mt-3 text-2xl font-bold text-white">{{ $classStat['name'] }}</p>
                            <p class="mt-1 text-base text-primary-200">{{ $shareStatistics['event']['title'] }} · {{ $shareStatistics['event']['date'] }}</p>
                        </div>
                        <div class="p-10">
                            <x-reports.attendance-share-class-card :class-stat="$classStat" />
                            <p class="mt-6 text-sm text-slate-500">Dibuat {{ $shareStatistics['generated_at'] }} · Persentase dari total sasaran Bapak atau Ibu.</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @elseif($shareStatisticsState === 'snapshot_missing')
        <section class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 p-5 text-orange-900">
            <div class="flex gap-3">
                <span class="material-symbols-rounded text-orange-600">history</span>
                <div>
                    <h2 class="font-bold">Kegiatan lama belum memiliki snapshot sasaran</h2>
                    <p class="mt-1 text-sm text-orange-800">Agar angka per kelas benar-benar final, lakukan backfill snapshot data lama terlebih dahulu. Data presensi yang sudah ada tidak akan diubah.</p>
                </div>
            </div>
        </section>
    @elseif($selectedKajian)
        <section class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
            <div class="flex gap-3">
                <span class="material-symbols-rounded text-amber-600">lock_clock</span>
                <div>
                    <h2 class="font-bold">Statistik gambar belum final</h2>
                    <p class="mt-1 text-sm text-amber-800">Pilih kajian yang sudah ditutup untuk membuat statistik per kelas. Selama presensi dibuka, data tetap realtime dan Alfa belum dihitung.</p>
                </div>
            </div>
        </section>
    @else
        <section class="mb-6 rounded-2xl border border-primary-100 bg-primary-50 p-5 text-primary-900">
            <div class="flex gap-3">
                <span class="material-symbols-rounded text-primary-600">insights</span>
                <div>
                    <h2 class="font-bold">Statistik untuk Dibagikan</h2>
                    <p class="mt-1 text-sm text-primary-800">Pilih satu kajian yang sudah ditutup pada filter di atas untuk menyiapkan gambar grup umum dan grup kelas.</p>
                </div>
            </div>
        </section>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kajian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Orang Tua</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Anak</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Metode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $index => $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $rows->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $row['kajian_date'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-medium max-w-xs truncate">
                                {{ $row['kajian_title'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $row['guardian_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $row['guardian_type'] }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $row['children'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $row['class_name'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium {{ $row['derived_badge'] }}">
                                    {{ $row['derived_label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @php
                                    $methodLabel = match ($row['method'] ?? null) {
                                        'scan_qr' => 'Scan QR',
                                        'manual' => 'Manual',
                                        'google_form' => 'Google Form M1',
                                        'public_form' => 'Form Publik M1',
                                        'upload' => 'Upload',
                                        default => '-',
                                    };
                                @endphp
                                {{ $methodLabel }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium
                                            @if(($row['validation_status'] ?? null) === 'approved') bg-green-100 text-green-700
                                            @elseif(($row['validation_status'] ?? null) === 'pending') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700 @endif">
                                    @if(($row['validation_status'] ?? null) === 'approved') ✓
                                    @elseif(($row['validation_status'] ?? null) === 'pending') ⏳
                                    @elseif(($row['validation_status'] ?? null) === 'rejected') ✗
                                    @else - @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">assignment</span>
                                <p class="mt-2">Tidak ada data presensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>
