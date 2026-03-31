<div>
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 text-primary-600 font-bold text-sm uppercase tracking-wider mb-1">
                <span class="material-symbols-rounded text-lg">meeting_room</span>
                <span>{{ $class->name }}</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Perkembangan Presensi Kelas</h1>
            <p class="text-gray-500 max-w-2xl mt-1">Pantau perkembangan kehadiran orang tua santri dalam setiap kajian
                rutin bulanan.</p>
        </div>

        <div class="flex items-center gap-4 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
            <select wire:model.live="filterPeriod" class="bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-primary-500 text-sm">
                <option value="monthly">Bulanan</option>
                <option value="semester">Semester</option>
                <option value="annual">Tahunan</option>
            </select>
            <div class="relative">
                <span
                    class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari santri..."
                    class="pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-primary-500 w-64 text-sm">
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Total Students -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-rounded text-6xl">school</span>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-rounded">groups</span>
            </div>
            <p class="text-gray-500 text-sm font-medium">Total Santri</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</h3>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-rounded text-6xl">trending_up</span>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-rounded">check_circle</span>
            </div>
            <p class="text-gray-500 text-sm font-medium">Rata-rata Kehadiran</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['attendance_rate'] }}%</h3>
        </div>

        <!-- Top Performer Info (Quick View) -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-rounded text-6xl">military_tech</span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-rounded">stars</span>
            </div>
            <p class="text-gray-500 text-sm font-medium">Kehadiran Sempurna</p>
            <h3 class="text-3xl font-bold text-gray-900">
                {{ $stats['top_attendees']->where('attendances_count', '>=', 3)->count() }}</h3>
        </div>

        <!-- Low Attendance -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-rounded text-6xl">warning</span>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-rounded">person_alert</span>
            </div>
            <p class="text-gray-500 text-sm font-medium">Perlu Perhatian</p>
            <h3 class="text-3xl font-bold text-gray-900">
                {{ $stats['needs_attention']->where('attendances_count', '<', 2)->count() }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Main Student Table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-white to-gray-50/50">
                    <div>
                        <h3 class="font-extrabold text-gray-900 flex items-center gap-3 text-lg">
                            <span class="w-2 h-8 bg-primary-500 rounded-full shadow-lg shadow-primary-200"></span>
                            Daftar Santri & Orang Tua
                        </h3>
                        <p class="text-xs text-gray-400 mt-1 font-medium ml-5">Manajemen data presensi dan kontak wali murid</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/30">
                                <th class="px-8 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Santri</th>
                                <th class="px-8 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Wali / No. HP</th>
                                <th class="px-8 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em] text-center">Presensi</th>
                                <th class="px-8 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($students as $student)
                                <tr class="hover:bg-gray-50/50 transition-all duration-300 group" wire:key="student-{{ $student->id }}">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-emerald-600 rounded-2xl flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-500">
                                                    {{ substr($student->name, 0, 2) }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-gray-900 group-hover:text-primary-600 transition-colors uppercase tracking-tight text-sm">{{ $student->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">NIS: {{ $student->nis }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="space-y-4">
                                            @forelse($student->parents as $parent)
                                                <div class="flex items-start gap-3">
                                                    <div class="mt-0.5">
                                                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $parent->type == 'father' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-pink-50 text-pink-600 border-pink-100' }}">
                                                            {{ $parent->type_display }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-bold text-gray-800 leading-tight">{{ $parent->user->name }}</p>
                                                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ $parent->user->phone ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="flex items-center gap-2 text-amber-500 bg-amber-50 px-3 py-1 rounded-lg border border-amber-100 w-fit">
                                                    <span class="material-symbols-rounded text-sm">warning</span>
                                                    <span class="text-[10px] font-bold uppercase">Data Wali Belum Diisi</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="inline-flex flex-col items-center group/badge">
                                            <div class="px-4 py-1.5 bg-gray-50 text-gray-700 rounded-2xl border border-gray-100 group-hover/badge:bg-emerald-50 group-hover/badge:text-emerald-700 group-hover/badge:border-emerald-100 transition-all duration-500 flex items-center gap-2 shadow-sm">
                                                <span class="text-xs font-black tracking-tight">{{ $student->attendances_count }}</span>
                                                <span class="text-[9px] font-extrabold uppercase tracking-widest opacity-60">Hadir</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <button wire:click="openDetail({{ $student->id }})"
                                                class="w-10 h-10 flex items-center justify-center text-primary-600 bg-primary-50/50 hover:bg-primary-500 hover:text-white rounded-2xl transition-all duration-300 shadow-sm border border-primary-100/50" 
                                                title="Detail Lengkap">
                                                <span class="material-symbols-rounded text-lg">visibility</span>
                                            </button>
                                            
                                            <div class="h-8 w-px bg-gray-100 mx-1"></div>

                                            <div class="flex items-center gap-2">
                                                @foreach($student->parents as $parent)
                                                    @if($parent->user->phone)
                                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $parent->user->phone)) }}" target="_blank"
                                                            class="w-10 h-10 flex items-center justify-center {{ $parent->type == 'father' ? 'text-blue-500 bg-blue-50/50 hover:bg-blue-500 border-blue-100/50' : 'text-pink-500 bg-pink-50/50 hover:bg-pink-500 border-pink-100/50' }} hover:text-white rounded-2xl transition-all duration-300 shadow-sm border" 
                                                            title="WhatsApp {{ $parent->type_display }}">
                                                            <span class="material-symbols-rounded text-lg">chat</span>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-8 border-t border-gray-50 bg-gray-50/30">
                    {{ $students->links() }}
                </div>
            </div>
        </div>

        <!-- Right Side: Trends & Attention -->
        <div class="space-y-8">
            <!-- Summary Chart (Simulated with Bars) -->
            <div class="bg-primary-600 rounded-[2rem] p-6 shadow-lg shadow-primary-500/25 text-white">
                <h3 class="font-bold mb-6 flex items-center justify-between">
                    Perkembangan 4 Kajian Terakhir
                    <span class="material-symbols-rounded">bar_chart</span>
                </h3>
                <div class="flex items-end justify-between h-32 gap-3">
                    @php
                        $lastEvents = $kajianEvents->take(4)->reverse();
                    @endphp
                    @foreach($lastEvents as $event)
                        @php
                            $eventPresent = \App\Models\Attendance::where('kajian_event_id', $event->id)
                                ->whereHas('student', function ($q) use ($class) {
                                    $q->where('class_id', $class->id); })
                                ->where('validation_status', 'approved')
                                ->count();
                            $height = $class->students()->count() > 0 ? ($eventPresent / $class->students()->count()) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 group">
                            <div class="w-full bg-white/20 rounded-lg relative overflow-hidden h-full">
                                <div class="absolute bottom-0 left-0 w-full bg-white transition-all duration-1000"
                                    style="height: {{ $height }}%"></div>
                            </div>
                            <span
                                class="text-[10px] uppercase font-bold text-white/60 truncate w-full text-center">{{ date('M', strtotime($event->date)) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 pt-6 border-t border-white/10">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/70">Kajian Terakhir</span>
                        <span class="font-bold">{{ $lastEvents->last()->title ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Needs Attention List -->
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                    Perlu Perhatian (Absen Terbanyak)
                </h3>
                <div class="space-y-4">
                    @forelse($stats['needs_attention'] as $stu)
                        <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center font-bold">
                                    {{ substr($stu->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 leading-none mb-1">{{ $stu->name }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">{{ $stu->attendances_count }}
                                        kali hadir</p>
                                </div>
                            </div>
                            <div
                                class="text-[10px] font-extrabold text-red-500 bg-red-50 px-2 py-1 rounded-lg uppercase tracking-wider">
                                Red Note
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 text-sm py-4 italic">Semua santri rajin hadir</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Attendees -->
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-amber-500 rounded-full"></span>
                    Presensi Terbaik
                </h3>
                <div class="space-y-4">
                    @foreach($stats['top_attendees'] as $stu)
                        <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                                    {{ substr($stu->name, 0, 1) }}
                                </div>
                                <p class="text-sm font-bold text-gray-900">{{ $stu->name }}</p>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full border-2 border-amber-400 flex items-center justify-center text-[10px] font-bold text-amber-700 bg-amber-50">
                                {{ $stu->attendances_count }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Student & Parent Detail Modal -->
    @if($showDetailModal && $selectedStudent)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" wire:click="closeDetail" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="relative bg-primary-600 h-32 overflow-hidden">
                        <div class="absolute inset-0 opacity-20">
                            <span class="material-symbols-rounded text-[10rem] -bottom-8 -right-8 absolute rotate-12">person_search</span>
                        </div>
                        <button wire:click="closeDetail" class="absolute top-6 right-6 p-2 bg-white/10 hover:bg-white/20 text-white rounded-full transition-all">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <div class="px-8 pb-8 -mt-12 relative">
                        <div class="flex flex-col md:flex-row gap-8">
                            <!-- Left: Profile Info -->
                            <div class="md:w-1/3">
                                <div class="bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100">
                                    <div class="w-24 h-24 bg-primary-50 text-primary-600 rounded-[2rem] flex items-center justify-center text-4xl font-bold mb-4 mx-auto border-4 border-white shadow-lg">
                                        {{ substr($selectedStudent->name, 0, 2) }}
                                    </div>
                                    <h2 class="text-xl font-extrabold text-gray-900 text-center leading-tight">{{ $selectedStudent->name }}</h2>
                                    <p class="text-primary-600 font-bold text-xs uppercase tracking-widest text-center mt-2">{{ $selectedStudent->nis }}</p>
                                    
                                    <div class="mt-8 space-y-4">
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                            <span class="material-symbols-rounded text-gray-400">meeting_room</span>
                                            <div>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase">Kelas</p>
                                                <p class="text-sm font-bold text-gray-700">{{ $selectedStudent->classRoom->name }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                            <span class="material-symbols-rounded text-gray-400">calendar_month</span>
                                            <div>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase">Kehadiran</p>
                                                <p class="text-sm font-bold text-gray-700">{{ $selectedStudent->attendances_count }} Sesi</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent List -->
                                <div class="mt-6 space-y-4">
                                    <h4 class="text-sm font-bold text-gray-900 px-2 flex items-center gap-2">
                                        <span class="w-1.5 h-4 bg-primary-500 rounded-full"></span>
                                        Data Wali
                                    </h4>
                                    @foreach($selectedStudent->parents as $parent)
                                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-[10px] font-extrabold px-2 py-0.5 bg-primary-100 text-primary-700 rounded-lg uppercase tracking-wider">
                                                    {{ $parent->type_display }}
                                                </span>
                                            </div>
                                            <p class="font-bold text-gray-900">{{ $parent->user->name }}</p>
                                            <p class="text-xs text-gray-500 mb-3">{{ $parent->user->phone ?? 'Tidak ada No. HP' }}</p>
                                            
                                            <div class="space-y-2 pt-2 border-t border-gray-200/50">
                                                <div class="flex items-center gap-2 text-[10px] text-gray-500">
                                                    <span class="material-symbols-rounded text-sm">work</span>
                                                    <span>{{ $parent->occupation ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-[10px] text-gray-500">
                                                    <span class="material-symbols-rounded text-sm">location_on</span>
                                                    <span class="leading-tight">{{ $parent->address ?? '-' }}</span>
                                                </div>
                                            </div>

                                            @if($parent->user->phone)
                                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $parent->user->phone)) }}" target="_blank"
                                                    class="mt-4 w-full py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-lg shadow-green-200">
                                                    <span class="material-symbols-rounded text-sm">chat</span>
                                                    Hubungi WhatsApp
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Right: Attendance Track Record -->
                            <div class="md:flex-1">
                                <div class="bg-white rounded-3xl p-8 border border-gray-100 h-full">
                                    <h3 class="text-xl font-extrabold text-gray-900 mb-8 flex items-center justify-between">
                                        Track Record Presensi
                                        <span class="text-xs font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-full uppercase">Timeline</span>
                                    </h3>

                                    <div class="relative pl-8 space-y-8 before:content-[''] before:absolute before:left-0 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                                        @forelse($selectedStudent->attendances->sortByDesc('kajianEvent.date') as $attendance)
                                            <div class="relative">
                                                <span class="absolute -left-[37px] top-1 w-4 h-4 rounded-full border-4 border-white shadow-sm ring-2 
                                                    {{ $attendance->validation_status == 'approved' ? 'ring-green-500 bg-green-500' : 'ring-amber-500 bg-amber-500' }}">
                                                </span>
                                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-1">
                                                    <h5 class="font-extrabold text-gray-900">{{ $attendance->kajianEvent->title }}</h5>
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $attendance->kajianEvent->date->translatedFormat('d F Y') }}</span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-3">
                                                    @if($attendance->parent)
                                                        <div class="flex items-center gap-2 px-3 py-1 bg-gray-50 rounded-xl border border-gray-100">
                                                            <span class="material-symbols-rounded text-xs text-primary-500">person</span>
                                                            <span class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">
                                                                {{ $attendance->parent->user->name }} 
                                                                <span class="text-gray-400 ml-1">({{ $attendance->parent->type_display }})</span>
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider
                                                        {{ $attendance->status == 'hadir_fisik' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                                                        {{ $attendance->status_display }}
                                                    </span>
                                                    <span class="text-[10px] font-bold text-gray-400 flex items-center gap-1">
                                                        <span class="material-symbols-rounded text-sm uppercase">verified_user</span>
                                                        {{ $attendance->validation_status == 'approved' ? 'Terverifikasi' : 'Pending' }}
                                                    </span>
                                                </div>
                                                @if($attendance->notes)
                                                    <p class="mt-2 text-xs text-gray-500 italic bg-gray-50 p-2 rounded-xl">"{{ $attendance->notes }}"</p>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="py-12 text-center">
                                                <div class="w-16 h-16 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                    <span class="material-symbols-rounded text-gray-300">event_busy</span>
                                                </div>
                                                <p class="text-gray-400 text-sm font-medium italic">Belum ada riwayat kehadiran tercatat.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>