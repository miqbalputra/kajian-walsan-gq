        <div wire:ignore
            x-data="{
                supported: 'Notification' in window && 'serviceWorker' in navigator && 'PushManager' in window,
                permission: 'Notification' in window ? Notification.permission : 'unsupported',
                loading: false,
                message: '',
                waitForPushScript() {
                    return new Promise((resolve) => {
                        const hasPushScript = () => typeof window.initPushNotification === 'function';

                        if (hasPushScript()) {
                            resolve(true);
                            return;
                        }

                        const scriptUrl = document.querySelector('meta[name=pwa-push-script-url]')?.content || '/js/pwa-push.js?v=20260519-push-v4';
                        let script = document.querySelector('script[data-pwa-push-script]');

                        if (!script) {
                            script = document.createElement('script');
                            script.src = scriptUrl;
                            script.defer = true;
                            script.dataset.pwaPushScript = 'true';
                            document.head.appendChild(script);
                        }

                        script.addEventListener('load', () => resolve(hasPushScript()), { once: true });
                        script.addEventListener('error', () => resolve(false), { once: true });

                        let attempts = 0;
                        const timer = setInterval(() => {
                            attempts++;
                            if (hasPushScript()) {
                                clearInterval(timer);
                                resolve(true);
                            }

                            if (attempts >= 50) {
                                clearInterval(timer);
                                resolve(hasPushScript());
                            }
                        }, 100);
                    });
                },
                async enablePush() {
                    this.loading = true;
                    this.message = '';

                    const ready = await this.waitForPushScript();
                    if (!ready) {
                        this.message = 'Script notifikasi gagal dimuat. Muat ulang browser lalu coba lagi.';
                        this.loading = false;
                        alert(this.message);
                        return;
                    }

                    const result = await window.initPushNotification(false);
                    this.permission = 'Notification' in window ? Notification.permission : 'unsupported';
                    this.message = result?.message || '';
                    this.loading = false;
                }
            }"
            x-show="supported && permission !== 'granted'"
            x-cloak
            class="wali-panel wali-prompt">

            <div class="wali-muted">
                <h2>Aktifkan Notifikasi</h2>
                <p class="wali-muted">Terima pengumuman kajian langsung di HP.</p>
                <p x-show="message" x-text="message" class="wali-muted"></p>
            </div>
            <button type="button"
                @click.stop.prevent="enablePush()"
                :disabled="loading"
                class="wali-button wali-button-secondary">
                <span x-show="!loading">Aktifkan</span>
                <span x-show="loading" x-cloak>Memproses...</span>
            </button>
        </div>
