/**
 * Browser image compression and upload helpers.
 *
 * Proof images are compressed on the user's device before they are sent to
 * Livewire or submitted by the public form. This keeps the VPS request small
 * and avoids sending the original camera photo before compression finishes.
 */

const IMAGE_COMPRESSION_FAILURE_MESSAGE =
    'Mohon maaf, ukuran file foto terlalu besar. Mohon jadikan foto tersebut "screenshoot" supaya ukuran jadi kecil, kemudian upload ulang screenshoot foto tersebut.';

const IMAGE_UPLOAD_FAILURE_MESSAGE =
    'Foto belum dapat diupload. Silakan periksa koneksi internet lalu coba lagi.';

window.ImageCompressor = {
    defaults: {
        maxBytes: 1_000_000,
        maxWidth: 1600,
        maxHeight: 1600,
        minDimension: 640,
        initialQuality: 0.82,
        minQuality: 0.42,
        qualityStep: 0.08,
        dimensionScale: 0.85,
        mimeType: 'image/jpeg',
    },

    /**
     * Compress a File and return only the resulting File.
     */
    async compress(file, options = {}) {
        const result = await this.compressWithStats(file, options);

        return result.file;
    },

    /**
     * Compress a File until it is at or below maxBytes.
     *
     * @returns {Promise<{file: File, originalSize: number, compressedSize: number, width: number, height: number, wasCompressed: boolean}>}
     */
    async compressWithStats(file, options = {}) {
        if (!file || !file.type || !file.type.startsWith('image/')) {
            throw this._compressionError();
        }

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            throw this._compressionError();
        }

        const settings = { ...this.defaults, ...options };
        const source = await this._loadImage(file);

        try {
            if (!source.width || !source.height) {
                throw this._compressionError();
            }

            const sourceFits = file.type === settings.mimeType
                && file.size <= settings.maxBytes
                && source.width <= settings.maxWidth
                && source.height <= settings.maxHeight;

            if (sourceFits) {
                return {
                    file,
                    originalSize: file.size,
                    compressedSize: file.size,
                    width: source.width,
                    height: source.height,
                    wasCompressed: false,
                };
            }

            let dimensions = this._calculateDimensions(
                source.width,
                source.height,
                settings.maxWidth,
                settings.maxHeight
            );
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d', { alpha: false });

            if (!ctx) {
                throw this._compressionError();
            }

            while (true) {
                canvas.width = dimensions.width;
                canvas.height = dimensions.height;

                // JPEG has no alpha channel. A white background keeps a PNG
                // with transparency readable instead of turning black.
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, dimensions.width, dimensions.height);
                ctx.drawImage(source.image, 0, 0, dimensions.width, dimensions.height);

                for (const quality of this._qualitySteps(settings)) {
                    const blob = await this._canvasToBlob(canvas, settings.mimeType, quality);

                    if (blob.size <= settings.maxBytes) {
                        const compressedFile = this._makeFile(file, blob, settings.mimeType);

                        return {
                            file: compressedFile,
                            originalSize: file.size,
                            compressedSize: compressedFile.size,
                            width: dimensions.width,
                            height: dimensions.height,
                            wasCompressed: true,
                        };
                    }
                }

                const longestSide = Math.max(dimensions.width, dimensions.height);

                if (longestSide <= settings.minDimension) {
                    break;
                }

                dimensions = this._scaleDimensions(
                    dimensions,
                    settings.dimensionScale,
                    settings.minDimension
                );
            }

            throw this._compressionError();
        } finally {
            source.cleanup();
        }
    },

    /**
     * Replace the selected file in an input without triggering another change
     * event. The caller decides whether to upload it or submit the form.
     */
    replaceInputFile(input, file) {
        if (typeof DataTransfer === 'undefined') {
            throw this._compressionError();
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
    },

    clearInput(input) {
        try {
            input.value = '';
        } catch (error) {
            // Some browsers do not allow clearing a file input in every state.
        }
    },

    formatSize(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;

        return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
    },

    showCompressionFailure() {
        window.alert(IMAGE_COMPRESSION_FAILURE_MESSAGE);
    },

    showUploadFailure() {
        window.alert(IMAGE_UPLOAD_FAILURE_MESSAGE);
    },

    _compressionError() {
        const error = new Error(IMAGE_COMPRESSION_FAILURE_MESSAGE);
        error.code = 'IMAGE_COMPRESSION_FAILED';

        return error;
    },

    _qualitySteps(settings) {
        const qualities = [];

        for (let quality = settings.initialQuality; quality > settings.minQuality; quality -= settings.qualityStep) {
            qualities.push(Number(quality.toFixed(2)));
        }

        qualities.push(settings.minQuality);

        return [...new Set(qualities)];
    },

    _makeFile(originalFile, blob, mimeType) {
        const originalName = originalFile.name || 'foto-catatan';
        const baseName = originalName.replace(/\.[^.]+$/, '');

        return new File(
            [blob],
            `${baseName}.jpg`,
            {
                type: mimeType,
                lastModified: Date.now(),
            }
        );
    },

    _loadImage(file) {
        // createImageBitmap can apply the camera's EXIF orientation before the
        // canvas is drawn. Older browsers use the Image fallback below.
        if (typeof createImageBitmap === 'function') {
            return createImageBitmap(file, { imageOrientation: 'from-image' })
                .then((bitmap) => ({
                    image: bitmap,
                    width: bitmap.width,
                    height: bitmap.height,
                    cleanup: () => bitmap.close?.(),
                }))
                .catch(() => this._loadImageWithElement(file));
        }

        return this._loadImageWithElement(file);
    },

    _loadImageWithElement(file) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            const objectUrl = URL.createObjectURL(file);

            image.onload = () => {
                URL.revokeObjectURL(objectUrl);
                resolve({
                    image,
                    width: image.naturalWidth,
                    height: image.naturalHeight,
                    cleanup: () => {},
                });
            };

            image.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(this._compressionError());
            };

            image.src = objectUrl;
        });
    },

    _calculateDimensions(originalWidth, originalHeight, maxWidth, maxHeight) {
        const scale = Math.min(
            1,
            maxWidth / originalWidth,
            maxHeight / originalHeight
        );

        return {
            width: Math.max(1, Math.round(originalWidth * scale)),
            height: Math.max(1, Math.round(originalHeight * scale)),
        };
    },

    _scaleDimensions(dimensions, scale, minDimension) {
        const longestSide = Math.max(dimensions.width, dimensions.height);
        const nextLongestSide = Math.max(minDimension, Math.floor(longestSide * scale));
        const ratio = nextLongestSide / longestSide;

        return {
            width: Math.max(1, Math.round(dimensions.width * ratio)),
            height: Math.max(1, Math.round(dimensions.height * ratio)),
        };
    },

    _canvasToBlob(canvas, mimeType, quality) {
        return new Promise((resolve, reject) => {
            canvas.toBlob((blob) => {
                if (blob) {
                    resolve(blob);
                } else {
                    reject(this._compressionError());
                }
            }, mimeType, quality);
        });
    },
};

