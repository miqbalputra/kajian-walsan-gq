@props(['event', 'fields' => ['date', 'time', 'location', 'speaker']])
<dl class="wali-event-details">
    @if(in_array('date', $fields))<div><dt><span class="material-symbols-rounded" aria-hidden="true">event</span>Tanggal</dt><dd><time datetime="{{ $event->date->toDateString() }}">{{ $event->formatted_date }}</time></dd></div>@endif
    @if(in_array('time', $fields))<div><dt><span class="material-symbols-rounded" aria-hidden="true">schedule</span>Waktu</dt><dd>{{ $event->time_range }}</dd></div>@endif
    @if(in_array('location', $fields))<div><dt><span class="material-symbols-rounded" aria-hidden="true">location_on</span>Tempat</dt><dd>{{ $event->location ?: 'Belum diumumkan' }}</dd></div>@endif
    @if(in_array('speaker', $fields))<div><dt><span class="material-symbols-rounded" aria-hidden="true">person</span>Pemateri</dt><dd>{{ $event->speaker ?: 'Belum diumumkan' }}</dd></div>@endif
</dl>
