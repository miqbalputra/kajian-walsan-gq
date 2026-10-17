@php
    $parentRows = $displayRows->where('student_id', $student->id)->where('parent_type', $parentType);
    $names = $parentRows->pluck('parent_name')->unique()->filter();
    if ($names->isEmpty()) $names = $student->parents->where('type', $parentType)->pluck('user.name')->filter();
@endphp
<div class="wc-guardian-cell">
    <span class="wc-mobile-label">{{ $parentType === 'father' ? 'Ayah' : 'Ibu' }}</span>
    @if($names->isEmpty())
        <span class="wc-muted">Belum terdaftar</span>
    @else
        <strong>{{ $names->join(' / ') }}</strong>
        @if($parentRows->isEmpty())
            <x-wali-kelas.status status="not_started" :label="$events->isEmpty() ? 'Belum ada kajian' : ($kajianId === '' ? 'Belum ada riwayat periode ini' : 'Tidak menjadi sasaran')" />
        @elseif($kajianId === '')
            @php
                $parentSummary = app(\App\Services\WaliKelasReportService::class)->summary($parentRows);
            @endphp
            <span class="wc-muted">{{ $parentSummary['present'] }} / {{ $parentSummary['total'] }} kehadiran tercatat</span>
            @if($parentSummary['incomplete'])<small class="wc-muted">{{ $parentSummary['incomplete'] }} riwayat belum lengkap</small>@endif
        @else
            @foreach($parentRows->unique('parent_id') as $parentRow)
                <x-wali-kelas.status :status="$parentRow['status']" :label="$parentRow['label']" />
            @endforeach
        @endif
    @endif
</div>
