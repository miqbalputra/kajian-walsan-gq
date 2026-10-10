<x-layouts.app title="Beranda" surface="public" :force-light="true">
    <x-public.header />
    <main id="main-content" class="public-container" tabindex="-1">
        <section class="public-hero" aria-labelledby="welcome-title">
            <div class="public-intro">
                <p class="public-eyebrow">Selamat datang, Bapak/Ibu</p>
                <h1 id="welcome-title">Presensi <span>Wali Santri</span></h1>
                <p class="public-lead">Lihat jadwal kajian dan kelola kehadiran Bapak/Ibu.</p>
                <div class="public-entry">
                    @auth
                        <a class="public-button public-button-primary" href="{{ route('dashboard') }}">Buka akun saya <x-public.icon name="arrow-right" /></a>
                    @else
                        <a class="public-button public-button-primary" href="{{ route('login') }}">Masuk sebagai wali santri <x-public.icon name="arrow-right" /></a>
                    @endauth
                    <p>Gunakan akun untuk melihat kartu presensi, hadir online, atau mengajukan izin.</p>
                </div>
            </div>
            <section class="public-panel public-schedule" aria-labelledby="schedule-title">
                @if ($currentKajian)
                    @php
                        $attendanceOpen = $currentKajian->status === 'open' && $currentKajian->date->toDateString() <= today()->toDateString();
                        $scheduleStatus = match (true) {
                            $currentKajian->status === 'closed' => 'Kajian selesai',
                            $currentKajian->date->isToday() => 'Hari ini',
                            $currentKajian->date->isTomorrow() => 'Besok',
                            $currentKajian->date->lt(today()) => 'Kajian sebelumnya',
                            default => 'Kajian berikutnya',
                        };
                    @endphp
                    <div class="public-schedule-heading">
                        <p class="public-eyebrow">Jadwal kajian</p>
                        <span class="public-status">{{ $scheduleStatus }}</span>
                    </div>
                    <h2 id="schedule-title">{{ $currentKajian->title }}</h2>
                    <div class="public-event-overview">
                        <div class="public-event-date" aria-hidden="true">
                            <span>{{ $currentKajian->date->translatedFormat('M') }}</span>
                            <strong>{{ $currentKajian->date->format('d') }}</strong>
                        </div>
                        <div class="public-event-overview-content">
                            <p class="public-event-date-line"><time datetime="{{ $currentKajian->date->toDateString() }}">{{ $currentKajian->formatted_date }}</time></p>
                            <p class="public-event-time"><x-public.icon name="clock" /> {{ $currentKajian->time_range }}</p>
                        </div>
                    </div>
                    @if ($currentKajian->location || $currentKajian->speaker)
                        <dl class="public-event-details">
                            @if ($currentKajian->location)
                                <div><dt><x-public.icon name="pin" /> Tempat</dt><dd>{{ $currentKajian->location }}</dd></div>
                            @endif
                            @if ($currentKajian->speaker)
                                <div><dt><x-public.icon name="user" /> Pemateri</dt><dd>{{ $currentKajian->speaker }}</dd></div>
                            @endif
                        </dl>
                    @endif
                    @if ($timeStatus === 'soon' && $eventTimestamp && $currentKajian->status !== 'closed')
                        <p class="public-schedule-note" x-data="publicKajianCountdown({{ $eventTimestamp }})"><span x-text="label">Dimulai dalam {{ (int) ceil($countdownMinutes) }} menit</span></p>
                    @elseif ($attendanceOpen && $currentKajian->attendance_count > 0)
                        <p class="public-schedule-note">{{ $currentKajian->attendance_count }} wali santri tercatat hadir.</p>
                    @endif
                    <div class="public-attendance-state">
                        <p class="public-eyebrow">Status presensi</p>
                        @if ($attendanceOpen)
                            <p class="public-attendance-label public-attendance-open"><x-public.icon name="check" /> Presensi dibuka</p>
                            <p>Pencatatan kehadiran sudah dapat dilakukan.</p>
                        @elseif ($currentKajian->status === 'closed')
                            <p class="public-attendance-label">Presensi ditutup</p>
                            <p>Riwayat kehadiran dapat dilihat di akun.</p>
                        @else
                            <p class="public-attendance-label">Presensi belum dibuka</p>
                            <p>Ikuti pengumuman petugas untuk mulai presensi.</p>
                        @endif
                    </div>
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
                <div><p class="public-eyebrow">Panduan singkat</p><h2 id="guide-title">Cara presensi</h2></div>
                <p>Pilih petunjuk sesuai kehadiran Bapak/Ibu.</p>
            </div>
            <div class="public-guide-grid">
                <article class="public-guide-item public-guide-direct">
                    <div class="public-guide-heading"><span class="public-guide-symbol"><x-public.icon name="pin" /></span><h3>Hadir langsung</h3></div>
                    <ol class="public-guide-steps">
                        <li>Siapkan kartu presensi atau QR dari akun.</li>
                        <li>Tunjukkan kepada petugas kajian.</li>
                    </ol>
                    <details class="public-inline-detail">
                        <summary>Belum membawa kartu? <x-public.icon name="chevron-down" /></summary>
                        <p>Sebutkan nama Bapak/Ibu dan nama ananda kepada petugas agar kehadiran dapat dicatat.</p>
                    </details>
                </article>
                <article class="public-guide-item public-guide-online">
                    <div class="public-guide-heading"><span class="public-guide-symbol"><x-public.icon name="notebook" /></span><h3>Hadir online</h3></div>
                    <ol class="public-guide-steps">
                        <li>Masuk ke akun, pilih <strong>Hadir Online</strong>.</li>
                        <li>Unggah foto catatan kajian tulisan tangan.</li>
                    </ol>
                    <details class="public-inline-detail">
                        <summary>Ketentuan catatan <x-public.icon name="chevron-down" /></summary>
                        <p>Gunakan foto catatan tulisan tangan Bapak/Ibu sendiri. Bukti akan diperiksa petugas. Pantau statusnya di akun dan unggah ulang jika diminta.</p>
                    </details>
                </article>
                <article class="public-guide-item public-guide-permission">
                    <div class="public-guide-heading"><span class="public-guide-symbol"><x-public.icon name="document" /></span><h3>Izin tidak hadir</h3></div>
                    <ol class="public-guide-steps">
                        <li>Masuk ke akun, pilih <strong>Izin</strong>.</li>
                        <li>Unggah dokumen keterangan.</li>
                    </ol>
                    <details class="public-inline-detail">
                        <summary>Setelah mengirim izin <x-public.icon name="chevron-down" /></summary>
                        <p>Dokumen akan diperiksa petugas. Bapak/Ibu dapat melihat status pengajuan dan mengunggah ulang dokumen dari akun.</p>
                    </details>
                </article>
            </div>
        </section>

        <section id="bantuan" class="public-section" aria-labelledby="help-title">
            <div class="public-section-heading"><div><h2 id="help-title">Perlu bantuan?</h2></div><p>Jawaban untuk pertanyaan yang sering muncul.</p></div>
            <div class="public-help-grid">
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
                    <p class="public-support-label"><x-public.icon name="message" /> Bantuan akun</p>
                    <h3 id="support-title">Minta bantuan petugas</h3>
                    <p>Kesulitan masuk atau ada data yang belum sesuai? Petugas dapat membantu Bapak/Ibu.</p>
                    @if ($supportUrl)
                        <a class="public-button public-button-secondary" href="{{ $supportUrl }}" target="_blank" rel="noopener noreferrer">Hubungi admin <x-public.icon name="arrow-right" /></a>
                        <p class="public-support-note">Bantuan melalui WhatsApp.</p>
                    @else
                        <p class="public-support-note">Silakan hubungi petugas kajian atau wali kelas ananda.</p>
                    @endif
                </aside>
                <div class="public-faq">
                    <details>
                        <summary><span>Belum tahu username atau kata sandi?</span> <x-public.icon name="chevron-down" /></summary>
                        <div class="public-faq-content"><p>Hubungi petugas kajian atau wali kelas ananda untuk mengetahui akun Bapak/Ibu yang terdaftar.</p></div>
                    </details>
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
        @guest
            <div class="public-staff-entry"><span>Akses khusus petugas</span><a class="public-link" href="{{ route('login') }}">Masuk sebagai petugas <x-public.icon name="arrow-right" /></a></div>
        @endguest
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
