/**
 * Image Compressor Utility
 * Compress gambar di browser sebelum upload ke server.
 * Menggunakan Canvas API — tidak perlu library external.
 *
 * Usage dalam Alpine.js:
 *   await compressImage(fileInput, { maxWidth: 1200, quality: 0.7 })
 */

window.ImageCompressor = {
    /**
     * Default settings
     */
    defaults: {
        maxWidth: 1200,     // Max lebar (px)
        maxHeight: 1200,    // Max tinggi (px)
        quality: 0.7,       // Kualitas JPEG (0.0 - 1.0)
        mimeType: 'image/jpeg', // Output format
    },

    /**
     * Compress a File object and return a new compressed File.
     *
     * @param {File} file - File dari input[type=file]
     * @param {Object} options - Override default settings
     * @returns {Promise<File>} - Compressed File object
     */
    async compress(file, options = {}) {
        // Skip jika bukan image
        if (!file.type.startsWith('image/')) {
            console.log('[ImageCompressor] Bukan gambar, skip compression:', file.type);
            return file;
        }

        const settings = { ...this.defaults, ...options };
        const originalSize = file.size;

        try {
            // 1. Load image
            const img = await this._loadImage(file);

            // 2. Calculate new dimensions
            const { width, height } = this._calculateDimensions(
                img.naturalWidth,
                img.naturalHeight,
                settings.maxWidth,
                settings.maxHeight
            );

            // 3. Draw to canvas
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            // 4. Convert to blob
            const blob = await this._canvasToBlob(canvas, settings.mimeType, settings.quality);

            // 5. Buat File object baru
            const compressedFile = new File(
                [blob],
                file.name.replace(/\.[^.]+$/, '.jpg'), // Ganti extension ke .jpg
                { type: settings.mimeType, lastModified: Date.now() }
            );

            const compressedSize = compressedFile.size;
            const ratio = ((1 - compressedSize / originalSize) * 100).toFixed(0);

            console.log(
                `[ImageCompressor] ${this._formatSize(originalSize)} → ${this._formatSize(compressedSize)} (hemat ${ratio}%) | ${img.naturalWidth}x${img.naturalHeight} → ${width}x${height}`
            );

            // Jangan gunakan hasil compressed jika malah lebih besar
            if (compressedSize >= originalSize) {
                console.log('[ImageCompressor] Hasil compressed lebih besar, gunakan file asli.');
                return file;
            }

            return compressedFile;

        } catch (error) {
            console.error('[ImageCompressor] Error:', error);
            return file; // Fallback ke file asli jika gagal
        }
    },

    /**
     * Compress dan set ke Livewire file input.
     * Panggil dari event listener pada input[type=file].
     *
     * @param {HTMLInputElement} input - The file input element
     * @param {Object} options - Compression options
     */
    async compressAndSet(input, options = {}) {
        const files = input.files;
        if (!files || files.length === 0) return;

        const file = files[0];

        // Skip PDF
        if (file.type === 'application/pdf') {
            console.log('[ImageCompressor] PDF file, skip compression.');
            return;
        }

        // Show loading indicator
        const container = input.closest('[data-compress-container]') || input.parentElement;
        const loadingEl = container?.querySelector('[data-compress-loading]');
        if (loadingEl) loadingEl.style.display = 'flex';

        try {
            const compressed = await this.compress(file, options);

            // Create a new DataTransfer to set compressed file
            const dt = new DataTransfer();
            dt.items.add(compressed);
            input.files = dt.files;

            // Trigger events so Livewire picks up the change
            input.dispatchEvent(new Event('change', { bubbles: true }));

        } finally {
            if (loadingEl) loadingEl.style.display = 'none';
        }
    },

    // --- Private helpers ---

    _loadImage(file) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                URL.revokeObjectURL(img.src);
                resolve(img);
            };
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
        });
    },

    _calculateDimensions(origWidth, origHeight, maxWidth, maxHeight) {
        let width = origWidth;
        let height = origHeight;

        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }
        if (height > maxHeight) {
            width = Math.round((width * maxHeight) / height);
            height = maxHeight;
        }

        return { width, height };
    },

    _canvasToBlob(canvas, mimeType, quality) {
        return new Promise((resolve, reject) => {
            canvas.toBlob(
                (blob) => {
                    if (blob) resolve(blob);
                    else reject(new Error('Canvas toBlob failed'));
                },
                mimeType,
                quality
            );
        });
    },

    _formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    },
};
