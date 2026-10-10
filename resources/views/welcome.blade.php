<x-layouts.app title="Beranda" surface="public" :force-light="true">
    <x-public.header />
    <main id="main-content" class="public-container" tabindex="-1">
        <section class="public-hero" aria-labelledby="welcome-title">
            <div class="public-intro">
                <p class="public-eyebrow">Selamat datang, Bapak/Ibu</p>
                <h1 id="welcome-title">Presensi<br class="public-desktop-break"> Wali Santri</h1>
                <p class="public-lead">Lihat jadwal kajian dan kelola kehadiran Bapak/Ibu.</p>
                <div class="public-entry">
                    @auth
                        <a class="public-button public-button-primary" href="{{ route('dashboard') }}">Buka akun saya <x-public.icon name="arrow-right" /></a>
                    @else
                        <a class="public-button public-button-primary" href="{{ route('login') }}">Masuk sebagai wali santri <x-public.icon name="arrow-right" /></a>
                    @endauth
                    <p>Untuk kartu presensi, hadir online, dan pengajuan izin.</p>
                </div>
                @guest
                    <p class="public-staff-entry">Anda petugas? <a class="public-link" href="{{ route('login') }}">Masuk sebagai petugas</a></p>
                @endguest
            </div>
            <section class="public-panel public-schedule" aria-labelledby="schedule-title">
                @if ($currentKajian)
                    @php
                        $attendanceOpen = $currentKajian->status === 'open' && $currentKajian->date->toDateString() <= today()->toDateString();
                        $scheduleStatus = match (true) {
                            $currentKajian->status === 'closed' => 'Kajian selesai',
                            $attendanceOpen => 'Presensi dibuka',
                            $currentKajian->date->isToday() => 'Hari ini',
                            $currentKajian->date->isTomorrow() => 'Besok',
                            default => 'Kajian berikutnya',
                        };
                    @endphp
                    <div class="public-schedule-heading">
                        <p class="public-eyebrow">Jadwal kajian</p>
                        <span class="public-status {{ $attendanceOpen ? 'public-status-open' : '' }}">{{ $scheduleStatus }}</span>
                    </div>
                    <h2 id="schedule-title">{{ $currentKajian->title }}</h2>
                    <dl class="public-event-details">
                        <div><dt><x-public.icon name="calendar" /> Tanggal</dt><dd>{{ $currentKajian->formatted_date }}</dd></div>
                        <div><dt><x-public.icon name="clock" /> Waktu</dt><dd>{{ $currentKajian->time_range }}</dd></div>
                        @if ($currentKajian->location)
                            <div class="public-event-wide"><dt><x-public.icon name="pin" /> Tempat</dt><dd>{{ $currentKajian->location }}</dd></div>
                        @endif
                        @if ($currentKajian->speaker)
                            <div class="public-event-wide"><dt><x-public.icon name="user" /> Pemateri</dt><dd>{{ $currentKajian->speaker }}</dd></div>
                        @endif
                    </dl>
                    @if ($timeStatus === 'soon' && $eventTimestamp && $currentKajian->status !== 'closed')
                        <p class="public-schedule-note" x-data="publicKajianCountdown({{ $eventTimestamp }})"><span x-text="label">Dimulai dalam {{ (int) ceil($countdownMinutes) }} menit</span></p>
                    @elseif ($attendanceOpen && $currentKajian->attendance_count > 0)
                        <p class="public-schedule-note">{{ $currentKajian->attendance_count }} wali santri tercatat hadir.</p>
                    @endif
                @else
                    <p class="public-eyebrow">Jadwal kajian</p>
                    <div class="public-empty-schedule">
                        <x-public.icon name="calendar" />
                        <h2 id="schedule-title">Jadwal akan diumumkan</h2>
                        <p>Jadwal kajian berikutnya belum tersedia.</p>
                        <p class="public-muted">Bapak/Ibu tetap dapat masuk untuk melihat akun.</p>
                    </div>
                @endif
            </section>
        </section>

        <section id="cara-presensi" class="public-section" aria-labelledby="guide-title">
            <div class="public-section-heading">
                <p class="public-eyebrow">Panduan singkat</p>
                <h2 id="guide-title">Cara presensi</h2>
                <p>Pilih cara yang sesuai dengan kehadiran Bapak/Ibu.</p>
            </div>
            <div class="public-guide-grid">
                <article class="public-guide-item">
                    <h3>Hadir langsung</h3>
                    <p>Tunjukkan kartu presensi atau QR kepada petugas di lokasi kajian.</p>
                    <details class="public-inline-detail">
                        <summary>Belum membawa kartu? <x-public.icon name="chevron-down" /></summary>
                        <p>Sebutkan nama Bapak/Ibu dan nama ananda kepada petugas agar kehadiran dapat dicatat.</p>
                    </details>
                </article>
                <article class="public-guide-item">
                    <h3>Hadir online</h3>
                    <p>Masuk ke akun, pilih <strong>Hadir Online</strong>, lalu unggah foto catatan kajian.</p>
                    <details class="public-inline-detail">
                        <summary>Ketentuan catatan <x-public.icon name="chevron-down" /></summary>
                        <p>Gunakan foto catatan tulisan tangan Bapak/Ibu sendiri. Bukti akan diperiksa petugas. Pantau statusnya di akun dan unggah ulang jika diminta.</p>
                    </details>
                </article>
                <article class="public-guide-item">
                    <h3>Berhalangan hadir</h3>
                    <p>Masuk ke akun, pilih <strong>Izin</strong>, lalu unggah dokumen keterangan.</p>
                    <details class="public-inline-detail">
                        <summary>Setelah mengirim izin <x-public.icon name="chevron-down" /></summary>
                        <p>Dokumen akan diperiksa petugas. Bapak/Ibu dapat melihat status pengajuan dan mengunggah ulang dokumen dari akun.</p>
                    </details>
                </article>
            </div>
        </section>

        <section id="bantuan" class="public-section" aria-labelledby="help-title">
            <div class="public-section-heading"><h2 id="help-title">Perlu bantuan?</h2><p>Jawaban untuk pertanyaan yang sering muncul.</p></div>
            <div class="public-help-grid">
                <div class="public-faq">
                    <details>
                        <summary><span>Bagaimana cara masuk dengan Google?</span> <x-public.icon name="chevron-down" /></summary>
                        <div class="public-faq-content">
                            <p>Pada halaman masuk, pilih <strong>Masuk dengan Google</strong>. Gunakan akun Google dengan email yang sudah terdaftar di aplikasi.</p>
                            <p>Jika email belum terdaftar, masuk dengan username dan kata sandi atau minta bantuan petugas untuk memperbarui email akun.</p>
                        </div>
                    </details>
                    <details>
                        <summary><span>Bagaimana jika lupa kata sandi?</span> <x-public.icon name="chevron-down" /></summary>
                        <div class="public-faq-content"><p>Pilih <a class="public-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>, lalu isi username atau email yang terdaftar. Ikuti petunjuk pemulihan akun yang diberikan.</p></div>
                    </details>
                    <details>
                        <summary><span>Bagaimana cara memasang aplikasi di HP?</span> <x-public.icon name="chevron-down" /></summary>
                        <div class="public-faq-content">
                            <p><strong>Android atau Chrome:</strong> buka menu browser titik tiga, lalu pilih <strong>Install app</strong> atau <strong>Add to Home screen</strong>. Bapak/Ibu juga dapat menggunakan tombol pemasangan setelah masuk.</p>
                            <p><strong>iPhone:</strong> buka melalui Safari, tekan tombol Bagikan (Share), lalu pilih <strong>Add to Home Screen</strong>.</p>
                            <p>Aplikasi tetap dapat digunakan melalui browser tanpa dipasang.</p>
                        </div>
                    </details>
                    <details>
                        <summary><span>Bagaimana cara menerima pengingat kajian?</span> <x-public.icon name="chevron-down" /></summary>
                        <div class="public-faq-content"><p>Setelah masuk ke akun wali santri, aktifkan notifikasi dan izinkan saat browser meminta persetujuan. Pengingat kajian dan pengumuman dapat diterima di perangkat Bapak/Ibu.</p></div>
                    </details>
                    <details>
                        <summary><span>Di mana melihat status kehadiran?</span> <x-public.icon name="chevron-down" /></summary>
                        <div class="public-faq-content">
                            <p>Masuk ke akun untuk melihat riwayat kehadiran serta status pemeriksaan catatan kajian atau izin. Jika bukti ditolak atau perlu diperbaiki, unggah ulang dari akun.</p>
                            <p>Setelah kajian selesai, Bapak/Ibu juga dapat memberikan penilaian dan masukan.</p>
                        </div>
                    </details>
                </div>
                @php
                    $adminPhone = trim((string) \App\Models\Setting::get('admin_whatsapp', config('kajian.admin_whatsapp')));
                    if (preg_match('/^0[0-9]{9,12}$/', $adminPhone)) {
                        $adminPhone = '62' . substr($adminPhone, 1);
                    }
                    $supportUrl = preg_match('/^62[0-9]{8,13}$/', $adminPhone) && $adminPhone !== '6281234567890'
                        ? 'https://wa.me/' . $adminPhone
                        : null;
                @endphp
                <aside class="public-support" aria-labelledby="support-title">
                    <h3 id="support-title">Minta bantuan petugas</h3>
                    <p>Kesulitan masuk atau ada data yang belum sesuai? Petugas dapat membantu Bapak/Ibu.</p>
                    @if ($supportUrl)
                        <a class="public-button public-button-secondary" href="{{ $supportUrl }}" target="_blank" rel="noopener noreferrer">Hubungi admin <x-public.icon name="arrow-right" /></a>
                        <p class="public-support-note">Bantuan melalui WhatsApp.</p>
                    @else
                        <p class="public-support-note">Silakan hubungi petugas kajian atau wali kelas ananda.</p>
                    @endif
                </aside>
            </div>
        </section>

        @if ($lastKajian && $lastKajian->id !== $currentKajian?->id)
            <section class="public-last-event" aria-label="Kajian terakhir">
                <div><p class="public-eyebrow">Kajian terakhir</p><p class="public-last-title">{{ $lastKajian->title }}</p><p class="public-muted">{{ $lastKajian->formatted_date }}</p></div>
                @if ($lastKajian->attendance_count > 0)
                    <p class="public-last-count"><strong>{{ $lastKajian->attendance_count }}</strong> wali santri tercatat hadir</p>
                @endif
            </section>
        @endif
    </main>
    <x-public.footer />
    @if ($timeStatus === 'soon' && $eventTimestamp)
        @push('scripts')
            <script>
                function publicKajianCountdown(timestamp) {
                    return {
                        label: '',
                        interval: null,
                        init() {
                            this.update();
                            if (Number(timestamp) > Date.now()) {
                                this.interval = setInterval(() => this.update(), 30000);
                            }
                        },
                        update() {
                            const minutes = Math.ceil((Number(timestamp) - Date.now()) / 60000);
                            this.label = minutes > 0 ? 'Dimulai dalam ' + minutes + ' menit' : 'Waktu mulai kajian telah tiba';
                            if (minutes <= 0 && this.interval) clearInterval(this.interval);
                        },
                        destroy() {
                            if (this.interval) clearInterval(this.interval);
                        }
                    };
                }
            </script>
        @endpush
    @endif
</x-layouts.app>
