<div class="min-h-screen bg-gray-50 pb-6">
    <!-- Header -->
    <header
        class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-8 rounded-b-[2.5rem] shadow-lg relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M30 0 L60 30 L30 60 L0 30 Z\' fill=\'white\'/%3E%3C/svg%3E'); background-size: 30px 30px;">
        </div>

        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('panitia.scanner') }}"
                    class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-2xl flex items-center justify-center transition-all">
                    <span class="material-symbols-rounded">arrow_back</span>
                </a>
                <h1 class="text-xl font-bold">Jadwal Kajian</h1>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-rounded text-white">calendar_month</span>
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

        <!-- Upcoming Events Section -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-900">Agenda Mendatang</h3>
                <span
                    class="text-xs font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ count($upcomingEvents) }} Acara
                </span>
            </div>

            <div class="space-y-4">
                @forelse($upcomingEvents as $event)
                    <div wire:click="showDetail({{ $event->id }})"
                        class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all cursor-pointer group flex items-center gap-4">
                        <div
                            class="flex flex-col items-center justify-center w-14 h-14 bg-slate-50 group-hover:bg-primary-50 rounded-2xl transition-all border border-slate-100">
                            <span
                                class="text-[10px] font-black text-slate-400 uppercase tracking-tighter group-hover:text-primary-400 transition-colors">
                                {{ $event->date->translatedFormat('M') }}
                            </span>
                            <span class="text-xl font-black text-slate-800 group-hover:text-primary-700 transition-colors">
                                {{ $event->date->translatedFormat('d') }}
                            </span>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4
                                    class="font-bold text-slate-900 leading-tight group-hover:text-primary-700 transition-colors line-clamp-1">
                                    {{ $event->title }}
                                </h4>
                                @if($event->status === 'open')
                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase bg-green-100 text-green-700 rounded-full">Aktif</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] font-bold text-slate-400">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">schedule</span>
                                    {{ \Carbon\Carbon::parse($event->time_start)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($event->time_end)->format('H:i') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">location_on</span>
                                    {{ Str::limit($event->location ?? 'Aula Utama', 15) }}
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
                        <p class="text-slate-400 text-sm font-medium">Tidak ada jadwal kajian yang akan datang.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Back to Scanner Button -->
        <div class="mt-8">
            <a href="{{ route('panitia.scanner') }}"
                class="block w-full py-4 bg-primary-500 text-white rounded-2xl font-bold text-center shadow-lg shadow-primary-500/30 hover:bg-primary-600 transition-all">
                <span class="material-symbols-rounded align-middle mr-2">qr_code_scanner</span>
                Kembali ke Scanner
            </a>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div x-show="$wire.showEventDetail" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto"
        @click.self="$wire.closeDetail()">
        <div
            class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/40">
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

                        <button wire:click="closeDetail"
                            class="absolute top-6 right-6 w-10 h-10 bg-black/20 hover:bg-black/30 rounded-2xl text-white flex items-center justify-center transition-all z-20">
                            <span class="material-symbols-rounded">close</span>
                        </button>

                        <div
                            class="absolute inset-0 flex flex-col justify-end p-8 text-white bg-gradient-to-t from-slate-900/50 to-transparent">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-black uppercase tracking-widest">
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
                            <button wire:click="closeDetail"
                                class="w-full py-4 bg-slate-900 text-white rounded-[1.5rem] font-bold shadow-xl shadow-slate-900/20 hover:bg-slate-800 transition-all">
                                Tutup Detail
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
