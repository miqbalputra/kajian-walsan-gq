<div class="space-y-6">
    <!-- Header & Statistics Dashboard -->
    <!-- Header & Statistics Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-rounded text-xl">forum</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Respon</p>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-amber-400">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Avg. Materi</p>
                <div class="flex items-center gap-2 mb-2">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['materiAvg'] }}</h4>
                    <span class="material-symbols-rounded text-amber-400 text-sm fill-1">star</span>
                </div>
                <!-- Smart Conclusion -->
                <div
                    class="flex items-center gap-1.5 px-2 py-1 bg-{{ $stats['materiConclusion']['color'] }}-50 dark:bg-{{ $stats['materiConclusion']['color'] }}-900/20 text-{{ $stats['materiConclusion']['color'] }}-600 dark:text-{{ $stats['materiConclusion']['color'] }}-400 rounded-lg">
                    <span class="material-symbols-rounded text-sm">{{ $stats['materiConclusion']['icon'] }}</span>
                    <span class="text-[10px] font-bold">{{ $stats['materiConclusion']['text'] }}</span>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 border-l-4 border-l-emerald-400">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Avg. Teknis</p>
                <div class="flex items-center gap-2 mb-2">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['operasionalAvg'] }}</h4>
                    <span class="material-symbols-rounded text-emerald-400 text-sm fill-1">star</span>
                </div>
                <!-- Smart Conclusion -->
                <div
                    class="flex items-center gap-1.5 px-2 py-1 bg-{{ $stats['operasionalConclusion']['color'] }}-50 dark:bg-{{ $stats['operasionalConclusion']['color'] }}-900/20 text-{{ $stats['operasionalConclusion']['color'] }}-600 dark:text-{{ $stats['operasionalConclusion']['color'] }}-400 rounded-lg">
                    <span class="material-symbols-rounded text-sm">{{ $stats['operasionalConclusion']['icon'] }}</span>
                    <span class="text-[10px] font-bold">{{ $stats['operasionalConclusion']['text'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between gap-1">
                @foreach([5, 4, 3, 2, 1] as $r)
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-full bg-gray-100 dark:bg-slate-800 h-8 rounded-sm relative overflow-hidden">
                            @php
                                $percent = $stats['total'] > 0 ? ($stats['ratingCounts'][$r] / $stats['total']) * 100 : 0;
                            @endphp
                            <div class="absolute bottom-0 w-full bg-primary-500 transition-all duration-500"
                                style="height: {{ $percent }}%"></div>
                        </div>
                        <div class="text-[8px] font-bold text-gray-400 mt-1">{{ $r }}⭐</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pilih Kajian</label>
                <select wire:model.live="filterEvent"
                    class="w-full bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Kajian</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->date->format('d M Y') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Rating</label>
                <select wire:model.live="filterRating"
                    class="w-full bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} Bintang</option>
                    @endfor
                </select>
            </div>

            <div class="w-44">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dari Tanggal</label>
                <input type="date" wire:model.live="filterDateStart"
                    class="w-full bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="w-44">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sampai Tanggal</label>
                <input type="date" wire:model.live="filterDateEnd"
                    class="w-full bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
            </div>

            <button
                wire:click="$set('filterEvent', ''); $set('filterRating', ''); $set('filterDateStart', ''); $set('filterDateEnd', '');"
                class="px-4 py-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                Reset
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Wali Santri</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Kajian</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Rating</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Komentar / Saran</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Waktu Isi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                    @forelse($feedbacks as $feedback)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-xs font-bold">
                                        {{ substr($feedback->user?->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $feedback->user?->name }}
                                        </p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ $feedback->user?->parentProfile?->type_display ?? 'Umum' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $feedback->kajianEvent?->title }}
                                </p>
                                <p class="text-[10px] text-gray-500">{{ $feedback->kajianEvent?->date->format('d M Y') }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">Materi</span>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span
                                                    class="material-symbols-rounded text-[10px] {{ ($feedback->extra_feedback['materi'] ?? 0) >= $i ? 'text-amber-400 fill-1' : 'text-gray-200 dark:text-slate-700' }}">star</span>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">Teknis</span>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span
                                                    class="material-symbols-rounded text-[10px] {{ ($feedback->extra_feedback['operasional'] ?? 0) >= $i ? 'text-emerald-400 fill-1' : 'text-gray-200 dark:text-slate-700' }}">star</span>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs xl:max-w-md">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 italic">
                                        {{ $feedback->comment ?: '-' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-500">{{ $feedback->created_at->diffForHumans() }}</p>
                                <p class="text-[10px] text-gray-400">{{ $feedback->created_at->format('d/m H:i') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                Belum ada data survey yang masuk sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50">
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>