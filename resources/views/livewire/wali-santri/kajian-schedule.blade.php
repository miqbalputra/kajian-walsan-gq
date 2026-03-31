<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-500 pb-24">
    <!-- Header -->
    <header
        class="bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 text-white px-6 py-8 rounded-b-[2.5rem] shadow-lg relative overflow-hidden">
        <!-- Decorative Background (Cross Pattern from Welcome) -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
        </div>

        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('wali-santri.dashboard') }}"
                    class="w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-2xl flex items-center justify-center transition-all">
                    <span class="material-symbols-rounded">arrow_back</span>
                </a>
                <h1 class="text-xl font-bold">Jadwal Kajian</h1>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('logout-form').submit()" 
                    class="p-2 bg-white/20 hover:bg-red-500/20 hover:text-red-200 rounded-full transition-all text-white border border-white/10"
                    title="Keluar">
                    <span class="material-symbols-rounded">logout</span>
                </button>
            </div>
        </div>
    </header>

    <div class="px-4 -mt-6 relative z-20">
        <!-- Calendar Card -->
        <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
            <!-- Calendar Controls -->
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-black text-slate-900">
                    {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}
                </h2>
                <div class="flex items-center gap-2">
                    <button wire:click="prevMonth"
                        class="w-10 h-10 flex items-center justify-center bg-slate-50 hover:bg-primary-50 hover:text-primary-600 text-slate-400 rounded-xl transition-all border border-slate-100">
                        <span class="material-symbols-rounded">chevron_left</span>
                    </button>
                    <button wire:click="nextMonth"
                        class="w-10 h-10 flex items-center justify-center bg-slate-50 hover:bg-primary-50 hover:text-primary-600 text-slate-400 rounded-xl transition-all border border-slate-100">
                        <span class="material-symbols-rounded">chevron_right</span>
                    </button>
                </div>
            </div>

            <!-- Days Header -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 py-2">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1">
                @foreach($calendarDays as $dayInfo)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $dayInfo['year'], $dayInfo['month'], $dayInfo['day']);
                        $hasEvents = isset($events[$dateStr]);
                        $isToday = $dateStr === now()->toDateString();
                    @endphp
                    <div class="aspect-square relative flex flex-col items-center justify-center p-1 rounded-2xl transition-all
                            {{ $dayInfo['current_month'] ? 'bg-white' : 'bg-slate-50/50 opacity-30' }}
                            {{ $isToday ? 'ring-2 ring-primary-500 ring-offset-2' : '' }}
                        ">
                        <span class="text-sm font-bold {{ $isToday ? 'text-primary-600' : 'text-slate-700' }}">
                            {{ $dayInfo['day'] }}
                        </span>

                        @if($hasEvents)
                            <div class="flex items-center justify-center gap-0.5 mt-1">
                                @foreach($events[$dateStr] as $event)
                                    <button wire:click="showDetail({{ $event['id'] }})"
                                        class="w-1.5 h-1.5 rounded-full bg-primary-500 shadow-sm shadow-primary-500/50"
                                        title="{{ $event['title'] }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Event List for Selected Month -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-900">Agenda Terdekat</h3>
                <span
                    class="text-xs font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ count(collect($events)->flatten(1)) }} Acara
                </span>
            </div>

            <div class="space-y-4">
                @forelse(collect($events)->flatten(1)->sortBy('date')->values() as $event)
                    <div wire:click="showDetail({{ $event['id'] }})"
                        class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all cursor-pointer group flex items-center gap-4">
                        <div
                            class="flex flex-col items-center justify-center w-14 h-14 bg-slate-50 group-hover:bg-primary-50 rounded-2xl transition-all border border-slate-100">
                            <span
                                class="text-[10px] font-black text-slate-400 uppercase tracking-tighter group-hover:text-primary-400 transition-colors">
                                {{ \Carbon\Carbon::parse($event['date'])->translatedFormat('M') }}
                            </span>
                            <span class="text-xl font-black text-slate-800 group-hover:text-primary-700 transition-colors">
                                {{ \Carbon\Carbon::parse($event['date'])->translatedFormat('d') }}
                            </span>
                        </div>

                        <div class="flex-1">
                            <h4
                                class="font-bold text-slate-900 leading-tight mb-1 group-hover:text-primary-700 transition-colors line-clamp-1">
                                {{ $event['title'] }}
                            </h4>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] font-bold text-slate-400">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">schedule</span>
                                    {{ \Carbon\Carbon::parse($event['time_start'])->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($event['time_end'])->format('H:i') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">location_on</span>
                                    {{ Str::limit($event['location'] ?? 'Aula Utama', 15) }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300 group-hover:text-primary-500 group-hover:bg-primary-50 transition-all">
                            <span class="material-symbols-rounded">chevron_right</span>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-10 rounded-[2rem] border border-slate-100 text-center shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-rounded text-3xl text-slate-200">event_busy</span>
                        </div>
                        <p class="text-slate-400 text-sm font-medium">Tidak ada jadwal kajian bulan ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div x-show="$wire.showEventDetail" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto"
        @click.self="$wire.showEventDetail = false">
        <div
            class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/40 backdrop-blur-sm">
            <div x-show="$wire.showEventDetail" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-8 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-8 sm:scale-95"
                class="bg-white w-full max-w-lg rounded-t-[2.5rem] sm:rounded-[2.5rem] overflow-hidden relative">
                @if($selectedEvent)
                    <div class="relative h-48 bg-gradient-to-br from-primary-600 to-primary-700">
                        <div class="absolute inset-0 opacity-20"
                            style="background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M50 0 L100 50 L50 100 L0 50 Z\' fill=\'none\' stroke=\'white\' stroke-width=\'1\'/%3E%3C/svg%3E'); background-size: 40px 40px;">
                        </div>

                        <button @click="$wire.showEventDetail = false"
                            class="absolute top-6 right-6 w-10 h-10 bg-black/10 hover:bg-black/20 backdrop-blur-md rounded-2xl text-white flex items-center justify-center transition-all group z-20">
                            <span
                                class="material-symbols-rounded group-hover:rotate-90 transition-all duration-300">close</span>
                        </button>

                        <div
                            class="absolute inset-0 flex flex-col justify-end p-8 text-white bg-gradient-to-t from-slate-900/50 to-transparent">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest">
                                    {{ $selectedEvent->status === 'open' ? 'Terbuka' : 'Mendatang' }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-black leading-tight">{{ $selectedEvent->title }}</h3>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="space-y-6">
                            <!-- Date & Time -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100">
                                    <p
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                        <span class="material-symbols-rounded text-sm">event</span> Tanggal
                                    </p>
                                    <p class="text-sm font-black text-slate-800">
                                        {{ $selectedEvent->date->translatedFormat('d F Y') }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100">
                                    <p
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                        <span class="material-symbols-rounded text-sm">schedule</span> Waktu
                                    </p>
                                    <p class="text-sm font-black text-slate-800">{{ $selectedEvent->time_range }}</p>
                                </div>
                            </div>

                            <!-- Detail Info -->
                            <div class="space-y-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600 shrink-0">
                                        <span class="material-symbols-rounded">person</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Pemateri</p>
                                        <p class="text-sm font-bold text-slate-800">
                                            {{ $selectedEvent->speaker ?? 'Konfirmasi' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 bg-secondary-50 rounded-xl flex items-center justify-center text-secondary-600 shrink-0">
                                        <span class="material-symbols-rounded">location_on</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Lokasi</p>
                                        <p class="text-sm font-bold text-slate-800">
                                            {{ $selectedEvent->location ?? 'Aula Pesantren' }}</p>
                                    </div>
                                </div>

                                @if($selectedEvent->description)
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                                            <span class="material-symbols-rounded">notes</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                                Keterangan</p>
                                            <p class="text-sm font-medium text-slate-600 leading-relaxed">
                                                {{ $selectedEvent->description }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-8">
                            <button @click="$wire.showEventDetail = false"
                                class="w-full py-4 bg-slate-900 text-white rounded-[1.5rem] font-bold shadow-xl shadow-slate-900/20 hover:bg-slate-800 transition-all">
                                Tutup Detail
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 inset-x-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-t border-slate-100 dark:border-slate-800 px-6 py-3 flex items-center justify-around z-[60] transition-colors">
        <a href="{{ route('wali-santri.dashboard') }}"
            class="flex flex-col items-center gap-1 {{ request()->routeIs('wali-santri.dashboard') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400' }}">
            <span class="material-symbols-rounded {{ request()->routeIs('wali-santri.dashboard') ? 'fill-1' : '' }}">home</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Home</span>
        </a>
        <a href="{{ route('wali-santri.schedule') }}"
            class="flex flex-col items-center gap-1 {{ request()->routeIs('wali-santri.schedule') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400' }}">
            <span class="material-symbols-rounded {{ request()->routeIs('wali-santri.schedule') ? 'fill-1' : '' }}">calendar_month</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Jadwal</span>
        </a>
        <a href="{{ route('wali-santri.profile') }}"
            class="flex flex-col items-center gap-1 {{ request()->routeIs('wali-santri.profile') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400' }}">
            <span class="material-symbols-rounded {{ request()->routeIs('wali-santri.profile') ? 'fill-1' : '' }}">person</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Profil</span>
        </a>
    </nav>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
</div>