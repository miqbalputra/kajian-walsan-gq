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
    nativeFullscreen: false,
    fullscreenChanging: false,
    cameraRestarting: false,
    destroyed: false,
    layoutTimer: null,
    zoomValue: 1,
    minZoom: 1,
    maxZoom: 3,
    zoomStep: 0.1,
    hasZoom: false,
    usesDigitalZoom: true,
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
            const wasNative = this.nativeFullscreen;
            this.nativeFullscreen = this.fullscreenElement() === this.$refs.scannerContainer;
            if (this.nativeFullscreen || wasNative) this.setFullscreen(this.nativeFullscreen);
        };
        this.onKeydown = event => {
            if (event.key === 'Escape' && this.isFullscreen) this.exitFullscreen();
        };

        document.addEventListener('visibilitychange', this.onVisibilityChange);
        document.addEventListener('fullscreenchange', this.onFullscreenChange);
        document.addEventListener('webkitfullscreenchange', this.onFullscreenChange);
        document.addEventListener('keydown', this.onKeydown);

        if (!this.$refs.scannerContainer) return;

        this.onVideoMetadata = event => {
            if (event.target.tagName === 'VIDEO') this.sizeCameraVideo(event.target);
        };
        this.$refs.qrReader.addEventListener('loadedmetadata', this.onVideoMetadata, true);

        if (window.ResizeObserver) {
            let previousSize = '';
            this.layoutObserver = new ResizeObserver(([entry]) => {
                const size = `${Math.round(entry.contentRect.width)}x${Math.round(entry.contentRect.height)}`;
                if (previousSize && size !== previousSize) this.refreshScannerLayout();
                previousSize = size;
            });
            this.layoutObserver.observe(this.$refs.cameraFrame);
        }

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
        this.destroyed = true;
        document.removeEventListener('visibilitychange', this.onVisibilityChange);
        document.removeEventListener('fullscreenchange', this.onFullscreenChange);
        document.removeEventListener('webkitfullscreenchange', this.onFullscreenChange);
        document.removeEventListener('keydown', this.onKeydown);
        this.layoutObserver?.disconnect();
        this.$refs.qrReader?.removeEventListener('loadedmetadata', this.onVideoMetadata, true);
        clearTimeout(this.layoutTimer);
        this.exitFullscreen();
        this.livewireCleanup?.forEach(cleanup => cleanup?.());
        clearTimeout(this.feedbackTimer);
        clearTimeout(this.zoomTimer);
        if (this.cameraActive && this.scanner && !this.cameraRestarting) {
            this.scanner.stop().catch(() => {});
        }
    },

    eventPayload(event) {
        if (event?.detail && typeof event.detail === 'object') return event.detail;
        if (Array.isArray(event)) return event[0] || {};
        if (event?.[0] && typeof event[0] === 'object') return event[0];
        return event || {};
    },

    async startScanner(resetFeedback = true) {
        if (this.destroyed || this.starting || this.cameraActive) return;
        this.starting = true;
        this.hasCamera = true;
        this.cameraError = '';
        if (resetFeedback) this.feedback = null;

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
                    const frame = this.$refs.cameraFrame;
                    const size = Math.floor(Math.min(viewfinderWidth, viewfinderHeight, frame.clientWidth, frame.clientHeight) * 0.72);
                    this.$refs.cameraFrame.style.setProperty('--qr-guide-size', `${size}px`);
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

            if (this.destroyed) {
                await this.scanner.stop();
                return;
            }
            this.cameraActive = true;
            this.scanning = true;
            this.decodePaused = false;
            this.hasCamera = true;
            this.hasZoom = true;
            this.usesDigitalZoom = true;
            this.minZoom = 1;
            this.maxZoom = 3;
            this.zoomStep = 0.1;
            this.zoomValue = 1;
            this.pauseReasons.hidden = document.visibilityState === 'hidden';
            this.syncDecodeState(document.visibilityState === 'hidden');
            this.checkZoomCapability();
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
        this.usesDigitalZoom = true;
        this.hasZoom = false;
    },

    async toggleScanner() {
        if (this.cameraRestarting) return;
        if (!this.cameraActive) {
            await this.startScanner();
            return;
        }
        this.setPauseReason('user', !this.pauseReasons.user);
    },

    async switchCamera() {
        if (!this.cameraActive || this.starting || this.cameraRestarting) return;
        this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
        try { window.localStorage?.setItem('scanner_camera_pref', this.facingMode); } catch (_) {}
        await this.stopScanner();
        await this.startScanner();
    },

    checkZoomCapability() {
        try {
            const zoom = this.scanner?.getRunningTrackCapabilities?.().zoom;
            const min = Number(zoom?.min);
            const max = Number(zoom?.max);
            if (Number.isFinite(min) && Number.isFinite(max) && max > min) {
                this.usesDigitalZoom = false;
                this.minZoom = min;
                this.maxZoom = max;
                this.zoomStep = Number(zoom.step) > 0 ? Number(zoom.step) : 0.1;
                const current = Number(this.scanner.getRunningTrackSettings?.().zoom ?? min);
                this.zoomValue = Math.min(max, Math.max(min, Number.isFinite(current) ? current : min));
            } else {
                this.usesDigitalZoom = true;
                this.minZoom = 1;
                this.maxZoom = 3;
                this.zoomStep = 0.1;
                this.zoomValue = 1;
            }
            this.hasZoom = true;
        } catch (error) {
            // Keep an on-screen digital zoom control available when the camera
            // does not expose native zoom capabilities.
            this.usesDigitalZoom = true;
            this.minZoom = 1;
            this.maxZoom = 3;
            this.zoomStep = 0.1;
            this.zoomValue = 1;
            this.hasZoom = true;
        }
    },

    adjustZoom(direction) {
        const next = Number(this.zoomValue) + (direction * Number(this.zoomStep));
        this.zoomValue = Math.min(Number(this.maxZoom), Math.max(Number(this.minZoom), Number(next.toFixed(2))));
        this.applyZoom();
    },

    applyZoom() {
        if (!this.cameraActive || !this.scanner || this.usesDigitalZoom) return;
        clearTimeout(this.zoomTimer);
        this.zoomTimer = window.setTimeout(async () => {
            try {
                await this.scanner.applyVideoConstraints({ advanced: [{ zoom: Number(this.zoomValue) }] });
            } catch (error) {
                console.warn('Could not apply camera zoom:', error);
                this.usesDigitalZoom = true;
            }
        }, 80);
    },

    fullscreenElement() {
        return document.fullscreenElement || document.webkitFullscreenElement;
    },

    sizeCameraVideo(video) {
        if (!video.videoWidth || !video.videoHeight) return;
        const frame = this.$refs.cameraFrame;
        const scale = Math.max(frame.clientWidth / video.videoWidth, frame.clientHeight / video.videoHeight);
        // Preserve the video's aspect ratio so the decoder and visible QR agree.
        frame.style.setProperty('--camera-video-width', `${video.videoWidth * scale}px`);
        frame.style.setProperty('--camera-video-height', `${video.videoHeight * scale}px`);
    },

    setFullscreen(enabled) {
        enabled = enabled && !this.destroyed;
        this.isFullscreen = enabled;
        document.documentElement.classList.toggle('panitia-scanner-fullscreen', enabled);
        document.body.classList.toggle('panitia-scanner-fullscreen', enabled);
    },

    async toggleFullscreen() {
        if (this.fullscreenChanging) return;
        if (this.isFullscreen) return this.exitFullscreen();
        const container = this.$refs.scannerContainer;
        if (!container) return;

        // Fill the viewport even when native fullscreen is unsupported or denied.
        this.setFullscreen(true);
        this.fullscreenChanging = true;
        try {
            if (container.requestFullscreen) await container.requestFullscreen();
            else if (container.webkitRequestFullscreen) await container.webkitRequestFullscreen();
            this.nativeFullscreen = this.fullscreenElement() === container;
            if (!this.isFullscreen || this.destroyed) await this.exitFullscreen();
        } catch (error) {
            console.info('Using viewport fullscreen:', error);
        } finally {
            this.fullscreenChanging = false;
        }
    },

    async exitFullscreen() {
        const container = this.$refs.scannerContainer;
        this.setFullscreen(false);
        this.nativeFullscreen = false;
        if (!container || this.fullscreenElement() !== container) return;

        try {
            if (document.exitFullscreen) await document.exitFullscreen();
            else if (document.webkitExitFullscreen) await document.webkitExitFullscreen();
        } catch (error) {
            // Keep the exit control available if the browser remains fullscreen.
            this.nativeFullscreen = this.fullscreenElement() === container;
            this.setFullscreen(this.nativeFullscreen);
        }
    },

    refreshScannerLayout() {
        clearTimeout(this.layoutTimer);
        this.layoutTimer = window.setTimeout(async () => {
            if (this.destroyed) return;
            if (this.cameraRestarting) return this.refreshScannerLayout();
            if (!this.cameraActive || this.starting) return;
            const zoom = Number(this.zoomValue);
            // html5-qrcode calculates its decode region only when starting.
            this.cameraRestarting = true;
            try {
                await this.stopScanner();
                await this.startScanner(false);
                this.zoomValue = Math.min(this.maxZoom, Math.max(this.minZoom, zoom));
                this.applyZoom();
            } finally {
                this.cameraRestarting = false;
            }
        }, 200);
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

    async openManualDialog() {
        this.setPauseReason('manual', true);
        await this.exitFullscreen();
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
