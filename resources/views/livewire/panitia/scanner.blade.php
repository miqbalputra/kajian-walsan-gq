<div class="panitia-scanner" x-data="qrScannerComponent()" data-scan-endpoint="{{ route('panitia.scan.store') }}">
    <main class="panitia-main">
        @if(!$activeEvent)
            <section class="panitia-empty panel" aria-labelledby="empty-event-title">
                <span class="material-symbols-rounded panitia-empty__icon" aria-hidden="true">event_busy</span>
                <p class="panitia-eyebrow">Pemindai presensi</p>
                <h2 id="empty-event-title">Belum ada kajian aktif</h2>
                <p>Presensi bisa dilakukan setelah petugas membuka jadwal kajian.</p>
                <a href="{{ route('panitia.jadwal') }}" class="panitia-button panitia-button--primary">
                    <span class="material-symbols-rounded" aria-hidden="true">calendar_month</span>
                    Lihat jadwal kajian
                </a>
            </section>
        @else
            <div class="panitia-scanner__layout">
                <div class="panitia-scanner__work">
                    <section class="panitia-event panel" aria-labelledby="active-event-title">
                        <div class="panitia-event__heading">
                            <div class="panitia-event__copy">
                                <p class="panitia-eyebrow">{{ $activeEvent->category_display }} · Presensi aktif</p>
                                <h2 id="active-event-title">{{ $activeEvent->title }}</h2>
                            </div>
                            <div class="panitia-count" aria-label="Jumlah kehadiran yang tercatat">
                                <strong>{{ $this->totalAttendance }}</strong>
                                <span>Hadir</span>
                            </div>
                        </div>
                        <dl class="panitia-event__details">
                            <div>
                                <dt>Tanggal</dt>
                                <dd>{{ $activeEvent->date?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}</dd>
                            </div>
                            <div>
                                <dt>Waktu</dt>
                                <dd>{{ $activeEvent->time_range ?? 'Belum ditentukan' }}</dd>
                            </div>
                            <div>
                                <dt>Tempat</dt>
                                <dd>{{ $activeEvent->location ?: 'Belum diumumkan' }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="panitia-camera panel" aria-labelledby="camera-title">
                        <div class="panitia-section-heading">
                            <div>
                                <p class="panitia-eyebrow">Arahkan kamera ke kartu wali</p>
                                <h2 id="camera-title">Scan QR presensi</h2>
                            </div>
                            <span class="panitia-camera__state" :class="cameraActive && scanning ? 'is-active' : ''" aria-live="polite">
                                <span class="panitia-camera__state-dot" aria-hidden="true"></span>
                                <span x-text="starting ? 'Menyiapkan kamera' : (cameraActive ? (scanning ? 'Siap scan' : 'Scanner dijeda') : 'Kamera belum aktif')"></span>
                            </span>
                        </div>

                        <div id="scanner-container" class="panitia-camera__frame"
                            :class="{ 'mirror-cam': facingMode === 'user', 'scanner-fullscreen-mode': isFullscreen, 'has-camera-error': !hasCamera, 'uses-digital-zoom': usesDigitalZoom }"
                            :style="{ '--camera-zoom': usesDigitalZoom ? zoomValue : 1 }">
                            <div id="qr-reader" wire:ignore aria-label="Tampilan kamera untuk memindai QR"></div>

                            <div class="panitia-camera__guide" x-show="scanning" aria-hidden="true">
                                <span class="panitia-camera__corner panitia-camera__corner--tl"></span>
                                <span class="panitia-camera__corner panitia-camera__corner--tr"></span>
                                <span class="panitia-camera__corner panitia-camera__corner--bl"></span>
                                <span class="panitia-camera__corner panitia-camera__corner--br"></span>
                            </div>

                            <p class="panitia-camera__hint" x-show="scanning">Posisikan QR di dalam bingkai</p>

                            <div class="panitia-camera__error" x-show="!hasCamera" x-cloak>
                                <span class="material-symbols-rounded" aria-hidden="true">videocam_off</span>
                                <strong>Kamera belum tersedia</strong>
                                <span x-text="cameraError"></span>
                                <button type="button" class="panitia-button panitia-button--light" @click="startScanner()" :disabled="starting">
                                    Coba lagi
                                </button>
                            </div>

                            <button type="button" class="panitia-camera__tool panitia-camera__tool--left" @click="toggleFullscreen()"
                                :aria-label="isFullscreen ? 'Keluar dari layar penuh' : 'Buka layar penuh'"
                                :title="isFullscreen ? 'Keluar dari layar penuh' : 'Layar penuh'">
                                <span class="material-symbols-rounded" x-text="isFullscreen ? 'fullscreen_exit' : 'fullscreen'" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="panitia-camera__tool panitia-camera__tool--right" @click="switchCamera()"
                                aria-label="Ganti kamera" title="Ganti kamera" :disabled="!cameraActive">
                                <span class="material-symbols-rounded" aria-hidden="true">flip_camera_ios</span>
                            </button>

                            <div class="panitia-camera__zoom" x-show="cameraActive && hasZoom" x-cloak>
                                <button type="button" class="panitia-camera__zoom-button" @click="adjustZoom(-1)"
                                    :disabled="Number(zoomValue) <= Number(minZoom)" aria-label="Perkecil kamera" title="Perkecil">
                                    <span class="material-symbols-rounded" aria-hidden="true">zoom_out</span>
                                </button>
                                <label class="visually-hidden" for="camera-zoom">Perbesar kamera</label>
                                <input id="camera-zoom" type="range" x-model="zoomValue" @input="applyZoom()"
                                    :min="minZoom" :max="maxZoom" :step="zoomStep" aria-label="Perbesar kamera">
                                <span class="panitia-camera__zoom-value" x-text="Number(zoomValue).toFixed(1) + '×'" aria-hidden="true"></span>
                                <button type="button" class="panitia-camera__zoom-button" @click="adjustZoom(1)"
                                    :disabled="Number(zoomValue) >= Number(maxZoom)" aria-label="Perbesar kamera" title="Perbesar">
                                    <span class="material-symbols-rounded" aria-hidden="true">zoom_in</span>
                                </button>
                            </div>

                            <span class="panitia-camera__mode" x-show="cameraActive" x-cloak>
                                <span class="material-symbols-rounded" aria-hidden="true" x-text="facingMode === 'user' ? 'front_camera' : 'rear_camera'"></span>
                                <span x-text="facingMode === 'user' ? 'Kamera depan' : 'Kamera belakang'"></span>
                            </span>
                        </div>

                        <div class="panitia-feedback" x-show="feedback" x-cloak
                            :class="feedback ? 'is-' + feedback.type : ''" role="status" aria-live="assertive">
                            <span class="material-symbols-rounded panitia-feedback__icon" aria-hidden="true"
                                x-text="feedback?.type === 'success' ? 'check_circle' : (feedback?.type === 'warning' ? 'info' : (feedback?.type === 'loading' ? 'qr_code_scanner' : 'error'))"></span>
                            <div class="panitia-feedback__copy">
                                <strong x-text="feedback?.title"></strong>
                                <span x-text="feedback?.message"></span>
                                <template x-if="feedback?.type === 'success' && feedback?.childName">
                                    <span x-text="feedback.childName"></span>
                                </template>
                            </div>
                        </div>

                        <div class="panitia-scanner__actions">
                            <button type="button" class="panitia-button panitia-button--primary" @click="toggleScanner()" :disabled="starting || Boolean(feedback)">
                                <span class="material-symbols-rounded" x-text="scanning ? 'stop' : 'play_arrow'" aria-hidden="true"></span>
                                <span x-text="scanning ? 'Jeda scan' : (cameraActive ? 'Lanjutkan scan' : 'Mulai scan')"></span>
                            </button>
                            <button id="manual-entry-trigger" type="button" class="panitia-button panitia-button--secondary"
                                @click="openManualDialog()">
                                <span class="material-symbols-rounded" aria-hidden="true">edit</span>
                                Input manual
                            </button>
                        </div>
                        <p class="panitia-camera__help">Jika QR tidak terbaca, pilih <b>Input manual</b> lalu cari nama wali atau santri.</p>
                    </section>
                </div>

                <aside class="panitia-history panel" aria-labelledby="attendance-history-title">
                    <div class="panitia-section-heading">
                        <div>
                            <p class="panitia-eyebrow">{{ $activeEvent->date?->translatedFormat('d F Y') ?? '' }}</p>
                            <h2 id="attendance-history-title">Presensi terakhir</h2>
                        </div>
                        <span class="panitia-history__count">Maks. 5</span>
                    </div>

                    @if (session()->has('message'))
                        <div class="panitia-flash is-success" role="status">
                            <span class="material-symbols-rounded" aria-hidden="true">check_circle</span>
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="panitia-flash is-error" role="alert">{{ session('error') }}</div>
                    @endif

                    <div class="panitia-history__list">
                        @forelse($this->recentAttendances as $attendance)
                            <article class="panitia-attendance" wire:key="scanner-attendance-{{ $attendance->id }}">
                                <span class="panitia-attendance__avatar {{ $attendance->parent?->type === 'father' ? 'is-father' : 'is-mother' }}" aria-hidden="true">
                                    <span class="material-symbols-rounded">{{ $attendance->parent?->type === 'father' ? 'man' : 'woman' }}</span>
                                </span>
                                <div class="panitia-attendance__person">
                                    <strong>{{ $attendance->parent?->user?->name ?? 'Nama wali tidak tersedia' }}</strong>
                                    <span>{{ $attendance->parent?->type_display ?? 'Wali santri' }}</span>
                                    <span>Santri: {{ $attendance->parent?->students?->first()?->name ?? 'Belum tercantum' }}</span>
                                </div>
                                <div class="panitia-attendance__actions">
                                    <time datetime="{{ $attendance->created_at->toIso8601String() }}">{{ $attendance->created_at->format('H:i') }}</time>
                                    <button type="button" class="panitia-icon-button panitia-icon-button--danger"
                                        @click="askCancel({{ $attendance->id }}, @js($attendance->parent?->user?->name ?? 'wali santri'))"
                                        aria-label="Batalkan presensi {{ $attendance->parent?->user?->name ?? 'wali santri' }}" title="Batalkan presensi">
                                        <span class="material-symbols-rounded" aria-hidden="true">delete</span>
                                    </button>
                                </div>
                            </article>
                        @empty
                            <div class="panitia-history__empty">
                                <span class="material-symbols-rounded" aria-hidden="true">pageview</span>
                                <strong>Belum ada presensi</strong>
                                <span>Hasil scan akan muncul di sini.</span>
                            </div>
                        @endforelse
                    </div>
                </aside>
            </div>

            <dialog class="panitia-dialog panitia-dialog--manual" x-ref="manualDialog" wire:ignore.self
                @cancel.prevent="closeManualDialog()" @click.self="closeManualDialog()"
                aria-labelledby="manual-dialog-title" aria-describedby="manual-dialog-help">
                <div class="panitia-dialog__heading">
                    <div>
                        <p class="panitia-eyebrow">Alternatif pemindaian</p>
                        <h2 id="manual-dialog-title">Input presensi manual</h2>
                        <p id="manual-dialog-help">Cari wali santri jika QR tidak tersedia atau sulit dibaca.</p>
                    </div>
                    <button type="button" class="panitia-icon-button" @click="closeManualDialog()" aria-label="Tutup input manual">
                        <span class="material-symbols-rounded" aria-hidden="true">close</span>
                    </button>
                </div>

                <label class="panitia-field-label" for="parent-search">Nama wali atau santri</label>
                <div class="panitia-search-field">
                    <span class="material-symbols-rounded" aria-hidden="true">search</span>
                    <input id="parent-search" x-ref="manualSearch" type="search" wire:model.live.debounce.300ms="searchQuery"
                        placeholder="Contoh: Ahmad atau nama santri" autocomplete="off">
                </div>

                <div wire:loading wire:target="searchQuery" class="panitia-search-loading" role="status">Mencari nama…</div>
                <div class="panitia-search-results" aria-live="polite">
                    @forelse($searchResults as $parent)
                        <button type="button" wire:key="manual-parent-{{ $parent->id }}" wire:click="manualCheckIn({{ $parent->id }})"
                            wire:loading.attr="disabled" wire:target="manualCheckIn"
                            class="panitia-search-result">
                            <span class="panitia-attendance__avatar {{ $parent->type === 'father' ? 'is-father' : 'is-mother' }}" aria-hidden="true">
                                <span class="material-symbols-rounded">{{ $parent->type === 'father' ? 'man' : 'woman' }}</span>
                            </span>
                            <span class="panitia-search-result__copy">
                                <strong>{{ $parent->user?->name ?? 'Nama wali tidak tersedia' }}</strong>
                                <span>{{ $parent->type_display }}</span>
                                <span class="panitia-search-result__students">
                                    @foreach($parent->students as $student)
                                        <span>{{ $student->name }}@if($student->classRoom) · {{ $student->classRoom->name }}@endif</span>
                                    @endforeach
                                </span>
                            </span>
                            <span class="material-symbols-rounded panitia-search-result__icon" aria-hidden="true">check_circle</span>
                        </button>
                    @empty
                        @if(strlen($searchQuery) >= 2)
                            <p class="panitia-search-empty">Nama belum ditemukan. Coba kata kunci yang lebih singkat.</p>
                        @else
                            <p class="panitia-search-empty">Ketik minimal 2 huruf untuk mencari wali atau santri.</p>
                        @endif
                    @endforelse
                </div>
            </dialog>

            <dialog class="panitia-dialog panitia-dialog--confirm" x-ref="cancelDialog" aria-labelledby="cancel-dialog-title">
                <div class="panitia-dialog__heading">
                    <div>
                        <p class="panitia-eyebrow">Periksa kembali</p>
                        <h2 id="cancel-dialog-title">Batalkan presensi?</h2>
                    </div>
                    <button type="button" class="panitia-icon-button" @click="$refs.cancelDialog.close()" aria-label="Tutup konfirmasi">
                        <span class="material-symbols-rounded" aria-hidden="true">close</span>
                    </button>
                </div>
                <p class="panitia-dialog__message">Presensi <strong x-text="pendingCancel?.name"></strong> akan dihapus dari kajian ini.</p>
                <div class="panitia-dialog__actions">
                    <button type="button" class="panitia-button panitia-button--secondary" @click="$refs.cancelDialog.close()">Kembali</button>
                    <button type="button" class="panitia-button panitia-button--danger" @click="confirmCancelAttendance($wire)">Ya, batalkan</button>
                </div>
            </dialog>
        @endif
    </main>
</div>
