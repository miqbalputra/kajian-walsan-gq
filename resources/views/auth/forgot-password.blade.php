<x-layouts.app title="Lupa Password" surface="public" :force-light="true">
    <x-public.header :login="true" />

    <main id="main-content" class="public-container public-login-main" tabindex="-1">
        <div class="public-login-grid">
            <div class="public-login-intro">
                <p class="public-eyebrow">Pemulihan akun</p>
                <h2>Presensi Wali Santri</h2>
                <p class="public-lead">Masukkan username atau email akun yang terdaftar. Tautan untuk membuat kata sandi baru akan dikirim ke Gmail yang terdaftar.</p>
            </div>

            <section class="public-panel public-login-panel" aria-labelledby="forgot-password-title">
                <h1 id="forgot-password-title">Lupa kata sandi?</h1>
                <p class="public-form-intro">Masukkan username atau email Anda untuk menerima tautan reset.</p>

                @if (session('status'))
                    <p class="public-message" role="status">{{ session('status') }}</p>
                @endif

                <form class="public-login-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="public-field">
                        <label for="identifier">Isi Username atau Email Anda (pilih salah satu)</label>
                        <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}"
                            placeholder="Masukkan username atau email Anda" required autofocus autocomplete="username"
                            autocapitalize="none" autocorrect="off" spellcheck="false"
                            aria-invalid="{{ $errors->has('identifier') ? 'true' : 'false' }}"
                            aria-describedby="identifier-help{{ $errors->has('identifier') ? ' identifier-error' : '' }}">
                        <p id="identifier-help" class="public-form-intro">Link reset akan dikirim melalui Gmail yang sudah terdaftar.</p>
                        @error('identifier')
                            <p id="identifier-error" class="public-field-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="public-button public-button-primary">
                        <span>Kirim Instruksi Reset</span>
                        <x-public.icon name="arrow-right" />
                    </button>
                </form>

                @php
                    $adminPhone = trim((string) \App\Models\Setting::get('admin_whatsapp', config('kajian.admin_whatsapp')));
                    if (preg_match('/^0[0-9]{9,12}$/', $adminPhone)) {
                        $adminPhone = '62' . substr($adminPhone, 1);
                    }
                    $supportMessage = "Assalamu'alaikum Admin, saya kesulitan reset password akun Presensi Wali Santri.\n\nNama Bapak/Ibu:\nNama Ananda:\nKelas:\n\nMohon bantuannya. Terima kasih.";
                    $supportUrl = preg_match('/^62[0-9]{8,13}$/', $adminPhone) && $adminPhone !== '6281234567890'
                        ? 'https://wa.me/' . $adminPhone . '?text=' . urlencode($supportMessage)
                        : null;
                @endphp

                <aside class="public-password-support" aria-labelledby="password-support-title">
                    <h2 id="password-support-title"><x-public.icon name="message" /> Kesulitan reset password?</h2>
                    <p>Khusus Bapak/Ibu yang masih kesulitan, silakan hubungi Admin melalui WhatsApp. Mohon sertakan:</p>
                    <ul>
                        <li>Nama Bapak/Ibu</li>
                        <li>Nama Ananda</li>
                        <li>Kelas Ananda</li>
                    </ul>
                    @if ($supportUrl)
                        <a class="public-button public-password-support-button" href="{{ $supportUrl }}" target="_blank" rel="noopener noreferrer">
                            <x-public.icon name="message" />
                            Hubungi Admin via WhatsApp
                        </a>
                    @else
                        <p class="public-password-support-unavailable">Kontak WhatsApp Admin belum tersedia. Silakan hubungi petugas kajian atau wali kelas Ananda.</p>
                    @endif
                </aside>

                <p class="public-login-help">
                    <a class="public-link" href="{{ route('login') }}"><x-public.icon name="arrow-left" /> Kembali ke Login</a>
                </p>
            </section>
        </div>
    </main>

    <x-public.footer />
</x-layouts.app>
