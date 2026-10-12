@props(['id', 'title', 'close' => null, 'state' => null, 'dismissible' => true])
<dialog id="{{ $id }}" class="wali-dialog" wire:key="dialog-{{ $id }}" wire:ignore.self aria-labelledby="{{ $id }}-title"
    x-data="waliDialog({ dismissible: @js($dismissible), onClose: () => {{ $close ? '$wire.$call(' . \Illuminate\Support\Js::from($close) . ')' : ($state ? '$wire.$set(' . \Illuminate\Support\Js::from($state) . ', false)' : '$el.close()') }} })"
    @cancel.prevent="requestClose()" @keydown.tab="trapTab($event)" @click="if ($event.target === $el) requestClose()">
    <div class="wali-dialog-content">
        <div class="wali-dialog-heading"><h2 id="{{ $id }}-title" tabindex="-1">{{ $title }}</h2>
            @if($dismissible)
                <button type="button" class="wali-icon-button" @click="requestClose()" :disabled="closing" aria-label="Tutup {{ $title }}"><span class="material-symbols-rounded" aria-hidden="true">close</span></button>
            @endif
        </div>
        {{ $slot }}
    </div>
</dialog>
