@props(['property', 'file', 'label', 'id'])
<div class="wali-field">
    <label for="{{ $id }}">{{ $label }} <span aria-hidden="true">*</span></label>
    <div class="wali-upload" data-compress-container>
        <input type="file" id="{{ $id }}" accept="image/jpeg,image/png" data-image-upload-input
            x-on:change="uploadLivewire($wire, @js($property), $event)" x-bind:disabled="isInputBusy()"
            aria-describedby="{{ $id }}-help {{ $id }}-error" aria-required="true" @if($errors->has($property)) aria-invalid="true" @endif>
        @if($file)
            <img class="wali-upload-preview" src="{{ $file->temporaryUrl() }}" alt="Pratinjau {{ strtolower($label) }}">
            <p>Foto sudah dipilih. Gunakan tombol di atas untuk menggantinya.</p>
        @endif
        <p id="{{ $id }}-help" class="wali-small">JPG atau PNG. Foto otomatis diperkecil sebelum dikirim.</p>
    </div>
    <div x-cloak x-show="hasUploadStatus()" class="wali-upload-status" role="status">
        <div><span x-text="statusText"></span><span x-show="uploadState === 'uploading'" x-text="`${progress}%`"></span></div>
        <div class="wali-progress" role="progressbar" aria-label="Progres upload foto" x-bind:aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"><span x-bind:style="`width: ${progress}%`"></span></div>
        <p class="wali-small" x-show="originalSize && compressedSize">Ukuran: <span x-text="originalSize"></span> → <span x-text="compressedSize"></span></p>
        <p class="wali-error" x-show="errorMessage" x-text="errorMessage"></p>
    </div>
    <p id="{{ $id }}-error" class="wali-error" role="alert">@error($property) {{ str_starts_with($message, 'The ') ? (str_contains($message, 'required') ? 'Pilih foto bukti terlebih dahulu.' : (str_contains($message, 'failed to upload') ? 'Foto belum dapat diupload. Periksa koneksi lalu coba lagi.' : 'Gunakan foto JPG atau PNG dengan ukuran maksimal 2 MB.')) : $message }} @enderror</p>
</div>
