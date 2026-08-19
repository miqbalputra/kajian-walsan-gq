@props(['classStat', 'detailed' => false])

@php
    $statusBadgeClasses = [
        'hadir_fisik' => 'bg-emerald-100 text-emerald-700',
        'hadir_online' => 'bg-blue-100 text-blue-700',
        'izin' => 'bg-yellow-100 text-yellow-700',
        'alpha' => 'bg-red-100 text-red-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'rejected' => 'bg-rose-100 text-rose-700',
        'missing' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<article class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
    <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-6 py-5">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary-700">Rekap per kelas</p>
            <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ $classStat['name'] }}</h3>
        </div>
        <div class="rounded-2xl bg-primary-100 px-4 py-2 text-right">
            <p class="text-[11px] font-bold uppercase tracking-wide text-primary-700">Wali unik</p>
            <p class="text-xl font-extrabold text-primary-800">{{ $classStat['summary']['total'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 divide-x divide-slate-100">
        @foreach(['father' => 'Bapak', 'mother' => 'Ibu'] as $guardianKey => $guardianLabel)
            @php($breakdown = $classStat['guardians'][$guardianKey])
            <section class="p-5">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <h4 class="font-bold text-slate-900">{{ $guardianLabel }}</h4>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                        {{ $breakdown['total'] }} wali unik
                    </span>
                </div>

                <div class="space-y-2.5">
                    @foreach([
                        'hadir_fisik' => ['Hadir Langsung', 'bg-emerald-500', 'text-emerald-700'],
                        'hadir_online' => ['Menyimak Online', 'bg-blue-500', 'text-blue-700'],
                        'izin' => ['Izin', 'bg-amber-400', 'text-amber-700'],
                        'alpha' => ['Alfa', 'bg-red-500', 'text-red-700'],
                    ] as $status => [$label, $dotClass, $textClass])
                        <div class="flex items-center justify-between gap-2 text-sm">
                            <span class="flex min-w-0 items-center gap-2 text-slate-600">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $dotClass }}"></span>
                                <span class="truncate">{{ $label }}</span>
                            </span>
                            <span class="shrink-0 font-extrabold {{ $textClass }}">
                                {{ $breakdown['counts'][$status] }} <span class="text-xs font-bold">({{ number_format($breakdown['percentages'][$status], 1, ',', '.') }}%)</span>
                            </span>
                        </div>
                    @endforeach
                </div>

                @if($breakdown['counts']['perlu_validasi'] > 0)
                    <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-800">
                        {{ $breakdown['counts']['perlu_validasi'] }} perlu validasi
                        @if($breakdown['counts']['pending'] > 0 || $breakdown['counts']['rejected'] > 0)
                            <span class="font-normal">
                                ({{ $breakdown['counts']['pending'] }} pending, {{ $breakdown['counts']['rejected'] }} ditolak)
                            </span>
                        @endif
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</article>

@if($detailed)
    <article class="mt-5 overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary-700">Detail peserta didik</p>
                    <h3 class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">Daftar anak dan presensi wali</h3>
                </div>
                <p class="text-sm font-semibold text-slate-500">{{ count($classStat['students'] ?? []) }} anak · {{ $classStat['summary']['total'] }} wali unik</p>
            </div>
        </div>

        @if(count($classStat['students'] ?? []))
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left text-sm">
                    <thead class="bg-white text-xs font-black uppercase tracking-[0.08em] text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap border-b border-slate-200 px-6 py-3">No</th>
                            <th class="whitespace-nowrap border-b border-slate-200 px-4 py-3">Nama Anak</th>
                            <th class="whitespace-nowrap border-b border-slate-200 px-4 py-3">NIS</th>
                            <th class="min-w-[220px] whitespace-nowrap border-b border-slate-200 px-4 py-3">Bapak</th>
                            <th class="min-w-[220px] whitespace-nowrap border-b border-slate-200 px-6 py-3">Ibu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classStat['students'] as $index => $student)
                            <tr class="align-top odd:bg-white even:bg-slate-50/60">
                                <td class="border-b border-slate-100 px-6 py-4 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="border-b border-slate-100 px-4 py-4 font-bold text-slate-900">{{ $student['name'] ?: '-' }}</td>
                                <td class="border-b border-slate-100 px-4 py-4 font-mono text-xs font-semibold text-slate-600">{{ $student['nis'] ?: '-' }}</td>
                                @foreach(['father', 'mother'] as $parentType)
                                    @php($parent = $student['parents'][$parentType] ?? null)
                                    <td class="border-b border-slate-100 px-{{ $parentType === 'mother' ? '6' : '4' }} py-4">
                                        @if($parent && filled($parent['name']))
                                            <p class="font-semibold text-slate-800">{{ $parent['name'] }}</p>
                                            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusBadgeClasses[$parent['status'] ?? 'missing'] ?? $statusBadgeClasses['missing'] }}">{{ $parent['label'] }}</span>
                                        @else
                                            <p class="font-semibold text-slate-400">Belum terdaftar</p>
                                            <span class="mt-1 inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-8 text-center text-sm text-slate-500">Detail anak belum tersedia pada snapshot kajian ini.</div>
        @endif
    </article>
@endif
