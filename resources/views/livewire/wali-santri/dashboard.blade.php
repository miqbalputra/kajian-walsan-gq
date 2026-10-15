<div>
    @php
        $parent = $this->parent;
        $active = $this->activeEvent;
        $attendance = $this->myAttendanceToday;
        $attendanceNames = ['hadir_fisik' => 'Hadir Langsung', 'hadir_online' => 'Hadir Online', 'izin' => 'Izin', 'alpha' => 'Tidak hadir'];
        $otherRejected = $this->rejectedAttendances->reject(fn ($item) => $item->id === $attendance?->id);
    @endphp
    <div class="wali-page-heading">
        <p class="wali-eyebrow">Assalamu'alaikum,</p>
        <h1 data-wali-return-focus tabindex="-1">{{ \Illuminate\Support\Str::title(auth()->user()->name) }} @if($this->isGuru)<span class="wali-chip">Guru</span>@endif</h1>
        @if($parent?->students->count() > 0)
            <div class="wali-children" aria-label="Ananda">@foreach($parent->students as $child)<span>{{ $child->name }}</span>@endforeach</div>
        @endif
    </div>
    @if(session()->has('message'))<p class="wali-notice wali-notice-success" role="status">{{ session('message') }}</p>@endif
    @if(session()->has('error'))<p class="wali-notice wali-notice-error" role="alert">{{ session('error') }}</p>@endif

    @if($parent?->isGuardian() && !auth()->user()->hasGmailEmail())
        <section x-data="{ visible: sessionStorage.getItem('dismiss-gmail-prompt') !== 'true' }" x-show="visible" x-cloak class="wali-panel wali-prompt" aria-labelledby="gmail-login-title">
            <div class="wali-panel-heading">
                <h2 id="gmail-login-title">Tambahkan Gmail untuk login lebih mudah</h2>
                <button type="button" class="wali-icon-button" aria-label="Tutup pengingat Gmail" @click="visible = false; sessionStorage.setItem('dismiss-gmail-prompt', 'true')"><span class="material-symbols-rounded" aria-hidden="true">close</span></button>
            </div>
            <p>Email akun Bapak/Ibu belum menggunakan Gmail. Hubungkan akun Google agar Gmail tersimpan otomatis dan bisa digunakan untuk login.</p>
            <a href="{{ route('google.link') }}" class="wali-button wali-button-primary">Hubungkan Gmail</a>
        </section>
    @endif

    <div class="wali-dashboard">
        <div class="wali-current wali-stack">
            <section class="wali-panel" aria-labelledby="current-kajian-title">
                @if($active)
                    <div class="wali-panel-heading"><p class="wali-eyebrow">{{ $active->category_display }}</p><span class="wali-chip wali-chip-success">Presensi dibuka</span></div>
                    <h2 id="current-kajian-title" class="wali-event-title">{{ $active->title }}</h2>
                    <x-wali.event-details :event="$active" :fields="['date', 'time']" />
                    @if($attendance)
                        <div class="wali-attendance wali-attendance-{{ $attendance->validation_status }}">
                            <p class="wali-eyebrow">Kehadiran Bapak/Ibu</p>
                            <p><strong>{{ $attendanceNames[$attendance->status] ?? 'Presensi' }}</strong></p>
                            @if($attendance->validation_status === 'rejected')
                                <h3>Bukti Presensi Ditolak</h3><p class="wali-muted">Perbaiki bukti sesuai keterangan petugas, lalu kirim ulang.</p>
                                @if($attendance->rejection_reason)<div class="wali-reason"><strong>Alasan penolakan</strong><p>{{ $attendance->rejection_reason }}</p></div>@endif
                                <button type="button" wire:click="openReuploadModal({{ $attendance->id }})" class="wali-button wali-button-primary wali-button-full" style="margin-top:16px" data-focus-key="reupload-{{ $attendance->id }}">Upload Ulang Bukti</button>
                            @elseif($attendance->validation_status === 'pending')
                                @if($this->isWaliGuru && $attendance->status === 'hadir_fisik' && !$attendance->proof_file)
                                    <h3>Catatan kajian belum dikirim</h3><p class="wali-muted">Presensi QR sudah tercatat. Lengkapi dengan catatan kajian Bapak/Ibu.</p>
                                @else
                                    <h3>{{ match($attendance->ai_validation_status) { 'queued' => 'Sedang diperiksa otomatis', 'failed' => 'Perlu pemeriksaan admin', default => 'Menunggu pemeriksaan' } }}</h3>
                                    <p class="wali-muted">{{ $attendance->proof_file ? 'Bukti sudah dikirim. Hasil pemeriksaan akan tampil di sini.' : 'Kehadiran menunggu pemeriksaan petugas.' }}</p>
                                @endif
                            @else
                                <h3>Kehadiran sudah tercatat</h3><p class="wali-muted">Presensi Bapak/Ibu telah dikonfirmasi.</p>
                            @endif

                            @if($this->isWaliGuru && $attendance->status === 'hadir_fisik' && !$attendance->proof_file)
                                <div class="wali-reason"><strong>Lengkapi catatan kajian</strong><p>Sebagai Guru, Bapak/Ibu perlu mengupload catatan kajian untuk melengkapi hadir langsung.</p>
                                    <button type="button" wire:click="openReuploadModal({{ $attendance->id }}, false, true)" class="wali-button wali-button-secondary wali-button-full" style="margin-top:12px">Upload Catatan Kajian</button>
                                </div>
                            @endif
                            <div class="wali-actions">
                                @if($attendance->proof_file)<a class="wali-button wali-button-secondary" href="{{ $attendance->proof_file }}" target="_blank" rel="noopener noreferrer">Lihat Bukti Terkirim</a>@endif
                                @if($attendance->validation_status === 'pending' && in_array($attendance->method, ['upload']))
                                    <button type="button" wire:click="openReuploadModal({{ $attendance->id }}, true)" class="wali-button wali-button-secondary" data-focus-key="replace-{{ $attendance->id }}">Ganti Foto Bukti</button><p class="wali-small">Foto lama akan diganti dengan foto yang baru.</p>
                                @endif
                                @if((in_array($attendance->validation_status, ['pending', 'rejected']) || ($attendance->validation_status === 'approved' && is_null($attendance->validated_by))) && ($attendance->method === 'upload' || ($attendance->method === 'scan_qr' && $attendance->proof_file)))
                                    <button type="button" wire:click="cancelSubmittedAttendance({{ $attendance->id }})" wire:confirm="Batalkan kiriman ini? File yang sudah diupload akan dihapus." class="wali-button wali-button-danger">Batalkan Kiriman &amp; Pilih Ulang</button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="wali-attendance"><p class="wali-eyebrow">Kehadiran Bapak/Ibu</p><p>Belum tercatat pada kajian ini.</p></div>
                        <div class="wali-actions">
                            @unless($this->isPureGuru)
                                <button type="button" wire:click="$set('showQrModal', true)" class="wali-button wali-button-primary" data-focus-key="show-qr"><span class="material-symbols-rounded" aria-hidden="true">qr_code_2</span>Tampilkan QR Code Saya</button>
                                <p class="wali-small">Untuk hadir langsung, tunjukkan QR ke petugas.</p>
                            @endunless
                            @if($this->isGuru)
                                <button type="button" wire:click="openPhysicalAttendanceModal" class="wali-button {{ $this->isPureGuru ? 'wali-button-primary' : 'wali-button-secondary' }}">Upload Hadir Langsung</button>
                                <div class="wali-action-options"><button type="button" wire:click="openOnlineAttendanceModal" class="wali-button wali-button-secondary">Upload Menyimak Online</button><button type="button" wire:click="openIzinModal" class="wali-button wali-button-secondary">Upload Izin</button></div>
                            @else
                                <div class="wali-action-options {{ ($active->policy['online_enabled'] ?? false) ? '' : 'one-option' }}">
                                    @if($active->policy['online_enabled'] ?? false)<button type="button" wire:click="openOnlineAttendanceModal" class="wali-button wali-button-secondary" data-focus-key="online">Hadir Online</button>@endif
                                    <button type="button" wire:click="openIzinModal" class="wali-button wali-button-secondary" data-focus-key="izin">Izin</button>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="wali-event-secondary"><x-wali.event-details :event="$active" :fields="['location', 'speaker']" /></div>
                @else
                    <div class="wali-empty"><h2 id="current-kajian-title">Presensi belum dibuka</h2><p>Belum ada presensi yang dibuka untuk Bapak/Ibu. Jadwal dapat dilihat pada halaman Jadwal.</p><a class="wali-link" href="{{ route('wali-santri.schedule') }}">Lihat jadwal kajian</a></div>
                    @if($this->upcomingEvent)
                        <div class="wali-attendance" x-data="{ showUpcoming: localStorage.getItem('hide-event-{{ $this->upcomingEvent->id }}') !== 'true' }" x-show="showUpcoming">
                            <div class="wali-panel-heading"><div><p class="wali-eyebrow">Kajian berikutnya</p><h3>{{ $this->upcomingEvent->title }}</h3></div><button type="button" class="wali-icon-button" @click="showUpcoming = false; localStorage.setItem('hide-event-{{ $this->upcomingEvent->id }}', 'true')" aria-label="Sembunyikan jadwal berikutnya"><span class="material-symbols-rounded" aria-hidden="true">close</span></button></div>
                            <x-wali.event-details :event="$this->upcomingEvent" />
                        </div>
                    @endif
                @endif
            </section>

            @if($otherRejected->isNotEmpty())
                <section class="wali-panel" aria-labelledby="rejected-title"><div class="wali-panel-heading"><h2 id="rejected-title">Bukti kajian lain perlu diperbaiki</h2></div>
                    <ul class="wali-rejected-list">@foreach($otherRejected as $rejected)<li wire:key="rejected-{{ $rejected->id }}">
                        <h3>{{ $rejected->kajianEvent?->title }}</h3><p class="wali-small">{{ $rejected->kajianEvent?->formatted_date }} · {{ $attendanceNames[$rejected->status] ?? 'Presensi' }}</p>
                        @if($rejected->rejection_reason)<div class="wali-reason"><strong>Alasan penolakan</strong><p>{{ $rejected->rejection_reason }}</p></div>@endif
                        <button type="button" wire:click="openReuploadModal({{ $rejected->id }})" class="wali-button wali-button-secondary" style="margin-top:12px" data-focus-key="rejected-{{ $rejected->id }}">Upload Ulang Bukti</button>
                    </li>@endforeach</ul>
                </section>
            @endif
        </div>

        <section class="wali-panel wali-history" aria-labelledby="history-title">
            <div class="wali-panel-heading"><h2 id="history-title">Riwayat Kehadiran</h2></div>
            <ul class="wali-history-list">
                @forelse($this->attendanceHistory as $item)
                    <li wire:key="history-{{ $item->id }}"><h3>{{ $item->kajianEvent?->title }}</h3><p class="wali-small">{{ $item->kajianEvent?->formatted_date }}</p>
                        <div class="wali-history-status"><span class="wali-chip">{{ $attendanceNames[$item->status] ?? 'Presensi' }}</span>
                            <span>{{ match($item->validation_status) { 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => match($item->ai_validation_status) { 'queued' => 'Sedang diperiksa otomatis', 'failed' => 'Perlu pemeriksaan admin', default => 'Menunggu pemeriksaan' } } }}</span>
                        </div>
                        @if($item->validation_status === 'rejected' && $item->rejection_reason && $item->id !== $attendance?->id && !$otherRejected->contains('id', $item->id))<p class="wali-error">Alasan: {{ $item->rejection_reason }}</p>@endif
                        @if($item->validation_status === 'approved' && in_array($item->method, ['upload','manual','scan_qr']) && $item->proof_file)<a href="{{ $item->proof_file }}" target="_blank" rel="noopener noreferrer" class="wali-link">Lihat Bukti</a>@endif
                    </li>
                @empty
                    <li class="wali-empty"><p>Belum ada riwayat kehadiran.</p></li>
                @endforelse
            </ul>
        </section>

        <div class="wali-extras wali-stack">
            <x-wali.notifications />
            @if($this->pendingFeedbackEvents->isNotEmpty())
                <section class="wali-panel wali-prompt" aria-labelledby="survey-title"><h2 id="survey-title">Penilaian Kajian</h2><p>Berikan masukan untuk kajian yang sudah Bapak/Ibu ikuti.</p><div class="wali-stack" style="margin-top:20px">
                    @foreach($this->pendingFeedbackEvents as $event)<div class="wali-survey-item" wire:key="survey-{{ $event->id }}"><h3>{{ $event->title }}</h3><p class="wali-small">{{ $event->formatted_date }}</p><button type="button" wire:click="openFeedbackModal({{ $event->id }})" class="wali-button wali-button-secondary" data-focus-key="survey-{{ $event->id }}">Isi Survey</button></div>@endforeach
                </div></section>
            @endif
        </div>
    </div>

    @if($showQrModal && !$this->isPureGuru)
        <x-wali.dialog id="wali-qr" title="QR Code Anda" state="showQrModal"><p class="wali-dialog-description">Tunjukkan QR ini kepada petugas untuk mencatat hadir langsung.</p><div class="wali-qr">{!! $this->qrCodeSvg !!}</div><p class="wali-qr-text"><strong>{{ auth()->user()->name }}</strong></p><p class="wali-small wali-qr-text">{{ $parent?->type_display }}</p><p class="wali-small wali-qr-text">{{ $parent?->qr_code_string }}</p><div class="wali-dialog-actions"><button type="button" class="wali-button wali-button-secondary" @click="requestClose()">Tutup</button></div></x-wali.dialog>
    @endif

    @if($showPhysicalModal)
        <x-wali.dialog id="wali-physical" title="Upload Hadir Langsung" close="cancelPhysicalAttendanceSelection">
            <p class="wali-dialog-description">Upload foto catatan kajian yang jelas untuk melengkapi hadir langsung.</p>
            <form wire:submit="submitPhysicalAttendance" wire:key="physical-form" x-data="imageUploadForm()" x-on:submit="markSaving($event)" :data-busy="isSubmitBusy()">
                <x-wali.upload property="proofPhoto" :file="$proofPhoto" label="Foto Catatan Kajian" id="physical-photo" />
                <div class="wali-field"><label for="physical-notes">Catatan tambahan (opsional)</label><textarea id="physical-notes" wire:model="notes" rows="3" placeholder="Pesan untuk petugas"></textarea>@error('notes')<p class="wali-error" role="alert">{{ $message }}</p>@enderror</div>
                <div class="wali-dialog-actions"><button type="button" @click="requestClose()" :disabled="isSubmitBusy()" class="wali-button wali-button-secondary">Batalkan Pilihan</button><button type="submit" class="wali-button wali-button-primary" x-bind:disabled="isSubmitBusy()" wire:loading.attr="disabled"><span wire:loading.remove wire:target="submitPhysicalAttendance">Kirim Catatan</span><span wire:loading wire:target="submitPhysicalAttendance">Mengirim...</span></button></div>
            </form>
        </x-wali.dialog>
    @endif

    @if($showOnlineModal)
        <x-wali.dialog id="wali-online" :title="$this->isGuru ? 'Upload Menyimak Online' : 'Hadir Online'" close="cancelOnlineAttendanceSelection">
            <p class="wali-dialog-description">Upload foto catatan hasil kajian tulisan tangan yang terlihat jelas sebagai bukti mengikuti online. Gunakan catatan Bapak/Ibu sendiri.</p>
            <form wire:submit="submitOnlineAttendance" wire:key="online-form" x-data="imageUploadForm()" x-on:submit="markSaving($event)" :data-busy="isSubmitBusy()">
                <x-wali.upload property="proofPhoto" :file="$proofPhoto" :label="$this->isGuru ? 'Foto Catatan Kajian' : 'Foto Bukti Online'" id="online-photo" />
                <div class="wali-field"><label for="online-notes">Catatan tambahan (opsional)</label><textarea id="online-notes" wire:model="notes" rows="3" placeholder="Pesan untuk petugas"></textarea>@error('notes')<p class="wali-error" role="alert">{{ $message }}</p>@enderror</div>
                <div class="wali-dialog-actions"><button type="button" @click="requestClose()" :disabled="isSubmitBusy()" class="wali-button wali-button-secondary">Batalkan Pilihan</button><button type="submit" class="wali-button wali-button-primary" x-bind:disabled="isSubmitBusy()" wire:loading.attr="disabled"><span wire:loading.remove wire:target="submitOnlineAttendance">Kirim Presensi Online</span><span wire:loading wire:target="submitOnlineAttendance">Mengirim...</span></button></div>
            </form>
        </x-wali.dialog>
    @endif

    @if($showIzinModal)
        <x-wali.dialog id="wali-izin" :title="$this->isGuru ? 'Upload Izin' : 'Izin / Berhalangan'" close="cancelIzinSelection">
            <p class="wali-dialog-description">Upload foto surat atau keterangan berhalangan hadir yang lengkap dan terbaca, lalu tuliskan alasannya.</p>
            <form wire:submit="submitIzin" wire:key="izin-form" x-data="imageUploadForm()" x-on:submit="markSaving($event)" :data-busy="isSubmitBusy()">
                <x-wali.upload property="izinDocument" :file="$izinDocument" :label="$this->isGuru ? 'Surat Pernyataan' : 'Surat/Dokumen Izin'" id="izin-photo" />
                <div class="wali-field"><label for="izin-notes">Alasan <span aria-hidden="true">*</span></label><textarea id="izin-notes" wire:model="notes" rows="3" aria-required="true" aria-describedby="izin-notes-error" placeholder="Alasan berhalangan hadir"></textarea><p id="izin-notes-error" class="wali-error" role="alert">@error('notes'){{ $message }}@enderror</p></div>
                <div class="wali-dialog-actions"><button type="button" @click="requestClose()" :disabled="isSubmitBusy()" class="wali-button wali-button-secondary">Batalkan Pilihan</button><button type="submit" class="wali-button wali-button-primary" x-bind:disabled="isSubmitBusy()" wire:loading.attr="disabled"><span wire:loading.remove wire:target="submitIzin">Kirim Izin</span><span wire:loading wire:target="submitIzin">Mengirim...</span></button></div>
            </form>
        </x-wali.dialog>
    @endif

    @if($showReuploadModal)
        <x-wali.dialog id="wali-reupload" :title="$reuploadIsInitialTeacherNote ? 'Upload Catatan Kajian' : ($reuploadIsPendingReplace ? 'Ganti Foto Bukti' : 'Upload Ulang Bukti')" close="cancelReuploadSelection">
            <p class="wali-dialog-description">{{ $reuploadIsInitialTeacherNote ? 'Upload catatan kajian untuk melengkapi hadir langsung.' : ($reuploadIsPendingReplace ? 'Pilih foto baru untuk mengganti bukti yang belum selesai diperiksa.' : 'Perbaiki foto sesuai alasan penolakan, kemudian kirim ulang.') }}</p>
            <form wire:submit="reuploadProof" wire:key="reupload-form" x-data="imageUploadForm()" x-on:submit="markSaving($event)" :data-busy="isSubmitBusy()">
                <x-wali.upload property="reuploadFile" :file="$reuploadFile" label="Foto Bukti Baru" id="reupload-photo" />
                <div class="wali-dialog-actions"><button type="button" @click="requestClose()" :disabled="isSubmitBusy()" class="wali-button wali-button-secondary">Batalkan Pilihan</button><button type="submit" class="wali-button wali-button-primary" x-bind:disabled="isSubmitBusy()" wire:loading.attr="disabled"><span wire:loading.remove wire:target="reuploadProof">{{ $reuploadIsInitialTeacherNote ? 'Kirim Catatan' : ($reuploadIsPendingReplace ? 'Ganti Foto' : 'Kirim Ulang') }}</span><span wire:loading wire:target="reuploadProof">Mengirim...</span></button></div>
            </form>
        </x-wali.dialog>
    @endif

    @if($showFeedbackModal)
        <x-wali.dialog id="wali-feedback" title="Penilaian Kajian" state="showFeedbackModal">
            <p class="wali-dialog-description">Masukan Bapak/Ibu membantu perbaikan kajian berikutnya.</p>
            @error('feedbackEventId')<p class="wali-error" role="alert">{{ $message }}</p>@enderror
            <form wire:submit="submitFeedback" wire:loading.attr="data-busy" wire:target="submitFeedback">
                @foreach(['ratingMateri' => 'Isi Materi', 'ratingOperasional' => 'Kualitas Teknis / Fasilitas'] as $property => $label)
                    <fieldset class="wali-rating"><legend>{{ $label }}</legend><div class="wali-rating-buttons">
                        @for($i=1; $i<=5; $i++)<button type="button" wire:click="$set('{{ $property }}', {{ $i }})" aria-label="{{ $label }}: {{ $i }} dari 5" aria-pressed="{{ $$property === $i ? 'true' : 'false' }}" data-filled="{{ $$property >= $i ? 'true' : 'false' }}"><span class="material-symbols-rounded" aria-hidden="true">grade</span></button>@endfor
                    </div><p class="wali-small">{{ $$property ? $$property . ' dari 5' : 'Belum dipilih' }}</p>@error($property)@if(!$$property)<p class="wali-error" role="alert">{{ $message }}</p>@endif@enderror</fieldset>
                @endforeach
                <div class="wali-field"><label for="feedback-comment">Komentar / Saran (opsional)</label><textarea id="feedback-comment" wire:model="feedbackComment" rows="3" maxlength="1000" aria-describedby="feedback-comment-error" aria-invalid="{{ $errors->has('feedbackComment') ? 'true' : 'false' }}" placeholder="Masukan untuk kajian ini"></textarea><p id="feedback-comment-error" class="wali-error" role="alert">@error('feedbackComment')Komentar maksimal 1.000 karakter.@enderror</p></div>
                <div class="wali-dialog-actions"><button type="button" @click="requestClose()" class="wali-button wali-button-secondary">Nanti Saja</button><button type="submit" class="wali-button wali-button-primary" wire:loading.attr="disabled"><span wire:loading.remove wire:target="submitFeedback">Kirim Feedback</span><span wire:loading wire:target="submitFeedback">Mengirim...</span></button></div>
            </form>
        </x-wali.dialog>
    @endif
</div>
