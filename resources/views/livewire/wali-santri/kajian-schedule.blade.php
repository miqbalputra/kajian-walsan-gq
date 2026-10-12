<div>
    <div class="wali-page-heading"><h1 data-wali-return-focus tabindex="-1">Jadwal Kajian</h1><p>Pilih tanggal atau kajian untuk melihat rincian.</p></div>
    <div class="wali-calendar-layout">
        <section class="wali-panel" aria-labelledby="calendar-month">
            <div class="wali-calendar-controls"><h2 id="calendar-month" aria-live="polite">{{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}</h2>
                <div><button type="button" wire:click="prevMonth" class="wali-icon-button" aria-label="Bulan sebelumnya"><span class="material-symbols-rounded" aria-hidden="true">chevron_left</span></button><button type="button" wire:click="nextMonth" class="wali-icon-button" aria-label="Bulan berikutnya"><span class="material-symbols-rounded" aria-hidden="true">chevron_right</span></button></div>
            </div>
            <div class="wali-calendar-grid wali-calendar-weekdays" aria-hidden="true">@foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)<span>{{ $day }}</span>@endforeach</div>
            <div class="wali-calendar-grid">
                @foreach($calendarDays as $dayInfo)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $dayInfo['year'], $dayInfo['month'], $dayInfo['day']);
                        $dayEvents = $events[$dateStr] ?? [];
                        $isToday = $dateStr === today()->toDateString();
                        $dayClass = 'wali-calendar-day ' . (!$dayInfo['current_month'] ? 'is-outside ' : '') . ($isToday ? 'is-today' : '');
                    @endphp
                    @if(count($dayEvents))
                        <button type="button" wire:key="day-{{ $dateStr }}" wire:click="showDay('{{ $dateStr }}')" class="{{ $dayClass }}" aria-label="{{ \Carbon\Carbon::parse($dateStr)->translatedFormat('l, d F Y') }}, {{ count($dayEvents) }} acara" @if($isToday) aria-current="date" @endif>
                            <span>{{ $dayInfo['day'] }}</span><small aria-hidden="true">{{ count($dayEvents) }}</small>
                        </button>
                    @else
                        <span class="{{ $dayClass }}" wire:key="day-{{ $dateStr }}" @if($isToday) aria-current="date" @endif>{{ $dayInfo['day'] }}</span>
                    @endif
                @endforeach
            </div>
            <p class="wali-small" style="margin-top:16px">Tanggal berwarna memiliki jadwal kajian. Angka kecil menunjukkan jumlah acara.</p>
        </section>
        <section aria-labelledby="agenda-title">
            <div class="wali-panel-heading"><h2 id="agenda-title">Daftar kajian</h2><span class="wali-chip">{{ collect($events)->flatten(1)->count() }} acara</span></div>
            <div class="wali-agenda-list">
                @forelse(collect($events)->flatten(1)->sortBy('date')->values() as $event)
                    <button type="button" wire:key="agenda-{{ $event['id'] }}" wire:click="showDetail({{ $event['id'] }})" class="wali-agenda-item" data-focus-key="agenda-{{ $event['id'] }}">
                        <span class="wali-date-tile" aria-hidden="true"><small>{{ \Carbon\Carbon::parse($event['date'])->setTimezone(config('app.timezone'))->translatedFormat('M') }}</small><strong>{{ \Carbon\Carbon::parse($event['date'])->setTimezone(config('app.timezone'))->format('d') }}</strong></span>
                        <span><strong>{{ $event['title'] }}</strong><p>{{ \Carbon\Carbon::parse($event['date'])->setTimezone(config('app.timezone'))->translatedFormat('l, d F Y') }}</p><p>{{ \Carbon\Carbon::parse($event['time_start'])->format('H:i') }} – {{ \Carbon\Carbon::parse($event['time_end'])->format('H:i') }} WIB</p><p>{{ $event['location'] ?: 'Tempat belum diumumkan' }}</p></span>
                        <span class="material-symbols-rounded" aria-hidden="true">chevron_right</span>
                    </button>
                @empty
                    <div class="wali-panel wali-empty"><p>Tidak ada jadwal kajian pada kalender ini.</p></div>
                @endforelse
            </div>
        </section>
    </div>

    @if($showDayPicker)
        <x-wali.dialog id="wali-day-picker" :title="'Kajian pada ' . \Carbon\Carbon::parse($selectedDay)->translatedFormat('d F Y')" state="showDayPicker">
            <p class="wali-dialog-description">Ada beberapa kajian pada tanggal ini. Pilih kajian untuk melihat rincian.</p>
            <div class="wali-stack">@foreach($selectedDayEvents as $event)<button type="button" wire:key="day-event-{{ $event['id'] }}" wire:click="showDetail({{ $event['id'] }})" class="wali-button wali-button-secondary">{{ $event['title'] }}</button>@endforeach</div>
        </x-wali.dialog>
    @endif
    @if($showEventDetail && $selectedEvent)
        <x-wali.dialog id="wali-event-detail" :title="$selectedEvent->title" state="showEventDetail">
            @php
                $attendanceOpen = $selectedEvent->status === 'open' && $selectedEvent->date->toDateString() <= today()->toDateString();
                $statusLabel = $selectedEvent->status === 'closed' ? 'Presensi ditutup' : ($attendanceOpen ? 'Presensi dibuka' : 'Presensi belum dibuka');
            @endphp
            <p class="wali-chip {{ $attendanceOpen ? 'wali-chip-success' : '' }}" style="margin-bottom:20px">{{ $statusLabel }}</p>
            <x-wali.event-details :event="$selectedEvent" />
            @if($selectedEvent->description)<div class="wali-attendance"><h3>Keterangan</h3><p class="wali-muted">{{ $selectedEvent->description }}</p></div>@endif
            <div class="wali-dialog-actions"><button type="button" @click="requestClose()" class="wali-button wali-button-secondary">Tutup Detail</button></div>
        </x-wali.dialog>
    @endif
</div>
