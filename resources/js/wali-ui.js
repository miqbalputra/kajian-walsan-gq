// Presentation helpers for the wali pages; Livewire owns all saved data.
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('button, a');
    if (trigger?.closest('.wali-page') && !trigger.closest('dialog')) {
        window.waliLastTrigger = trigger;
    }
}, true);

window.waliDialog = function ({ onClose, dismissible = true }) {
    return {
        closing: false,
        opener: null,
        dialog: null,
        init() {
            this.dialog = this.$el;
            this.opener = window.waliLastTrigger || document.activeElement;
            this.$nextTick(() => {
                if (this.dialog.isConnected && !this.dialog.open) this.dialog.showModal();
            });
        },
        async requestClose() {
            if (!dismissible || this.closing || this.dialog.querySelector('[data-busy="true"]')) return;
            this.closing = true;
            try { await onClose(); } finally { this.closing = false; }
        },
        trapTab(event) {
            const controls = [...this.dialog.querySelectorAll('button, a[href], input, textarea, select, [tabindex]')]
                .filter((control) => !control.disabled && control.tabIndex >= 0 && control.getClientRects().length);
            const first = controls[0];
            const last = controls[controls.length - 1];
            if (!first) { event.preventDefault(); return; }
            if (event.shiftKey && (document.activeElement === first || !controls.includes(document.activeElement))) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && (document.activeElement === last || !controls.includes(document.activeElement))) {
                event.preventDefault();
                first.focus();
            }
        },
        destroy() {
            if (this.dialog.open) this.dialog.close();
            const opener = this.opener;
            queueMicrotask(() => {
                if (!document.querySelector('.wali-dialog[open]')) {
                    const target = opener?.isConnected ? opener : document.querySelector('[data-wali-return-focus]');
                    target?.focus({ preventScroll: true });
                }
            });
        },
    };
};

window.waliTheme = function () {
    return {
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
    };
};

window.waliInstall = function () {
    return {
        installed: false,
        busy: false,
        instructions: false,
        message: '',
        init() { this.update(); },
        update() {
            this.installed = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
        },
        async install() {
            if (this.busy) return;
            const prompt = window.pwaDeferredPrompt;
            if (!prompt) { this.instructions = true; return; }
            this.busy = true;
            try {
                await prompt.prompt();
                const choice = await prompt.userChoice;
                this.message = choice.outcome === 'accepted' ? 'Permintaan pemasangan diterima.' : 'Bapak/Ibu tetap dapat menggunakan aplikasi melalui browser.';
                window.pwaDeferredPrompt = null;
                this.update();
            } catch {
                this.instructions = true;
            } finally { this.busy = false; }
        },
    };
};

window.waliCardPreview = function () {
    return {
        scale: 1,
        observer: null,
        init() {
            this.observer = new ResizeObserver(([entry]) => { this.scale = Math.min(1, entry.contentRect.width / 323.4); });
            this.observer.observe(this.$el);
        },
        destroy() { this.observer?.disconnect(); },
    };
};
