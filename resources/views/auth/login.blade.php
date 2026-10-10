<x-layouts.app title="Masuk" surface="public" :force-light="true">
    <x-public.header :login="true" />
    <main id="main-content" class="public-container public-login-main" tabindex="-1">
        <div class="public-login-grid">
            <div class="public-login-intro">
                <p class="public-eyebrow">Kajian wali santri</p>
                <h2>Presensi Wali Santri</h2>
                <p class="public-lead">Masuk untuk melihat kartu presensi, mengirim catatan kajian, atau mengajukan izin.</p>
                <ul class="public-login-benefits">
                    <li><x-public.icon name="check" /> Lihat jadwal dan tempat kajian</li>
                    <li><x-public.icon name="check" /> Kelola kehadiran Bapak/Ibu</li>
                    <li><x-public.icon name="check" /> Periksa riwayat dan status presensi</li>
                </ul>
            </div>
            <div>
                <section class="public-panel public-login-panel" aria-labelledby="login-title"
                    x-data="{ submitting: false, googlePending: false }"
                    @pageshow.window="submitting = false; googlePending = false">
                    <h1 id="login-title">Masuk ke akun</h1>
                    <p class="public-form-intro">Pilih salah satu cara masuk di bawah.</p>

                    @if (session('status'))
                        <p class="public-message" role="status">{{ session('status') }}</p>
                    @endif

                    <div class="public-google-login">
                        @if ($errors->has('google'))
                            <p class="public-message public-message-error" role="alert">{{ $errors->first('google') }}</p>
                        @endif
                        <a class="public-button public-button-secondary" href="{{ route('google.redirect') }}"
                            :aria-disabled="submitting || googlePending"
                            @click="if (submitting || googlePending) { $event.preventDefault(); return; } googlePending = true">
                            <svg class="public-google-mark" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.31 9.14 5.38 12 5.38z"/>
                            </svg>
                            <span x-text="googlePending ? 'Membuka Google…' : 'Masuk dengan Google'">Masuk dengan Google</span>
                        </a>
                        <p>Email Google harus sudah terdaftar di aplikasi.</p>
                    </div>
                    <div class="public-divider">atau gunakan username</div>

                    <form class="public-login-form" method="POST" action="{{ route('login') }}"
                        @submit="if (submitting || googlePending) { $event.preventDefault(); return; } submitting = true"
                        :aria-busy="submitting">
                        @csrf
                        <div class="public-field">
                            <label for="username">Username atau email</label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                placeholder="Username / email" required autocomplete="username"
                                autocapitalize="none" autocorrect="off" spellcheck="false"
                                aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                                @if ($errors->has('username')) aria-describedby="username-error" @endif>
                            @error('username')
                                <p id="username-error" class="public-field-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="public-field" x-data="{ showPassword: false }">
                            <label for="password">Kata sandi</label>
                            <div class="public-password-field">
                                <input type="password" :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                    placeholder="Kata sandi" required autocomplete="current-password"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    @if ($errors->has('password')) aria-describedby="password-error" @endif>
                                <button type="button" class="public-password-toggle" x-cloak @click="showPassword = !showPassword"
                                    aria-label="Tampilkan kata sandi" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    :aria-pressed="showPassword" aria-controls="password">
                                    <span x-text="showPassword ? 'Tutup' : 'Lihat'">Lihat</span>
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="public-field-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="public-form-tools">
                            <a class="public-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        </div>
                        <button type="submit" class="public-button public-button-primary" :disabled="submitting || googlePending">
                            <span x-text="submitting ? 'Sedang masuk…' : 'Masuk'">Masuk</span>
                            <x-public.icon name="arrow-right" />
                        </button>
                    </form>
                </section>
                <p class="public-login-help">Kesulitan masuk? <a class="public-link" href="{{ route('home') }}#bantuan">Lihat bantuan</a></p>
            </div>
        </div>
    </main>
    <x-public.footer />
</x-layouts.app>
