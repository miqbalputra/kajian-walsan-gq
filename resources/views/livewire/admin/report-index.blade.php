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
            <p class="text-sm text-gray-500">Total Data</p>
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
                    <option value="alpha">Alpha</option>
                </select>
            </div>
        </div>
    </div>

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
                    @forelse($attendances as $index => $attendance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $attendances->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->kajianEvent?->date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-medium max-w-xs truncate">
                                {{ $attendance->kajianEvent?->title }}
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $attendance->parent?->user?->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $attendance->parent?->type_display }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->parent?->students->first()?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->parent?->students->first()?->classRoom?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium
                                            @if($attendance->status === 'hadir_fisik') bg-green-100 text-green-700
                                            @elseif($attendance->status === 'hadir_online') bg-blue-100 text-blue-700
                                            @elseif($attendance->status === 'izin') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700 @endif">
                                    @if($attendance->status === 'hadir_fisik') Hadir
                                    @elseif($attendance->status === 'hadir_online') Online
                                    @elseif($attendance->status === 'izin') Izin
                                    @else Alpha @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if($attendance->method === 'scan_qr') Scan QR
                                @elseif($attendance->method === 'manual') Manual
                                @else Upload @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium
                                            @if($attendance->validation_status === 'approved') bg-green-100 text-green-700
                                            @elseif($attendance->validation_status === 'pending') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700 @endif">
                                    @if($attendance->validation_status === 'approved') ✓
                                    @elseif($attendance->validation_status === 'pending') ⏳
                                    @else ✗ @endif
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

        @if($attendances->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
