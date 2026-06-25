<div class="flex flex-col min-h-[calc(100vh-64px)]">
    @if(!$activeEvent)
        <!-- No Active Event -->
        <div class="flex-1 flex items-center justify-center p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-yellow-600 text-5xl">event_busy</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Kegiatan Aktif</h2>
                <p class="text-gray-500 mb-6">Belum ada kajian yang sedang dibuka untuk presensi.</p>
                <a href="{{ route('panitia.jadwal') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                    <span class="material-symbols-rounded">calendar_month</span>
                    Lihat Jadwal Kegiatan
                </a>
            </div>
        </div>
    @else
        <!-- CSS Animasi Scanner -->
        <style>
            @keyframes scan-laser {
                0%, 100% { transform: translateY(0); opacity: 0; }
                10%, 90% { opacity: 1; }
                50% { transform: translateY(240px); opacity: 1; }
            }
            .animate-scan-laser {
                animation: scan-laser 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            
            /* Styling HTML5QRCode Builder agar rapi & fullscreen container */
            #qr-reader { border: none !important; }
            #qr-reader a { display: none !important; }
            #qr-reader span { border: none !important; }
            #qr-reader video {
                object-fit: cover !important;
                width: 100% !important;
                height: 100% !important;
                border-radius: 1rem;
            }
            /* Efek Mirror untuk Kamera Depan (Tanpa JS Hack) */
            .mirror-cam video {
                transform: scaleX(-1) !important;
            }

            /* Fullscreen Mode Fixes */
            .scanner-fullscreen-mode {
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                z-index: 9999 !important;
            }
            .scanner-fullscreen-mode #qr-reader {
                height: 100% !important;
            }
        </style>

        <!-- Active Event Info -->
        <div class="bg-gradient-to-r from-secondary-500 to-secondary-600 text-white px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs font-medium uppercase tracking-wide">{{ $activeEvent->category_display }} Aktif</p>
                    <p class="font-bold truncate">{{ $activeEvent->title }}</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold">{{ $this->totalAttendance }}</p>
                    <p class="text-white/80 text-xs">Hadir</p>
                </div>
            </div>
        </div>

        <!-- Scanner Area -->
        <div class="p-4" x-data="qrScannerComponent()" wire:ignore>
            <!-- Camera View Container -->
            <div id="scanner-container" :class="{'mirror-cam': facingMode === 'user', 'scanner-fullscreen-mode bg-black': isFullscreen}" 
                class="bg-black rounded-2xl overflow-hidden aspect-square max-w-md mx-auto relative mb-4 shadow-xl border-4 border-gray-900 transition-all duration-300">
                <div id="qr-reader" class="w-full h-full"></div>

                <!-- Petunjuk arahkan QR -->
                <div x-show="scanning" class="absolute top-4 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-md text-white text-xs px-4 py-2 rounded-full whitespace-nowrap z-30 font-medium tracking-wide shadow-lg border border-white/10 animate-pulse">
                    Arahkan Kartu QR Ke Layar
                </div>

                <!-- Scanning Overlay & Laser -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none" x-show="scanning">
                    <div class="w-[250px] h-[250px] relative">
                        <!-- Border frame sudut -->
                        <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-primary-500 rounded-tl-xl shadow-[0_0_10px_#10B981]"></div>
                        <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-primary-500 rounded-tr-xl shadow-[0_0_10px_#10B981]"></div>
                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-primary-500 rounded-bl-xl shadow-[0_0_10px_#10B981]"></div>
                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-primary-500 rounded-br-xl shadow-[0_0_10px_#10B981]"></div>
                        
                        <!-- Garis Laser -->
                        <div class="absolute top-0 left-0 w-full h-[2px] bg-primary-400 shadow-[0_0_15px_3px_#10B981] animate-scan-laser"></div>
                    </div>
                </div>

                <!-- Status Overlay (Kamera Mati) -->
                <div x-show="!scanning && !hasCamera" class="absolute inset-0 bg-gray-900 flex items-center justify-center">
                    <div class="text-center text-white p-6">
                        <span class="material-symbols-rounded text-5xl mb-3">videocam_off</span>
                        <p class="font-semibold">Kamera tidak tersedia</p>
                        <button @click="startScanner()" class="mt-4 px-4 py-2 bg-primary-500 rounded-xl text-sm font-semibold hover:bg-primary-600">
                            Coba Lagi
                        </button>
                    </div>
                </div>

                <!-- Indikator Mode Kamera -->
                <div x-show="scanning" class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-2">
                    <span class="material-symbols-rounded text-sm" x-text="facingMode === 'user' ? 'front_camera' : 'rear_camera'"></span>
                    <span x-text="facingMode === 'user' ? 'Kamera Depan' : 'Kamera Belakang'"></span>
                </div>

                <!-- Zoom Controller -->
                <div x-show="scanning && hasZoom" class="absolute bottom-16 left-1/2 -translate-x-1/2 w-2/3 bg-black/40 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10 z-40">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-rounded text-white text-sm">zoom_out</span>
                        <input type="range" x-model="zoomValue" @input="applyZoom()" :min="minZoom" :max="maxZoom" step="0.1" 
                            class="flex-1 h-1.5 bg-gray-600 rounded-lg appearance-none cursor-pointer accent-primary-500">
                        <span class="material-symbols-rounded text-white text-sm">zoom_in</span>
                    </div>
                </div>

                <!-- Camera Switcher -->
                <button @click="switchCamera()" 
                    class="absolute top-4 right-4 w-12 h-12 bg-black/40 backdrop-blur-md border border-white/20 text-white rounded-full flex items-center justify-center hover:bg-primary-500 transition-all z-40"
                    title="Ganti Kamera">
                    <span class="material-symbols-rounded text-2xl">flip_camera_ios</span>
                </button>

                <!-- Fullscreen Toggle -->
                <button @click="toggleFullscreen()" 
                    class="absolute top-4 left-4 w-12 h-12 bg-black/40 backdrop-blur-md border border-white/20 text-white rounded-full flex items-center justify-center hover:bg-primary-500 transition-all z-40"
                    :title="isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh'">
                    <span class="material-symbols-rounded text-2xl" x-text="isFullscreen ? 'fullscreen_exit' : 'fullscreen'"></span>
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 max-w-md mx-auto">
                <button @click="toggleScanner()"
                    :class="scanning ? 'bg-red-500 hover:bg-red-600 shadow-red-500/30' : 'bg-primary-500 hover:bg-primary-600 shadow-primary-500/30'"
                    class="flex-1 py-3 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg transition-all active:scale-95">
                    <span class="material-symbols-rounded" x-text="scanning ? 'stop' : 'play_arrow'"></span>
                    <span x-text="scanning ? 'Jeda Scanner' : 'Mulai Scan'"></span>
                </button>
                <button wire:click="$set('showManualModal', true)"
                    class="px-5 py-3 bg-white text-gray-700 border-2 border-gray-200 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-gray-50 transition-all active:scale-95">
                    <span class="material-symbols-rounded">edit</span>
                    Manual
                </button>
            </div>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="mx-4 mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3 shadow-sm">
                <span class="material-symbols-rounded text-green-500">check_circle</span>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
        @endif

        <!-- Live Feed Presensi Terakhir -->
        <div class="flex-1 bg-white rounded-t-3xl shadow-[0_-10px_30px_-15px_rgba(0,0,0,0.1)] p-5 mt-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500">history</span>
                    Presensi Terakhir
                </h3>
                <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-lg font-medium">{{ $activeEvent->date?->format('d/m/Y') }}</span>
            </div>

            <div class="space-y-3">
                @forelse($this->recentAttendances as $attendance)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-100 rounded-2xl hover:bg-gray-100 transition-colors">
                        <div
                            class="w-12 h-12 bg-{{ $attendance->parent?->type === 'father' ? 'blue' : 'pink' }}-100 rounded-xl flex items-center justify-center shadow-sm">
                            <span
                                class="material-symbols-rounded text-{{ $attendance->parent?->type === 'father' ? 'blue' : 'pink' }}-600">
                                {{ $attendance->parent?->type === 'father' ? 'man' : 'woman' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 truncate">{{ $attendance->parent?->user?->name }}</p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $attendance->parent?->type_display }} dari
                                <span class="font-medium">{{ $attendance->parent?->students?->first()?->name ?? '-' }}</span>
                            </p>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="bg-primary-50 text-primary-700 text-xs font-bold px-2 py-1 rounded-lg">{{ $attendance->created_at->format('H:i') }}</span>
                            <button onclick="confirmDelete({{ $attendance->id }}, @js($attendance->parent?->user?->name))"
                                class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg mt-1 transition-colors" title="Batal Hadir">
                                <span class="material-symbols-rounded text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-rounded text-4xl text-gray-300">pageview</span>
                        </div>
                        <p class="font-medium text-gray-500">Belum ada presensi</p>
                        <p class="text-sm">Scan QR Code untuk memulai.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Manual Input Modal -->
        @if($showManualModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen">
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('showManualModal', false)"></div>

                    <div
                        class="relative bg-white rounded-t-3xl shadow-2xl w-full max-w-lg p-6 z-10 max-h-[85vh] overflow-y-auto">
                        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-6"></div>

                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Input Presensi Manual</h3>
                                <p class="text-sm text-gray-500">Cari nama wali santri yang lupa bawa QR.</p>
                            </div>
                            <button wire:click="$set('showManualModal', false)" class="p-2 bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 rounded-full transition-colors">
                                <span class="material-symbols-rounded">close</span>
                            </button>
                        </div>

                        <!-- Search Input -->
                        <div class="relative mb-5">
                            <span
                                class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 font-medium transition-all"
                                placeholder="Ketik nama orang tua atau anak..." autofocus autocomplete="off">
                        </div>

                        <!-- Search Results -->
                        <div class="space-y-2 pb-4">
                            @forelse($searchResults as $parent)
                                <button wire:click="manualCheckIn({{ $parent->id }})"
                                    class="w-full flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:bg-primary-50 hover:border-primary-100 active:bg-primary-100 transition-all text-left shadow-sm hover:shadow">
                                    <div
                                        class="w-12 h-12 bg-{{ $parent->type === 'father' ? 'blue' : 'pink' }}-50 rounded-full flex items-center justify-center border border-{{ $parent->type === 'father' ? 'blue' : 'pink' }}-100">
                                        <span
                                            class="material-symbols-rounded text-{{ $parent->type === 'father' ? 'blue' : 'pink' }}-500 text-2xl">
                                            {{ $parent->type === 'father' ? 'man' : 'woman' }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 text-lg">{{ $parent->user?->name }}</p>
                                        <p class="text-sm text-gray-500 mb-1">
                                            {{ $parent->type_display }}
                                        </p>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($parent->students as $student)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-600">
                                                    {{ $student->name }}
                                                    @if($student->classRoom)
                                                        <span class="ml-1 opacity-70">({{ $student->classRoom->name }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded text-gray-300 text-3xl">check_circle</span>
                                </button>
                            @empty
                                @if(strlen($searchQuery) >= 2)
                                    <div class="text-center py-10 text-gray-500 bg-gray-50 rounded-2xl">
                                        <span class="material-symbols-rounded text-5xl text-gray-300 mb-2">person_off</span>
                                        <p class="font-medium text-gray-600">Tidak ditemukan</p>
                                        <p class="text-sm mt-1">Coba gunakan nama panggilan lain.</p>
                                    </div>
                                @else
                                    <div class="text-center py-10 text-gray-500">
                                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-500 rounded-full mb-3">
                                            <span class="material-symbols-rounded text-4xl mt-1">keyboard</span>
                                        </div>
                                        <p class="font-medium text-gray-900">Mulai Mengetik</p>
                                        <p class="text-sm mt-1">Ketik minimal 2 huruf untuk mencari database.</p>
                                    </div>
                                @endif
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @push('scripts')
        <script>
            window.confirmDelete = function (id, name) {
                Swal.fire({
                    title: 'Hapus Presensi?',
                    html: `Apakah Anda yakin ingin membatalkan kehadiran <strong>${name}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#e5e7eb',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: '<span style="color: black">Kembali</span>',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'rounded-xl shadow-lg',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.cancelAttendance(id);
                    }
                });
            }

            function qrScannerComponent() {
                return {
                    scanning: false,
                    hasCamera: true,
                    scanner: null,
                    isProcessing: false,
                    facingMode: localStorage.getItem('scanner_camera_pref') || 'environment',
                    isFullscreen: false,
                    zoomValue: 1,
                    minZoom: 1,
                    maxZoom: 5,
                    hasZoom: false,
                    track: null,
                    scanEndpoint: @js(route('panitia.scan.store')),
                    scanCooldownMs: 250,
                    successDisplayMs: 2200,
                    fullscreenListenerBound: false,

                    init() {
                        this.startScanner();

                        if (!this.fullscreenListenerBound) {
                            document.addEventListener('fullscreenchange', () => {
                                this.isFullscreen = !!document.fullscreenElement;
                            });
                            this.fullscreenListenerBound = true;
                        }

                        Livewire.on('scan-success', (data) => {
                            this.showSuccessAlert(data[0]);
                            window.Livewire?.dispatch('refreshScannerData');
                        });

                        Livewire.on('scan-error', (data) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Barcode Tidak Dikenali',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#fef2f2',
                                color: '#991b1b',
                                toast: true,
                                position: 'top'
                            }).then(() => {
                                this.releaseProcessingLock();
                            });
                        });

                        Livewire.on('scan-warning', (data) => {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sudah Tercatat Sebelumnya',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1800,
                                timerProgressBar: true,
                                background: '#fffbeb',
                                color: '#b45309'
                            }).then(() => {
                                this.releaseProcessingLock();
                            });
                        });
                    },

                    async startScanner() {
                        try {
                            if (this.scanner && this.scanning) {
                                try { 
                                    await this.scanner.stop(); 
                                    await new Promise(resolve => setTimeout(resolve, 300));
                                } catch (e) { console.error("Error stopping scanner", e); }
                            }
                            
                            if (!this.scanner) {
                                this.scanner = new Html5Qrcode("qr-reader");
                            }

                            const formats = window.Html5QrcodeSupportedFormats
                                ? [window.Html5QrcodeSupportedFormats.QR_CODE]
                                : undefined;

                            const config = { 
                                fps: 12, 
                                qrbox: (viewfinderWidth, viewfinderHeight) => {
                                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                                    const qrboxSize = Math.floor(minEdge * 0.72);
                                    return { width: qrboxSize, height: qrboxSize };
                                },
                                experimentalFeatures: {
                                    useBarCodeDetectorIfSupported: true
                                },
                                formatsToSupport: formats
                            };

                            try {
                                await this.scanner.start(
                                    { facingMode: { exact: this.facingMode } },
                                    config,
                                    (decodedText) => this.onScanSuccess(decodedText),
                                    () => {}
                                );
                            } catch (primaryError) {
                                const cameras = await window.Html5Qrcode.getCameras();
                                const fallbackCameraId = cameras[0]?.id;

                                if (!fallbackCameraId) {
                                    throw primaryError;
                                }

                                await this.scanner.start(
                                    fallbackCameraId,
                                    config,
                                    (decodedText) => this.onScanSuccess(decodedText),
                                    () => {}
                                );
                            }

                            this.scanning = true;
                            this.hasCamera = true;

                            // Cek kapabilitas Zoom setelah kamera aktif
                            setTimeout(() => this.checkZoomCapability(), 1000);
                        } catch (err) {
                            console.error("Camera error:", err);
                            this.hasCamera = false;
                            this.scanning = false;
                            
                            // Peringatan agar user tahu ada error dan tidak silent fallback!
                            Swal.fire({
                                icon: 'error',
                                title: 'Kamera Gagal Diakses',
                                text: 'Pastikan izin kamera browser sudah aktif dan tidak ada aplikasi lain yang sedang menggunakan kamera.',
                                confirmButtonColor: '#10B981'
                            });
                        }
                    },

                    checkZoomCapability() {
                        try {
                            const videoElement = document.querySelector('#qr-reader video');
                            if (!videoElement || !videoElement.srcObject) return;
                            
                            const track = videoElement.srcObject.getVideoTracks()[0];
                            this.track = track;
                            const capabilities = track.getCapabilities();
                            
                            if (capabilities.zoom) {
                                this.hasZoom = true;
                                this.minZoom = capabilities.zoom.min;
                                this.maxZoom = capabilities.zoom.max;
                                this.zoomValue = capabilities.zoom.min;
                            }
                        } catch (e) { console.warn("Zoom not supported", e); }
                    },

                    async applyZoom() {
                        if (!this.track) return;
                        try {
                            await this.track.applyConstraints({
                                advanced: [{ zoom: this.zoomValue }]
                            });
                        } catch (e) { console.error("Failed to apply zoom", e); }
                    },

                    toggleFullscreen() {
                        const container = document.getElementById('scanner-container');
                        
                        if (!document.fullscreenElement) {
                            if (container.requestFullscreen) {
                                container.requestFullscreen();
                            } else if (container.webkitRequestFullscreen) {
                                container.webkitRequestFullscreen();
                            } else if (container.msRequestFullscreen) {
                                container.msRequestFullscreen();
                            }
                            this.isFullscreen = true;
                        } else {
                            if (document.exitFullscreen) {
                                document.exitFullscreen();
                            } else if (document.webkitExitFullscreen) {
                                document.webkitExitFullscreen();
                            }
                            this.isFullscreen = false;
                        }

                    },

                    async stopScanner() {
                        if (this.scanner && this.scanning) {
                            try {
                                await this.scanner.stop();
                                this.scanning = false;
                            } catch (err) {
                                console.error("Stop error:", err);
                            }
                        }
                    },

                    toggleScanner() {
                        if (this.scanning) {
                            this.stopScanner();
                        } else {
                            this.startScanner();
                        }
                    },

                    async switchCamera() {
                        this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
                        // Simpan preferensi agar besok-besok tetap pakai kamera pilihan terakhir
                        localStorage.setItem('scanner_camera_pref', this.facingMode);
                        
                        if (this.scanning) {
                            await this.startScanner();
                        }
                    },

                    async onScanSuccess(qrCode) {
                        if (this.isProcessing) return;
                        this.isProcessing = true;
                        await this.processScanRequest(qrCode);
                    },

                    async processScanRequest(qrCode) {
                        try {
                            const response = await fetch(this.scanEndpoint, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({ qr_code: qrCode })
                            });

                            const result = await response.json();

                            if (result.status === 'success') {
                                this.showSuccessAlert(result.payload);
                                window.Livewire?.dispatch('refreshScannerData');
                                return;
                            }

                            if (result.status === 'warning') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sudah Tercatat Sebelumnya',
                                    text: result.message,
                                    showConfirmButton: false,
                                    timer: 1800,
                                    timerProgressBar: true,
                                    background: '#fffbeb',
                                    color: '#b45309'
                                }).then(() => {
                                    this.releaseProcessingLock();
                                });
                                return;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Barcode Tidak Dikenali',
                                text: result.message || 'QR Code tidak bisa diproses.',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                background: '#fef2f2',
                                color: '#991b1b',
                                toast: true,
                                position: 'top'
                            }).then(() => {
                                this.releaseProcessingLock();
                            });
                        } catch (error) {
                            console.error('Scan request failed', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Koneksi Terganggu',
                                text: 'Permintaan scan belum sampai ke server. Coba arahkan ulang QR setelah koneksi stabil.',
                                showConfirmButton: false,
                                timer: 2200,
                                timerProgressBar: true,
                                background: '#fef2f2',
                                color: '#991b1b'
                            }).then(() => {
                                this.releaseProcessingLock();
                            });
                        }
                    },

                    releaseProcessingLock() {
                        window.setTimeout(() => {
                            this.isProcessing = false;
                        }, this.scanCooldownMs);
                    },

                    showSuccessAlert(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'VALID',
                            html: `
                                <div class="text-center py-2">
                                    <div class="w-24 h-24 bg-green-100 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-inner border-4 border-white">
                                        <span style="font-size: 56px;">OK</span>
                                    </div>
                                    <p class="text-2xl font-black text-gray-900 mb-1">${data.parentType} ${data.parentName}</p>
                                    <div class="inline-block px-4 py-2 bg-white rounded-xl shadow-sm border border-green-100 mt-2">
                                        <p class="text-gray-500 font-medium text-sm">Orang tua / Wali dari:</p>
                                        <p class="text-green-700 font-bold text-lg">${data.childName}</p>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            timer: this.successDisplayMs,
                            timerProgressBar: true,
                            background: '#ecfdf5',
                            color: '#047857',
                            backdrop: `rgba(0,0,0,0.6)`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'rounded-3xl shadow-2xl border-b-8 border-green-500'
                            }
                        }).then(() => {
                            this.releaseProcessingLock();
                        });
                    }
                }
            }
        </script>
    @endpush
</div>
