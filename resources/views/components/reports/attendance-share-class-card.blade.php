@props(['classStat'])

<article class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
    <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-6 py-5">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary-700">Rekap per kelas</p>
            <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ $classStat['name'] }}</h3>
        </div>
        <div class="rounded-2xl bg-primary-100 px-4 py-2 text-right">
            <p class="text-[11px] font-bold uppercase tracking-wide text-primary-700">Sasaran</p>
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
                        {{ $breakdown['total'] }} sasaran
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