/**
 * Alpine state shared by the Livewire upload forms and the public form.
 */
window.imageUploadForm = function imageUploadForm() {
    return {
        submitting: false,
        uploadState: 'idle',
        progress: 0,
        statusText: '',
        originalSize: '',
        compressedSize: '',
        errorMessage: '',

        init() {
            // Return the form to an interactive state if Livewire responds
            // with validation errors instead of closing the modal.
            if (!this.$wire?.$hook) {
                return;
            }

            this.$wire.$hook('commit', ({ succeed }) => {
                succeed(() => {
                    if (this.uploadState === 'saving') {
                        this.uploadState = 'ready';
                        this.statusText = 'Foto siap dikirim';
                    }
                });
            });
        },

        isInputBusy() {
            return ['compressing', 'uploading'].includes(this.uploadState);
        },

        isSubmitBusy() {
            return this.submitting || ['compressing', 'uploading', 'saving'].includes(this.uploadState);
        },

        hasUploadStatus() {
            return ['compressing', 'uploading', 'saving', 'error'].includes(this.uploadState);
        },

        async uploadLivewire($wire, property, event) {
            const input = event.target;
            const file = input.files?.[0];

            if (!file || this.isInputBusy()) {
                return;
            }

            // Prevent a previous temporary upload from being submitted while
            // the newly selected image is still being compressed/uploaded.
            $wire.$set(property, null, false);
            this._beginCompression(file);

            try {
                const result = await window.ImageCompressor.compressWithStats(file);

                window.ImageCompressor.replaceInputFile(input, result.file);
                this._setCompressionStats(result);
                this.uploadState = 'uploading';
                this.progress = 0;
                this.statusText = 'Mengupload...';
                this.errorMessage = '';

                $wire.upload(
                    property,
                    result.file,
                    () => {
                        this.progress = 100;
                        this.uploadState = 'ready';
                        this.statusText = 'Foto siap dikirim';
                    },
                    () => {
                        this._handleFailure(input, $wire, property, false);
                    },
                    (uploadEvent) => {
                        this.progress = this._progressFromEvent(uploadEvent);
                    },
                    () => {
                        this._handleFailure(input, $wire, property, false);
                    }
                );
            } catch (error) {
                this._handleFailure(input, $wire, property, true);
            }
        },

        async compressPlain(event) {
            const input = event.target;
            const file = input.files?.[0];

            if (!file || this.isInputBusy()) {
                return;
            }

            this._beginCompression(file);

            try {
                const result = await window.ImageCompressor.compressWithStats(file);

                window.ImageCompressor.replaceInputFile(input, result.file);
                this._setCompressionStats(result);
                this.progress = 100;
                this.uploadState = 'ready';
                this.statusText = 'Foto siap dikirim';
            } catch (error) {
                this._handleFailure(input, null, null, true);
            }
        },

        markSaving(event) {
            if (this.submitting || ['compressing', 'uploading', 'saving'].includes(this.uploadState)) {
                event.preventDefault();
                return;
            }

            this.uploadState = 'saving';
            this.progress = 100;
            this.statusText = 'Menyimpan data...';
        },

        submitPlain(event) {
            if (this.submitting || ['compressing', 'uploading', 'saving'].includes(this.uploadState)) {
                event.preventDefault();
                return;
            }

            const form = event.currentTarget;
            const input = form.querySelector('[data-image-upload-input]');

            // Let the browser show its native required-field message when no
            // file has been selected.
            if (!input?.files?.length) {
                return;
            }

            event.preventDefault();
            this.submitting = true;
            this.uploadState = 'uploading';
            this.progress = 0;
            this.statusText = 'Mengupload...';
            this.errorMessage = '';

            const request = new XMLHttpRequest();
            this.xhr = request;

            request.open('POST', form.action, true);
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            request.upload.addEventListener('progress', (progressEvent) => {
                if (!progressEvent.lengthComputable) {
                    return;
                }

                this.progress = Math.min(99, Math.round((progressEvent.loaded / progressEvent.total) * 100));
            });

            request.onload = () => {
                this.progress = 100;

                if (request.status >= 200 && request.status < 400) {
                    this.uploadState = 'saving';
                    this.statusText = 'Menyimpan data...';
                    window.location.assign(request.responseURL || form.action);

                    return;
                }

                this._handleFailure(input, null, null, false);
            };

            request.onerror = () => this._handleFailure(input, null, null, false);
            request.onabort = () => this._handleFailure(input, null, null, false);
            request.send(new FormData(form));
        },

        _beginCompression(file) {
            this.uploadState = 'compressing';
            this.progress = 0;
            this.statusText = 'Mengompresi foto...';
            this.originalSize = window.ImageCompressor.formatSize(file.size);
            this.compressedSize = '';
            this.errorMessage = '';
        },

        _setCompressionStats(result) {
            this.originalSize = window.ImageCompressor.formatSize(result.originalSize);
            this.compressedSize = window.ImageCompressor.formatSize(result.compressedSize);
        },

        _progressFromEvent(uploadEvent) {
            const value = uploadEvent?.detail?.progress ?? uploadEvent?.progress ?? 0;

            return Math.max(0, Math.min(99, Number(value) || 0));
        },

        _handleFailure(input, $wire, property, isCompressionFailure) {
            window.ImageCompressor.clearInput(input);

            if ($wire && property) {
                $wire.$set(property, null, false);
            }

            this.uploadState = 'error';
            this.progress = 0;
            this.statusText = 'Upload gagal';
            this.errorMessage = isCompressionFailure
                ? IMAGE_COMPRESSION_FAILURE_MESSAGE
                : IMAGE_UPLOAD_FAILURE_MESSAGE;

            if (isCompressionFailure) {
                window.ImageCompressor.showCompressionFailure();
            } else {
                window.ImageCompressor.showUploadFailure();
            }
        },
    };
};
