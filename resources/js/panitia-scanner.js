import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeSupportedFormats = Html5QrcodeSupportedFormats;

window.qrScannerComponent = () => ({
    scanner: null,
    cameraActive: false,
    scanning: false,
    starting: false,
    hasCamera: true,
    cameraError: '',
    isProcessing: false,
    decodePaused: false,
    pauseReasons: { manual: false, hidden: false, feedback: false, request: false, user: false },
    facingMode: window.localStorage?.getItem('scanner_camera_pref') || 'environment',
    isFullscreen: false,
    zoomValue: 1,
    minZoom: 1,
    maxZoom: 5,
    hasZoom: false,
    track: null,
    feedback: null,
    feedbackTimer: null,
    zoomTimer: null,
    scanEndpoint: '',
    pendingCancel: null,
    successDisplayMs: 2000,

    init() {
        this.scanEndpoint = this.$el.dataset.scanEndpoint;
        this.onVisibilityChange = () => {
            this.setPauseReason('hidden', document.visibilityState === 'hidden', true);
        };
        this.onFullscreenChange = () => {
            this.isFullscreen = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
        };

        document.addEventListener('visibilitychange', this.onVisibilityChange);
        document.addEventListener('fullscreenchange', this.onFullscreenChange);
        document.addEventListener('webkitfullscreenchange', this.onFullscreenChange);

        if (window.Livewire) {
            this.livewireCleanup = [
                Livewire.on('scan-success', event => {
                    const data = this.eventPayload(event);
                    this.closeManualDialog();
                    this.showSuccess(data);
                    window.Livewire?.dispatch('refreshScannerData');
                }),
                Livewire.on('scan-warning', event => {
                    const data = this.eventPayload(event);
                    this.closeManualDialog();
                    this.showResult('warning', 'Sudah tercatat', data.message || 'Kehadiran untuk kajian ini sudah tercatat.');
                }),
                Livewire.on('scan-error', event => {
                    const data = this.eventPayload(event);
                    this.closeManualDialog();
                    this.showResult('error', 'QR belum dapat diproses', data.message || 'Periksa QR lalu coba lagi.');
                }),
            ];
        }

        this.startScanner();
    },

    destroy() {
        document.removeEventListener('visibilitychange', this.onVisibilityChange);
        document.removeEventListener('fullscreenchange', this.onFullscreenChange);
        document.removeEventListener('webkitfullscreenchange', this.onFullscreenChange);
        this.livewireCleanup?.forEach(cleanup => cleanup?.());
        clearTimeout(this.feedbackTimer);
        clearTimeout(this.zoomTimer);
        if (this.cameraActive && this.scanner) {
            this.scanner.stop().catch(() => {});
        }
    },

    eventPayload(event) {
        if (event?.detail && typeof event.detail === 'object') return event.detail;
        if (Array.isArray(event)) return event[0] || {};
        if (event?.[0] && typeof event[0] === 'object') return event[0];
        return event || {};
    },

    async startScanner() {
        if (this.starting || this.cameraActive) return;
        this.starting = true;
        this.hasCamera = true;
        this.cameraError = '';
        this.feedback = null;

        try {
            if (!this.scanner) {
                this.scanner = new Html5Qrcode('qr-reader', {
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    verbose: false,
                });
            }

            const config = {
                fps: 8,
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    const size = Math.floor(Math.min(viewfinderWidth, viewfinderHeight) * 0.72);
                    return { width: size, height: size };
                },
                experimentalFeatures: { useBarCodeDetectorIfSupported: true },
            };
            const onDecoded = decodedText => this.onScanSuccess(decodedText);
            const onDecodeError = () => {};

            try {
                await this.scanner.start({ facingMode: { exact: this.facingMode } }, config, onDecoded, onDecodeError);
            } catch (primaryError) {
                const cameras = await Html5Qrcode.getCameras();
                const fallbackCameraId = cameras[0]?.id;
                if (!fallbackCameraId) throw primaryError;
                await this.scanner.start(fallbackCameraId, config, onDecoded, onDecodeError);
            }

            this.cameraActive = true;
            this.scanning = true;
            this.decodePaused = false;
            this.hasCamera = true;
            this.pauseReasons.hidden = document.visibilityState === 'hidden';
            this.syncDecodeState(document.visibilityState === 'hidden');
            window.setTimeout(() => this.checkZoomCapability(), 700);
        } catch (error) {
            console.error('Camera error:', error);
            this.cameraActive = false;
            this.scanning = false;
            this.hasCamera = false;
            this.cameraError = this.cameraErrorMessage(error);
        } finally {
            this.starting = false;
        }
    },

    cameraErrorMessage(error) {
        if (error?.name === 'NotAllowedError' || /permission|denied/i.test(error?.message || '')) {
            return 'Izinkan kamera di pengaturan browser, lalu pilih “Coba lagi”.';
        }
        if (error?.name === 'NotFoundError' || /camera.*not found/i.test(error?.message || '')) {
            return 'Kamera tidak ditemukan pada perangkat ini.';
        }
        return 'Periksa izin kamera atau tutup aplikasi lain yang sedang memakai kamera, lalu coba lagi.';
    },

    setPauseReason(reason, paused, freezeVideo = false) {
        if (this.pauseReasons[reason] === paused) return;
        this.pauseReasons[reason] = paused;
        this.syncDecodeState(freezeVideo);
    },

    async syncDecodeState(freezeVideo = false) {
        if (!this.cameraActive || !this.scanner) return;
        const shouldPause = Object.values(this.pauseReasons).some(Boolean);

        try {
            if (shouldPause && !this.decodePaused) {
                this.scanner.pause(freezeVideo);
                this.decodePaused = true;
                this.scanning = false;
            } else if (!shouldPause && this.decodePaused) {
                this.scanner.resume();
                this.decodePaused = false;
                this.scanning = true;
            }
        } catch (error) {
            console.warn('Could not update QR decode state:', error);
        }
    },

    async stopScanner() {
        if (!this.cameraActive || !this.scanner) return;
        try {
            await this.scanner.stop();
        } catch (error) {
            console.warn('Could not stop camera:', error);
        }
        this.cameraActive = false;
        this.scanning = false;
        this.decodePaused = false;
        this.track = null;
        this.hasZoom = false;
    },

    async toggleScanner() {
        if (!this.cameraActive) {
            await this.startScanner();
            return;
        }
        this.setPauseReason('user', !this.pauseReasons.user);
    },

    async switchCamera() {
        if (!this.cameraActive) return;
        this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
        try { window.localStorage?.setItem('scanner_camera_pref', this.facingMode); } catch (_) {}
        await this.stopScanner();
        await this.startScanner();
    },

    checkZoomCapability() {
        try {
            const video = this.$el.querySelector('#qr-reader video');
            const track = video?.srcObject?.getVideoTracks?.()[0];
            const zoom = track?.getCapabilities?.().zoom;
            if (!zoom) return;
            this.track = track;
            this.hasZoom = true;
            this.minZoom = zoom.min;
            this.maxZoom = zoom.max;
            this.zoomValue = zoom.min;
        } catch (error) {
            console.info('Camera zoom is not supported on this device.');
        }
    },

    applyZoom() {
        if (!this.track) return;
        clearTimeout(this.zoomTimer);
        this.zoomTimer = window.setTimeout(async () => {
            try {
                await this.track.applyConstraints({ advanced: [{ zoom: Number(this.zoomValue) }] });
            } catch (error) {
                console.warn('Could not apply camera zoom:', error);
            }
        }, 80);
    },

    async toggleFullscreen() {
        const container = this.$el.querySelector('#scanner-container');
        try {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                if (container?.requestFullscreen) await container.requestFullscreen();
                else if (container?.webkitRequestFullscreen) container.webkitRequestFullscreen();
            } else if (document.exitFullscreen) {
                await document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        } catch (error) {
            console.info('Fullscreen mode is unavailable:', error);
        }
        this.isFullscreen = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
    },

    async onScanSuccess(qrCode) {
        if (this.isProcessing || Object.values(this.pauseReasons).some(Boolean)) return;
        this.isProcessing = true;
        this.setPauseReason('request', true);
        this.feedback = { type: 'loading', title: 'Memeriksa QR…', message: 'Mohon tunggu sebentar.' };

        try {
            const response = await fetch(this.scanEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ qr_code: qrCode }),
            });
            const result = await response.json();

            if (result.status === 'success') {
                this.showSuccess(result.payload || {});
                window.Livewire?.dispatch('refreshScannerData');
            } else if (result.status === 'warning') {
                this.showResult('warning', 'Sudah tercatat', result.message || 'Kehadiran untuk kajian ini sudah tercatat.');
            } else {
                const title = response.status === 429 ? 'Terlalu banyak percobaan' : 'QR belum dapat diproses';
                this.showResult('error', title, result.message || 'QR tidak dikenali. Silakan coba kembali.');
            }
        } catch (error) {
            console.error('Scan request failed:', error);
            this.showResult('error', 'Koneksi terganggu', 'Hasil scan belum diterima. Periksa jaringan, lalu coba kembali.');
        }
    },

    showSuccess(data) {
        const parent = [data.parentType, data.parentName].filter(Boolean).join(' ');
        this.showResult('success', 'Presensi berhasil', parent || data.parentName || 'Kehadiran berhasil dicatat.', {
            childName: data.childName || '',
        }, this.successDisplayMs);
    },

    showResult(type, title, message, extra = {}, duration = 2000) {
        clearTimeout(this.feedbackTimer);
        this.feedback = { type, title, message, ...extra };
        this.setPauseReason('feedback', true);
        this.setPauseReason('request', false);
        this.feedbackTimer = window.setTimeout(() => {
            this.feedback = null;
            this.isProcessing = false;
            this.setPauseReason('feedback', false);
        }, duration);
    },

    openManualDialog() {
        this.setPauseReason('manual', true);
        const dialog = this.$refs.manualDialog;
        if (dialog && !dialog.open) dialog.showModal();
        this.$nextTick(() => this.$refs.manualSearch?.focus());
    },

    closeManualDialog() {
        if (this.$refs.manualDialog?.open) this.$refs.manualDialog.close();
        this.setPauseReason('manual', false);
        this.$nextTick(() => this.$el.querySelector('#manual-entry-trigger')?.focus());
    },

    askCancel(id, name) {
        this.pendingCancel = { id, name };
        this.$refs.cancelDialog?.showModal();
    },

    confirmCancelAttendance(wire) {
        if (!this.pendingCancel) return;
        const attendanceId = this.pendingCancel.id;
        this.$refs.cancelDialog?.close();
        this.pendingCancel = null;
        wire.cancelAttendance(attendanceId);
    },
});
