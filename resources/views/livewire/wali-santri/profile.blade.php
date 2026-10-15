<div x-data @profile-updated.window="$nextTick(() => window.dispatchEvent(new CustomEvent('wali-profile-header-updated', { detail: { avatar: $wire.avatar, name: $wire.name } })))">
    @php
        $avatarParts = explode('|', str_replace('icon:', '', $avatar));
        $avatarNames = ['mosque'=>'Masjid','menu_book'=>'Buku','auto_stories'=>'Buku terbuka','lightbulb'=>'Lampu','verified'=>'Centang','stars'=>'Bintang','favorite'=>'Hati','history_edu'=>'Catatan','school'=>'Sekolah','diamond'=>'Berlian','auto_awesome'=>'Kilau','import_contacts'=>'Kitab'];
    @endphp
    <div class="wali-page-heading"><h1 data-wali-return-focus tabindex="-1">Profil Saya</h1>
        <div class="wali-profile-heading" style="margin-top:16px"><div class="wali-profile-avatar {{ $avatarParts[1] ?? 'bg-indigo-500' }}"><span class="material-symbols-rounded" aria-hidden="true">{{ $avatarParts[0] ?? 'mosque' }}</span></div><div><p><strong>{{ $name }}</strong></p><p class="wali-small">{{ '@' . $username }}</p></div></div>
    </div>
    @if(session()->has('message'))<p class="wali-notice wali-notice-success" role="status">{{ session('message') }}</p>@endif
    @if(session()->has('password-message'))<p class="wali-notice wali-notice-success" role="status">{{ session('password-message') }}</p>@endif
    @if(session()->has('google-success'))<p class="wali-notice wali-notice-success" role="status">{{ session('google-success') }}</p>@endif
    @if(session()->has('google-error'))<p class="wali-notice wali-notice-error" role="alert">{{ session('google-error') }}</p>@endif

    <div class="wali-profile">
        <section class="wali-panel wali-profile-contact" aria-labelledby="contact-title">
            <div class="wali-panel-heading"><h2 id="contact-title">Informasi Kontak</h2></div>
            @if(auth()->user()->isWaliSantri() && !auth()->user()->hasGmailEmail())
                <div x-data="{ visible: sessionStorage.getItem('dismiss-gmail-prompt') !== 'true' }" x-show="visible" x-cloak class="wali-notice" role="status">
                    <div class="wali-panel-heading">
                        <strong>Email belum menggunakan Gmail.</strong>
                        <button type="button" class="wali-icon-button" aria-label="Tutup pengingat Gmail" @click="visible = false; sessionStorage.setItem('dismiss-gmail-prompt', 'true')"><span class="material-symbols-rounded" aria-hidden="true">close</span></button>
                    </div>
                    <p>Hubungkan akun Google Bapak/Ibu untuk menyimpan Gmail dan memudahkan login.</p>
                    <a href="{{ route('google.link') }}" class="wali-button wali-button-primary wali-button-full">Hubungkan Gmail</a>
                </div>
            @endif
            <form wire:submit="updateProfile">
                @foreach(['name'=>['Nama Lengkap','text','name'], 'username'=>['Username','text','username'], 'email'=>[auth()->user()->isWaliSantri() ? 'Email Gmail' : 'Email','email','email'], 'phone'=>['Nomor WhatsApp','tel','tel']] as $field=>[$label,$type,$autocomplete])
                    <div class="wali-field">
                        <label for="profile-{{ $field }}">{{ $label }}</label>
                        <input id="profile-{{ $field }}" type="{{ $type }}"
                            @if($field==='email' && auth()->user()->isWaliSantri()) wire:model.live.debounce.400ms="email" placeholder="nama@gmail.com" @else wire:model="{{ $field }}" @endif
                            autocomplete="{{ $autocomplete }}" required aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}" aria-describedby="profile-{{ $field }}-error"
                            @if($field==='phone') placeholder="08..." @endif @if($field==='username') autocapitalize="none" spellcheck="false" @endif>
                        <p id="profile-{{ $field }}-error" class="wali-error" role="alert">@error($field){{ $message }}@enderror</p>
                        @if($field==='email' && auth()->user()->isWaliSantri())
                            <p class="wali-small">Wajib menggunakan Gmail dengan akhiran @gmail.com.</p>
                            @unless(\App\Models\User::isGmailAddress($email))
                                <p class="wali-error" role="alert">Alamat ini bukan Gmail. Masukkan alamat yang berakhiran @gmail.com.</p>
                            @endunless
                        @endif
                    </div>
                @endforeach
                <div class="wali-dialog-actions"><button type="submit" class="wali-button wali-button-primary wali-button-full" wire:loading.attr="disabled" wire:target="updateProfile"><span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span><span wire:loading wire:target="updateProfile">Menyimpan...</span></button></div>
            </form>
        </section>
        <div class="wali-profile-side">
        @if($parentData && !$parentData->isPureTeacher())
            <section class="wali-panel wali-profile-card wali-prompt" aria-labelledby="card-title"><h2 id="card-title">Kartu Identitas</h2><p>Kartu wali santri dengan QR Code untuk presensi hadir langsung.</p><button type="button" wire:click="showCard" class="wali-button wali-button-secondary wali-button-full" wire:loading.attr="disabled" wire:target="showCard" data-focus-key="show-card"><span wire:loading.remove wire:target="showCard">Lihat &amp; Cetak Kartu</span><span wire:loading wire:target="showCard">Memuat Kartu...</span></button></section>
        @endif
        <section class="wali-panel wali-profile-avatar-panel" aria-labelledby="avatar-title"><h2 id="avatar-title">Pilih Karakter</h2><p class="wali-muted" style="margin-top:8px">Pilih ikon untuk profil Bapak/Ibu.</p>
            <div class="wali-avatar-grid">@foreach($avatarPresets as $index=>$preset)<button type="button" wire:key="avatar-{{ $preset['icon'] }}" wire:click="selectAvatar('{{ $preset['icon'] }}', '{{ $preset['color'] }}')" class="{{ $preset['color'] }}" aria-label="Pilih karakter {{ $avatarNames[$preset['icon']] ?? $preset['icon'] }}" aria-pressed="{{ $avatar === 'icon:'.$preset['icon'].'|'.$preset['color'] ? 'true' : 'false' }}"><span class="material-symbols-rounded" aria-hidden="true">{{ $preset['icon'] }}</span>@if($avatar === 'icon:'.$preset['icon'].'|'.$preset['color'])<span class="text-sm">Dipilih</span>@endif</button>@endforeach</div>
            <p class="wali-small">Tekan “Simpan Perubahan” pada Informasi Kontak untuk menyimpan pilihan karakter.</p>
        </section>
        </div>
        <section class="wali-panel wali-profile-security" aria-labelledby="security-title"><div class="wali-panel-heading"><h2 id="security-title">Keamanan Akun</h2></div><p class="wali-muted" style="margin-bottom:20px">Gunakan kata sandi yang mudah Bapak/Ibu ingat dan sulit ditebak orang lain.</p>
            <form wire:submit="updatePassword">
                <input type="text" name="username" value="{{ $username }}" autocomplete="username" hidden>
                @foreach(['current_password'=>['Password Saat Ini','current-password'], 'new_password'=>['Password Baru','new-password'], 'new_password_confirmation'=>['Konfirmasi Password Baru','new-password']] as $field=>[$label,$autocomplete])
                    <div class="wali-field" x-data="{ show: false }"><label for="profile-{{ $field }}">{{ $label }}</label><div class="wali-password"><input id="profile-{{ $field }}" type="password" :type="show ? 'text' : 'password'" wire:model="{{ $field }}" autocomplete="{{ $autocomplete }}" required aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}" aria-describedby="profile-{{ $field }}-error"><button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan {{ $label }}' : 'Tampilkan {{ $label }}'" aria-label="Tampilkan {{ $label }}" :aria-pressed="show" aria-controls="profile-{{ $field }}" x-text="show ? 'Tutup' : 'Lihat'">Lihat</button></div><p id="profile-{{ $field }}-error" class="wali-error" role="alert">@error($field){{ $message }}@enderror</p>@if($field==='new_password')<p class="wali-small" style="margin-top:8px">Minimal 8 karakter.</p>@endif</div>
                @endforeach
                <div class="wali-dialog-actions"><button type="submit" class="wali-button wali-button-primary wali-button-full" wire:loading.attr="disabled" wire:target="updatePassword"><span wire:loading.remove wire:target="updatePassword">Ganti Password</span><span wire:loading wire:target="updatePassword">Memproses...</span></button></div>
            </form>
        </section>
        <section class="wali-panel wali-profile-logout wali-prompt" aria-labelledby="logout-title"><h2 id="logout-title">Keluar Aplikasi</h2><p>Pastikan perubahan profil sudah disimpan sebelum keluar.</p><button type="submit" form="wali-logout-form" class="wali-button wali-button-danger">Keluar Sekarang</button></section>
    </div>

    @if($showCardModal && $parentData && !$parentData->isPureTeacher())
        <x-wali.dialog id="wali-id-card" title="Kartu Identitas" state="showCardModal"><p class="wali-dialog-description">Ukuran standar KTP Indonesia. Gunakan Download Kartu untuk menyimpan atau mencetak.</p><x-wali.printed-card :parent-data="$parentData" :qr-code-svg="$qrCodeSvg" /><div class="wali-dialog-actions"><button type="button" @click="requestClose()" class="wali-button wali-button-secondary">Tutup</button><a href="{{ route('wali-santri.kartu.download') }}" target="_blank" rel="noopener noreferrer" class="wali-button wali-button-primary">Download Kartu</a></div></x-wali.dialog>
    @endif
    @if($showPasswordSuccessModal)
        <x-wali.dialog id="wali-password-success" title="Password Berhasil Diubah!" :dismissible="false">
            <p class="wali-dialog-description">Jika Google atau browser menampilkan kotak penyimpanan kata sandi, pilih “Update password” atau “Simpan” terlebih dahulu.</p><p>Setelah itu, masuk kembali menggunakan password baru Bapak/Ibu.</p><div class="wali-dialog-actions"><button type="button" wire:click="logoutAfterPasswordChange" wire:loading.attr="disabled" class="wali-button wali-button-primary"><span wire:loading.remove wire:target="logoutAfterPasswordChange">Masuk Kembali</span><span wire:loading wire:target="logoutAfterPasswordChange">Memproses...</span></button></div>
        </x-wali.dialog>
    @endif
</div>
