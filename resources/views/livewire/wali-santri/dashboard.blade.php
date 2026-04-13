<div class="min-h-screen flex flex-col pb-20">
    <!-- Header -->
    <header class="bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 text-white px-4 py-8 rounded-b-[2.5rem] shadow-lg relative overflow-hidden transition-all duration-500">
        <!-- Decorative Background (Cross Pattern from Welcome) -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
        </div>
        <div class="relative z-10 flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                @php
                    $avatar = auth()->user()->avatar ?? 'icon:person|bg-primary-400';
                    $avatarParts = explode('|', str_replace('icon:', '', $avatar));
                    $icon = $avatarParts[0] ?? 'person';
                    $color = $avatarParts[1] ?? 'bg-primary-400';
                @endphp
                <div class="w-10 h-10 rounded-xl {{ $color }} flex items-center justify-center border border-white/20 shadow-lg">
                    <span class="material-symbols-rounded text-xl fill-1">{{ $icon }}</span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/60">Presensi Kajian Wali Santri</span>
            </div>
            <div class="flex items-center gap-2">
                <x-theme-toggle />
                <button onclick="document.getElementById('logout-form').submit()" 
                    class="p-2 bg-white/10 hover:bg-red-500/20 hover:text-red-200 rounded-full transition-all text-white border border-white/10"
                    title="Keluar">
                    <span class="material-symbols-rounded">logout</span>
                </button>
            </div>
        </div>
        <p class="text-white/80 text-sm">Assalamu'alaikum,</p>
        <h1 class="text-2xl font-black">{{ $this->parent?->type === 'father' ? 'Abu' : 'Ummu' }}
            {{ auth()->user()->name }}
        </h1>

        @if($this->parent?->students->count() > 0)
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($this->parent->students as $child)
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm">{{ $child->name }}</span>
                @endforeach
            </div>
        @endif
    </header>

    <!-- Flash Messages -->
    <div class="px-4 mt-4">
        @if (session()->has('message'))
            <div class="p-4 bg-green-50 dark:bg-emerald-950/30 border border-green-200 dark:border-emerald-900/50 text-green-700 dark:text-emerald-400 rounded-xl flex items-center gap-3 mb-4 transition-colors">
                <span class="material-symbols-rounded">check_circle</span>
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-400 rounded-xl flex items-center gap-3 mb-4 transition-colors">
                <span class="material-symbols-rounded">error</span>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 px-4 space-y-4">
        <!-- Survey/Feedback Banner -->
        @foreach($this->pendingFeedbackEvents as $event)
            <div wire:key="survey-banner-{{ $event->id }}" 
                class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl shadow-xl p-5 text-white flex gap-4 items-center relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700 z-0"></div>
                
                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0 border border-white/30 relative z-10">
                    <span class="material-symbols-rounded text-2xl animate-bounce">rate_review</span>
                </div>
                <div class="flex-1 relative z-10">
                    <p class="text-white/80 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Berikan Masukan</p>
                    <h3 class="font-bold text-sm leading-tight">Bagaimana kajian "{{ $event->title }}" kemarin?</h3>
                </div>
                <button type="button" wire:click.stop="openFeedbackModal({{ $event->id }})" 
                    class="px-4 py-2 bg-white text-indigo-600 rounded-xl font-bold text-xs shadow-lg hover:bg-indigo-50 transition-all active:scale-95 shrink-0 relative z-10">
                    Isi Survey
                </button>
            </div>
        @endforeach
        <!-- Upcoming Event Banner -->
        @if($this->upcomingEvent && !$this->activeEvent)
            <div x-data="{ 
                    showUpcoming: localStorage.getItem('hide-event-{{ $this->upcomingEvent->id }}') !== 'true' 
                }" 
                x-show="showUpcoming" 
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"                class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-3xl shadow-xl shadow-primary-500/20 p-5 text-white flex gap-4 items-center relative overflow-hidden group border border-white/10">
                <!-- Decorative Circle -->
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>

                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/30 shadow-inner">
                    <span class="material-symbols-rounded text-3xl animate-pulse">calendar_clock</span>
                </div>
                <div class="flex-1 relative z-10">
                    <p class="text-white/80 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Agenda Terdekat</p>
                    <h3 class="font-bold text-lg leading-tight">{{ $this->upcomingEvent->title }}</h3>
                    <div class="flex items-center gap-3 mt-2">
                        <div class="flex items-center gap-1.5 py-1 px-2.5 bg-white/10 rounded-lg backdrop-blur-sm border border-white/10">
                            <span class="material-symbols-rounded text-base">event</span>
                            <span class="text-xs font-bold">{{ $this->upcomingEvent->date->translatedFormat('d M') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 py-1 px-2.5 bg-white/10 rounded-lg backdrop-blur-sm border border-white/10">
                            <span class="material-symbols-rounded text-base">schedule</span>
                            <span class="text-xs font-bold text-white/90">{{ \Carbon\Carbon::parse($this->upcomingEvent->time_start)->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <button @click="showUpcoming = false; localStorage.setItem('hide-event-{{ $this->upcomingEvent->id }}', 'true')" 
                        class="p-2 hover:bg-white/10 rounded-full transition-colors text-white/50 hover:text-white"
                        title="Sembunyikan">
                        <span class="material-symbols-rounded text-sm font-bold">close</span>
                    </button>
                    <a href="{{ route('wali-santri.schedule') }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-full transition-colors shadow-lg">
                        <span class="material-symbols-rounded font-bold">chevron_right</span>
                    </a>
                </div>
            </div>
        @endif
        @if($this->activeEvent)
            <!-- Active Kajian Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-secondary-500 to-secondary-600 text-white px-4 py-3">
                    <p class="text-white/80 text-xs font-medium uppercase tracking-wide">Kajian Hari Ini</p>
                    <h2 class="font-bold text-lg">{{ $this->activeEvent->title }}</h2>
                </div>

                <div class="p-4">
                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-rounded text-lg">schedule</span>
                            <span>{{ $this->activeEvent->time_range }}</span>
                        </div>
                        @if($this->activeEvent->speaker)
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-rounded text-lg">person</span>
                                <span>{{ $this->activeEvent->speaker }}</span>
                            </div>
                        @endif
                    </div>

                    @if($this->myAttendanceToday)
                        <!-- Already Attended -->
                        @if($this->myAttendanceToday->validation_status === 'rejected')
                            <!-- DITOLAK - Tampilkan alasan & tombol re-upload -->
                            <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-rounded text-red-500 text-3xl mt-0.5">error</span>
                                    <div class="flex-1">
                                        <p class="font-semibold text-red-700">Bukti Presensi Ditolak</p>
                                        <p class="text-red-600 text-sm mt-1">
                                            Status: {{ ucfirst(str_replace('_', ' ', $this->myAttendanceToday->status)) }}
                                        </p>
                                        @if($this->myAttendanceToday->rejection_reason)
                                            <div class="mt-2 p-3 bg-white/80 rounded-lg border border-red-100">
                                                <p class="text-xs font-semibold text-red-500 uppercase tracking-wider mb-1">Alasan penolakan:</p>
                                                <p class="text-sm text-gray-700">{{ $this->myAttendanceToday->rejection_reason }}</p>
                                            </div>
                                        @endif
                                        <button wire:click="openReuploadModal({{ $this->myAttendanceToday->id }})"
                                            class="mt-3 w-full py-3 bg-red-600 text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-red-700 transition-colors shadow-lg shadow-red-500/20">
                                            <span class="material-symbols-rounded text-xl">upload_file</span>
                                            Upload Ulang Bukti
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- PENDING / APPROVED -->
                            <div class="p-4 bg-green-50 rounded-xl text-center">
                                <span class="material-symbols-rounded text-green-600 text-4xl">check_circle</span>
                                <p class="font-semibold text-green-700 mt-2">Anda sudah tercatat</p>
                                <p class="text-green-600 text-sm">
                                    Status: {{ ucfirst(str_replace('_', ' ', $this->myAttendanceToday->status)) }}
                                    @if($this->myAttendanceToday->validation_status === 'pending')
                                        <span class="text-yellow-600 text-xs">(Menunggu validasi)</span>
                                    @elseif($this->myAttendanceToday->validation_status === 'approved' && in_array($this->myAttendanceToday->method, ['upload', 'manual']))
                                        <span class="text-green-600 text-xs">(Divalidasi)</span>
                                    @endif
                                </p>
                                @if($this->myAttendanceToday->proof_file)
                                    <div class="mt-3">
                                        <a href="{{ $this->myAttendanceToday->proof_file }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 text-primary-700 rounded-xl font-bold text-xs hover:bg-primary-200 transition-all border border-primary-200">
                                            <span class="material-symbols-rounded text-base">visibility</span>
                                            Lihat Bukti Terkirim
                                        </a>
                                    </div>
                                @endif

                                {{-- Tombol ganti foto: hanya muncul jika masih PENDING (belum diapprove) --}}
                                @if($this->myAttendanceToday->validation_status === 'pending' && in_array($this->myAttendanceToday->method, ['upload']))
                                    <div class="mt-3 pt-3 border-t border-green-100">
                                        <button wire:click="openReuploadModal({{ $this->myAttendanceToday->id }}, true)"
                                            class="w-full py-2.5 bg-yellow-500 text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:bg-yellow-600 transition-colors shadow shadow-yellow-400/20">
                                            <span class="material-symbols-rounded text-lg">swap_horiz</span>
                                            Ganti Foto Bukti
                                        </button>
                                        <p class="text-[10px] text-yellow-600 mt-1.5">Foto lama akan otomatis dihapus</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <!-- Show QR Code -->
                            <button wire:click="$set('showQrModal', true)"
                                class="w-full py-4 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl font-semibold flex items-center justify-center gap-3 shadow-lg shadow-primary-500/25">
                                <span class="material-symbols-rounded text-2xl">qr_code_2</span>
                                Tampilkan QR Code Saya
                            </button>

                            <div class="grid grid-cols-2 gap-3">
                                <!-- Online Attendance -->
                                <button wire:click="$set('showOnlineModal', true)"
                                    class="py-3 bg-secondary-50 text-secondary-700 rounded-xl font-medium flex items-center justify-center gap-2 hover:bg-secondary-100 transition-colors">
                                    <span class="material-symbols-rounded">videocam</span>
                                    Hadir Online
                                </button>

                                <!-- Permission/Izin -->
                                <button wire:click="$set('showIzinModal', true)"
                                    class="py-3 bg-yellow-50 text-yellow-700 rounded-xl font-medium flex items-center justify-center gap-2 hover:bg-yellow-100 transition-colors">
                                    <span class="material-symbols-rounded">edit_document</span>
                                    Izin
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- No Active Kajian -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-gray-400 text-4xl">event_busy</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Tidak Ada Kajian Aktif</h3>
                <p class="text-gray-500 text-sm">Belum ada kajian yang dijadwalkan untuk hari ini.</p>
            </div>
        @endif

        <!-- Rejected Attendances Alert (kajian sebelumnya yang ditolak) -->
        @if($this->rejectedAttendances->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                <div class="p-4 border-b border-red-100 bg-red-50">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-red-500">warning</span>
                        <h3 class="font-semibold text-red-800">Bukti Ditolak — Perlu Upload Ulang</h3>
                    </div>
                </div>
                <div class="divide-y divide-red-50">
                    @foreach($this->rejectedAttendances as $rejected)
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $rejected->kajianEvent?->title }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $rejected->kajianEvent?->date->translatedFormat('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">
                                        Status: {{ $rejected->status === 'hadir_online' ? 'Hadir Online' : 'Izin' }}
                                    </p>
                                    @if($rejected->rejection_reason)
                                        <div class="mt-2 p-2 bg-red-50 rounded-lg border border-red-100">
                                            <p class="text-xs font-semibold text-red-500 mb-0.5">Alasan penolakan:</p>
                                            <p class="text-xs text-gray-700">{{ $rejected->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                                <button wire:click="openReuploadModal({{ $rejected->id }})"
                                    class="shrink-0 px-3 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold flex items-center gap-1 hover:bg-red-700 transition-colors shadow-md shadow-red-500/20">
                                    <span class="material-symbols-rounded text-sm">upload_file</span>
                                    Upload Ulang
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Attendance History -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Riwayat Kehadiran</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->attendanceHistory as $attendance)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                                @if($attendance->status === 'hadir_fisik') bg-green-100
                                                @elseif($attendance->status === 'hadir_online') bg-blue-100
                                                @elseif($attendance->status === 'izin') bg-yellow-100
                                                @else bg-red-100 @endif">
                            <span class="material-symbols-rounded text-2xl
                                                    @if($attendance->status === 'hadir_fisik') text-green-600
                                                    @elseif($attendance->status === 'hadir_online') text-blue-600
                                                    @elseif($attendance->status === 'izin') text-yellow-600
                                                    @else text-red-600 @endif">
                                @if($attendance->status === 'hadir_fisik') check_circle
                                @elseif($attendance->status === 'hadir_online') videocam
                                @elseif($attendance->status === 'izin') description
                                @else cancel @endif
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $attendance->kajianEvent?->title }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $attendance->kajianEvent?->date->translatedFormat('d M Y') }}
                                @if($attendance->validation_status === 'rejected')
                                    <br><span class="text-xs text-red-500 font-medium">Ditolak:
                                        {{ $attendance->rejection_reason }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                @if($attendance->status === 'hadir_fisik') bg-green-100 text-green-700
                                                @elseif($attendance->status === 'hadir_online') bg-blue-100 text-blue-700
                                                @elseif($attendance->status === 'izin') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700 @endif">
                                @if($attendance->status === 'hadir_fisik') Hadir
                                @elseif($attendance->status === 'hadir_online') Online
                                @elseif($attendance->status === 'izin') Izin
                                @else Alpha @endif
                            </span>
                            @if($attendance->validation_status === 'pending')
                                <span class="text-[10px] text-yellow-600 font-medium">Menunggu Validasi</span>
                            @elseif($attendance->validation_status === 'approved' && in_array($attendance->method, ['upload', 'manual']))
                                <span class="text-[10px] text-green-600 font-medium flex items-center gap-0.5">
                                    <span class="material-symbols-rounded text-[12px]">verified</span>
                                    Divalidasi
                                </span>
                                @if($attendance->proof_file)
                                    <a href="{{ $attendance->proof_file }}" target="_blank" class="mt-1 flex items-center gap-1 text-[10px] text-primary-600 hover:text-primary-700 font-bold bg-primary-50 px-1.5 py-0.5 rounded-md transition-colors">
                                        <span class="material-symbols-rounded text-[12px]">visibility</span>
                                        Lihat Bukti
                                    </a>
                                @endif
                            @elseif($attendance->validation_status === 'rejected')
                                <span class="text-[10px] text-red-600 font-medium flex items-center gap-0.5">
                                    <span class="material-symbols-rounded text-[12px]">cancel</span>
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <span class="material-symbols-rounded text-4xl text-gray-300">history</span>
                        <p class="mt-2">Belum ada riwayat kehadiran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- PWA Notification Promo -->
    <div class="px-4 mt-8 mb-6">
        <div class="bg-white rounded-3xl border border-dashed border-gray-300 p-6 text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-rounded text-3xl">notifications_active</span>
            </div>
            <h4 class="font-bold text-gray-900">Aktifkan Pengingat</h4>
            <p class="text-xs text-gray-500 mt-1 mb-4">Dapatkan notifikasi langsung di HP Anda agar tidak melewatkan info kajian penting.</p>
            
            <button onclick="initPushNotification()" 
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all active:scale-95 shadow-lg shadow-gray-200">
                <span class="material-symbols-rounded text-lg">touch_app</span>
                Minta Izin Notifikasi
            </button>
            <button onclick="testLocalNotification()" class="block w-full mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest hover:text-primary-600 transition-colors">
                Kirim Tes Notifikasi
            </button>
        </div>
    </div>

    <!-- QR Code Modal -->
    @if($showQrModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" wire:click="$set('showQrModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10 text-center">
                    <button wire:click="$set('showQrModal', false)"
                        class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-full">
                        <span class="material-symbols-rounded">close</span>
                    </button>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">QR Code Anda</h3>
                    <p class="text-gray-500 text-sm mb-4">Tunjukkan ke panitia untuk scan</p>

                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-4 inline-block mb-4">
                        {!! $this->qrCodeSvg !!}
                    </div>

                    <p class="text-xs text-gray-400 font-mono">{{ $this->parent?->qr_code_string }}</p>

                    <div class="mt-4 p-3 bg-gray-50 rounded-xl">
                        <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ $this->parent?->type_display }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Online Attendance Modal -->
    @if($showOnlineModal)
        <div class="fixed inset-0 z-[70] overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-10 px-0 sm:px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showOnlineModal', false)"></div>

                <div class="relative bg-white rounded-t-[2.5rem] sm:rounded-b-[2.5rem] shadow-2xl w-full max-w-lg p-8 z-10 max-h-[95vh] overflow-y-auto pb-24 sm:pb-8">
                    <!-- Handle Bar -->
                    <div class="w-16 h-1.5 bg-slate-200 rounded-full mx-auto mb-8"></div>

                    <h3 class="text-2xl font-black text-slate-900 mb-2">Hadir Online</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Upload foto catatan kajian sebagai bukti kehadiran online Anda hari ini.</p>

                    <form wire:submit="submitOnlineAttendance">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Foto Catatan Kajian <span class="text-primary-500">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:border-primary-500 hover:bg-primary-50/30 transition-all cursor-pointer relative group"
                                    x-data="{ compressing: false }" data-compress-container>
                                    <input type="file" accept="image/jpeg,image/png"
                                        wire:model="proofPhoto"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        x-on:change="
                                            const input = $event.target;
                                            if (input.files[0] && input.files[0].type.startsWith('image/')) {
                                                compressing = true;
                                                const compressed = await window.ImageCompressor.compress(input.files[0]);
                                                const dt = new DataTransfer();
                                                dt.items.add(compressed);
                                                input.files = dt.files;
                                                compressing = false;
                                            }
                                        ">

                                    @if($proofPhoto)
                                        <div class="relative">
                                            <img src="{{ $proofPhoto->temporaryUrl() }}" class="max-h-48 mx-auto rounded-2xl shadow-lg border-4 border-white">
                                            <div class="absolute inset-0 bg-black/40 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="text-white text-xs font-bold px-3 py-1 bg-white/20 backdrop-blur-md rounded-full">Ganti Foto</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-primary-500 group-hover:text-white transition-all">
                                                <span class="material-symbols-rounded text-3xl font-light">add_photo_alternate</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">Klik untuk upload foto</p>
                                                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider mt-1">JPG/PNG, Maks 2MB</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Compression loading indicator -->
                                    <div x-show="compressing" class="flex flex-col items-center gap-2 py-2">
                                        <span class="material-symbols-rounded text-primary-500 animate-spin text-3xl">progress_activity</span>
                                        <p class="text-xs font-bold text-primary-600">Mengompresi foto...</p>
                                    </div>
                                </div>
                                @error('proofPhoto') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Catatan (opsional)</label>
                                <textarea wire:model="notes" rows="3"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-medium"
                                    placeholder="Tulis pesan atau catatan tambahan untuk panitia..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-4 pb-4">
                            <button type="button" wire:click="$set('showOnlineModal', false)"
                                class="flex-1 px-6 py-4 border border-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-50 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-4 bg-primary-600 text-white rounded-2xl font-bold hover:bg-primary-700 flex items-center justify-center gap-3 shadow-xl shadow-primary-500/20 transition-all active:scale-95"
                                wire:loading.attr="disabled">
                                <span wire:loading wire:target="submitOnlineAttendance"
                                    class="material-symbols-rounded animate-spin text-xl">progress_activity</span>
                                <span wire:loading.remove wire:target="submitOnlineAttendance">Kirim Bukti</span>
                                <span wire:loading wire:target="submitOnlineAttendance">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Izin Modal -->
    @if($showIzinModal)
        <div class="fixed inset-0 z-[70] overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-10 px-0 sm:px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showIzinModal', false)"></div>

                <div class="relative bg-white rounded-t-[2.5rem] sm:rounded-b-[2.5rem] shadow-2xl w-full max-w-lg p-8 z-10 max-h-[95vh] overflow-y-auto pb-24 sm:pb-8">
                    <!-- Handle Bar -->
                    <div class="w-16 h-1.5 bg-slate-200 rounded-full mx-auto mb-8"></div>

                    <h3 class="text-2xl font-black text-slate-900 mb-2">Izin / Berhalangan</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Upload surat izin atau keterangan berhalangan hadir jika Anda tidak bisa mengikuti kajian.</p>

                    <form wire:submit="submitIzin">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Surat/Dokumen Izin <span class="text-amber-500">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:border-amber-500 hover:bg-amber-50/30 transition-all cursor-pointer relative group"
                                    x-data="{ compressing: false }" data-compress-container>
                                    <input type="file"
                                        accept="image/jpeg,image/png"
                                        wire:model="izinDocument"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        x-on:change="
                                            const input = $event.target;
                                            if (input.files[0] && input.files[0].type.startsWith('image/')) {
                                                compressing = true;
                                                const compressed = await window.ImageCompressor.compress(input.files[0]);
                                                const dt = new DataTransfer();
                                                dt.items.add(compressed);
                                                input.files = dt.files;
                                                compressing = false;
                                            }
                                        ">

                                    @if($izinDocument)
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="w-16 h-16 bg-amber-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                                                <span class="material-symbols-rounded text-3xl">task</span>
                                            </div>
                                            <p class="text-sm font-bold text-slate-700 mt-2">{{ $izinDocument->getClientOriginalName() }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Klik untuk ganti file</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                                <span class="material-symbols-rounded text-3xl font-light">upload_file</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">Klik untuk upload surat</p>
                                                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider mt-1">JPG/PNG, Maks 2MB</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Compression loading indicator -->
                                    <div x-show="compressing" class="flex flex-col items-center gap-2 py-2">
                                        <span class="material-symbols-rounded text-amber-500 animate-spin text-3xl">progress_activity</span>
                                        <p class="text-xs font-bold text-amber-600">Mengompresi foto...</p>
                                    </div>
                                </div>
                                @error('izinDocument') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Alasan <span class="text-amber-500">*</span></label>
                                <textarea wire:model="notes" rows="3"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-sm font-medium"
                                    placeholder="Jelaskan alasan berhalangan hadir secara singkat..."></textarea>
                                @error('notes') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex gap-4 pb-4">
                            <button type="button" wire:click="$set('showIzinModal', false)"
                                class="flex-1 px-6 py-4 border border-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-50 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-4 bg-amber-500 text-white rounded-2xl font-bold hover:bg-amber-600 flex items-center justify-center gap-3 shadow-xl shadow-amber-500/20 transition-all active:scale-95"
                                wire:loading.attr="disabled">
                                <span wire:loading wire:target="submitIzin"
                                    class="material-symbols-rounded animate-spin text-xl">progress_activity</span>
                                <span wire:loading.remove wire:target="submitIzin">Kirim Izin</span>
                                <span wire:loading wire:target="submitIzin">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Re-upload / Ganti Foto Modal -->
    @if($showReuploadModal)
        <div class="fixed inset-0 z-[70] overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-10 px-0 sm:px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showReuploadModal', false)"></div>

                <div class="relative bg-white rounded-t-[2.5rem] sm:rounded-b-[2.5rem] shadow-2xl w-full max-w-lg p-8 z-10 max-h-[95vh] overflow-y-auto pb-24 sm:pb-8">
                    <!-- Handle Bar -->
                    <div class="w-16 h-1.5 bg-slate-200 rounded-full mx-auto mb-8"></div>

                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 {{ $reuploadIsPendingReplace ? 'bg-yellow-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                            <span class="material-symbols-rounded {{ $reuploadIsPendingReplace ? 'text-yellow-600' : 'text-red-600' }}">{{ $reuploadIsPendingReplace ? 'swap_horiz' : 'upload_file' }}</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">{{ $reuploadIsPendingReplace ? 'Ganti Foto Bukti' : 'Upload Ulang Bukti' }}</h3>
                    </div>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                        {{ $reuploadIsPendingReplace
                            ? 'Foto bukti lama akan dihapus dan diganti dengan foto baru. Presensi Anda tetap tercatat menunggu validasi.'
                            : 'Bukti sebelumnya ditolak. Silakan upload file baru yang sesuai.' }}
                    </p>

                    <form wire:submit="reuploadProof">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">File Bukti Baru <span class="text-red-500">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:border-red-500 hover:bg-red-50/30 transition-all cursor-pointer relative group"
                                    x-data="{ compressing: false }" data-compress-container>
                                    <input type="file" accept="image/jpeg,image/png"
                                        wire:model="reuploadFile"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        x-on:change="
                                            const input = $event.target;
                                            if (input.files[0] && input.files[0].type.startsWith('image/')) {
                                                compressing = true;
                                                const compressed = await window.ImageCompressor.compress(input.files[0]);
                                                const dt = new DataTransfer();
                                                dt.items.add(compressed);
                                                input.files = dt.files;
                                                compressing = false;
                                            }
                                        ">

                                    @if($reuploadFile)
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="w-16 h-16 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/20">
                                                <span class="material-symbols-rounded text-3xl">task</span>
                                            </div>
                                            <p class="text-sm font-bold text-slate-700 mt-2">{{ $reuploadFile->getClientOriginalName() }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Klik untuk ganti file</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-red-500 group-hover:text-white transition-all">
                                                <span class="material-symbols-rounded text-3xl font-light">add_photo_alternate</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">Klik untuk upload file baru</p>
                                                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider mt-1">JPG/PNG, Maks 2MB</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Compression loading indicator -->
                                    <div x-show="compressing" class="flex flex-col items-center gap-2 py-2">
                                        <span class="material-symbols-rounded text-red-500 animate-spin text-3xl">progress_activity</span>
                                        <p class="text-xs font-bold text-red-600">Mengompresi foto...</p>
                                    </div>
                                </div>
                                @error('reuploadFile') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex gap-4 pb-4">
                            <button type="button" wire:click="$set('showReuploadModal', false)"
                                class="flex-1 px-6 py-4 border border-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-50 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-4 {{ $reuploadIsPendingReplace ? 'bg-yellow-500 hover:bg-yellow-600 shadow-yellow-400/20' : 'bg-red-600 hover:bg-red-700 shadow-red-500/20' }} text-white rounded-2xl font-bold flex items-center justify-center gap-3 shadow-xl transition-all active:scale-95"
                                wire:loading.attr="disabled">
                                <span wire:loading wire:target="reuploadProof"
                                    class="material-symbols-rounded animate-spin text-xl">progress_activity</span>
                                <span wire:loading.remove wire:target="reuploadProof">{{ $reuploadIsPendingReplace ? 'Ganti Foto' : 'Kirim Ulang' }}</span>
                                <span wire:loading wire:target="reuploadProof">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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

    <!-- Feedback / Survey Modal -->
    @if($showFeedbackModal)
        <div class="fixed inset-0 z-[70] overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-10 px-0 sm:px-4">
                <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-sm" wire:click="$set('showFeedbackModal', false)"></div>

                <div class="relative bg-white dark:bg-slate-900 rounded-t-[2.5rem] sm:rounded-b-[2.5rem] shadow-2xl w-full max-w-lg p-8 z-10 transition-colors max-h-[95vh] overflow-y-auto pb-24 sm:pb-8">
                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full mx-auto mb-8"></div>

                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-500 dark:text-indigo-400 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-indigo-100 dark:border-indigo-900/50">
                            <span class="material-symbols-rounded text-4xl">editor_choice</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white transition-colors">Penilaian Kajian</h3>
                        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1 transition-colors">Masukan Anda sangat berharga bagi kami.</p>
                    </div>

                    <form wire:submit="submitFeedback">
                        <div class="space-y-6">
                            <!-- Multi-Criteria Rating -->
                            <div class="space-y-6">
                                <!-- Materi -->
                                <div class="text-center bg-slate-50 dark:bg-slate-800/50 p-4 rounded-3xl">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Isi Materi</label>
                                    <div class="flex items-center justify-center gap-1" x-data="{ hover: 0 }">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" wire:click="$set('ratingMateri', {{ $i }})"
                                                @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                                                class="p-1 transition-all active:scale-90">
                                                <span class="material-symbols-rounded text-3xl transition-colors"
                                                    :class="{ 
                                                        'text-amber-400 fill-1': (hover || {{ $ratingMateri }}) >= {{ $i }},
                                                        'text-slate-200 dark:text-slate-700': (hover || {{ $ratingMateri }}) < {{ $i }}
                                                    }">grade</span>
                                            </button>
                                        @endfor
                                    </div>
                                    @error('ratingMateri') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Operasional -->
                                <div class="text-center bg-slate-50 dark:bg-slate-800/50 p-4 rounded-3xl">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kualitas Teknis / Fasilitas</label>
                                    <div class="flex items-center justify-center gap-1" x-data="{ hover: 0 }">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" wire:click="$set('ratingOperasional', {{ $i }})"
                                                @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                                                class="p-1 transition-all active:scale-90">
                                                <span class="material-symbols-rounded text-3xl transition-colors"
                                                    :class="{ 
                                                        'text-emerald-400 fill-1': (hover || {{ $ratingOperasional }}) >= {{ $i }},
                                                        'text-slate-200 dark:text-slate-700': (hover || {{ $ratingOperasional }}) < {{ $i }}
                                                    }">grade</span>
                                            </button>
                                        @endfor
                                    </div>
                                    @error('ratingOperasional') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Comment -->
                            <div>
                                <label class="block text-xs font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest mb-3 px-1">Komentar / Saran (opsional)</label>
                                <textarea wire:model="feedbackComment" rows="3"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-3xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-sm font-medium dark:text-white"
                                    placeholder="Apa yang perlu diperbaiki dari kajian ini?"></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-4 pb-4">
                            <button type="button" wire:click="$set('showFeedbackModal', false)"
                                class="flex-1 px-6 py-4 border border-slate-100 dark:border-slate-800 text-slate-500 dark:text-gray-400 rounded-2xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                                Nanti Saja
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 flex items-center justify-center gap-3 shadow-xl shadow-indigo-500/20 transition-all active:scale-95"
                                wire:loading.attr="disabled">
                                <span wire:loading wire:target="submitFeedback" class="material-symbols-rounded animate-spin text-xl">progress_activity</span>
                                <span wire:loading.remove wire:target="submitFeedback">Kirim Feedback</span>
                                <span wire:loading wire:target="submitFeedback">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>