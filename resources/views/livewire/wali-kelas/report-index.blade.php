<div>
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 text-primary-600 font-bold text-sm uppercase tracking-wider mb-1">
                <span class="material-symbols-rounded text-lg">description</span>
                <span>Laporan Kehadiran</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Presensi Perorangan</h1>
            <p class="text-gray-500 max-w-2xl mt-1">Cari dan lihat track record kehadiran orang tua santri secara
                mendalam.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="exportExcel"
                class="inline-flex items-center gap-2 px-6 py-3 bg-secondary-600 text-white rounded-2xl font-bold text-sm hover:bg-secondary-700 transition-all shadow-lg shadow-secondary-500/20 active:scale-95">
                <span class="material-symbols-rounded text-lg">table_chart</span>
                Export Excel
            </button>
            <button wire:click="exportPdf"
                class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-2xl font-bold text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-500/20 active:scale-95">
                <span class="material-symbols-rounded text-lg">picture_as_pdf</span>
                Export PDF
            </button>
            <button wire:click="resetFilters" 
                class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                <span class="material-symbols-rounded text-lg">restart_alt</span>
                Reset
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Cari Wali /
                    Santri</label>
                <div class="relative">
                    <span
                        class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Nama orang tua atau santri..."
                        class="pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary-500 w-full text-sm font-medium">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Tahun Ajaran</label>
                <select wire:model.live="academicYearId" class="w-full py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary-500 text-sm font-medium">
                    @foreach(\App\Models\AcademicYear::orderByDesc('name')->get() as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Kajian</label>
                <select wire:model.live="kajianId"
                    class="w-full py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary-500 text-sm font-medium">
                    <option value="">Semua Kajian</option>
                    @foreach($kajianEvents as $event)
                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Status</label>
                <select wire:model.live="status"
                    class="w-full py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary-500 text-sm font-medium">
                    <option value="">Semua Status</option>
                    <option value="hadir_fisik">Hadir Fisik</option>
                    <option value="hadir_online">Hadir Online</option>
                    <option value="izin">Izin</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Wali Murid</th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Santri (Anak)
                        </th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Kajian / Tanggal
                        </th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">
                            Status</th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Waktu
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $attendance)
                        <tr wire:key="attendance-{{ $attendance->id }}" class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center text-primary-700 font-bold text-xs">
                                        {{ substr($attendance->parent->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 leading-tight">
                                            {{ $attendance->parent->user->name }}</p>
                                        <p class="text-[10px] text-primary-600 font-extrabold uppercase tracking-tighter">
                                            {{ $attendance->parent->type_display }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-gray-700">{{ $attendance->student->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">NIS:
                                    {{ $attendance->student->nis }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-gray-800 leading-tight">
                                    {{ $attendance->kajianEvent->title }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">
                                    {{ $attendance->kajianEvent->date->translatedFormat('d F Y') }}</p>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                        {{ $attendance->status == 'hadir_fisik' ? 'bg-green-100 text-green-700' : ($attendance->status == 'hadir_online' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $attendance->status_display }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ $attendance->scanned_at ? $attendance->scanned_at->format('H:i') : '-' }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">WIB</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div
                                    class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-gray-200">
                                    <span class="material-symbols-rounded text-gray-300 text-4xl">search_off</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900">Tidak ada data ditemukan</h4>
                                <p class="text-gray-400 max-w-xs mx-auto text-sm mt-1">Coba ubah kata kunci pencarian atau
                                    filter kajian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="px-8 py-6 border-t border-gray-50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>