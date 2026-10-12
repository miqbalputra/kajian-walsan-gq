@props(['parentData', 'qrCodeSvg'])
@php
            $isMother = $parentData->type === 'mother';
            $bgHeader = $isMother ? 'from-rose-500 to-pink-500' : 'from-emerald-600 to-teal-500';
            $textColor = $isMother ? 'text-rose-600' : 'text-teal-600';
            $borderColor = $isMother ? 'border-rose-100' : 'border-teal-100';
            $accentBar = $isMother ? 'from-rose-500 via-pink-400 to-rose-500' : 'from-emerald-600 via-teal-400 to-emerald-600';
            $badgeBg = $isMother ? 'bg-rose-50 border-rose-100' : 'bg-emerald-50 border-emerald-100';
            $badgeText = $isMother ? 'text-rose-700' : 'text-emerald-700';
        @endphp

<div class="wali-card-preview" x-data="waliCardPreview()" :style="{ '--card-scale': scale }"><div class="wali-card-frame"><div id="id-card-element-ws"
                            class="wali-card-paper relative overflow-hidden rounded-xl shadow-2xl bg-white keep-white border border-gray-200"
                            style="width: 323.4px; height: 204px; min-width: 323.4px; min-height: 204px; font-family: 'Inter', sans-serif;">

                            {{-- Header Bar --}}
                            <div class="h-8 bg-gradient-to-r {{ $bgHeader }} flex items-center px-4 justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-rounded text-white text-base">mosque</span>
                                    <p class="text-white font-black text-[9px] tracking-tight uppercase">Presensi Wali Santri</p>
                                </div>
                                <p class="text-white/80 font-bold text-[7px] uppercase tracking-tighter">ID Wali Santri</p>
                            </div>

                            {{-- Background Pattern --}}
                            <div class="absolute inset-0 top-8 pointer-events-none opacity-[0.03]"
                                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M10 0 L20 10 L10 20 L0 10 Z\' fill=\'none\' stroke=\'black\' stroke-width=\'0.5\'/%3E%3C/svg%3E');">
                            </div>

                            {{-- Card Body --}}
                            <div class="p-4 flex gap-4 h-[calc(100%-32px)]">
                                {{-- Left: Data Info --}}
                                <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                                    <div class="space-y-0.5">
                                        <p class="text-[6px] font-bold {{ $textColor }} uppercase tracking-widest leading-none">
                                            Nama Lengkap</p>
                                        <p class="text-[10px] font-black text-slate-800 leading-tight uppercase truncate" id="card-ws-name">
                                            {{ $parentData->user?->name }}
                                        </p>
                                    </div>

                                    <div class="flex gap-4">
                                        <div>
                                            <p class="text-[6px] font-bold {{ $textColor }} uppercase tracking-widest leading-none">
                                                Peran</p>
                                            <p class="text-[8px] font-bold text-slate-700 leading-none">
                                                {{ strtoupper($parentData->type_display) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-0.5 min-h-0 overflow-hidden">
                                        <p class="text-[6px] font-bold {{ $textColor }} uppercase tracking-widest leading-none">
                                            Wali Dari / NIS</p>
                                        <div class="space-y-0.5">
                                            @foreach($parentData->students->take(3) as $student)
                                                <p class="text-[8px] font-medium text-slate-700 truncate leading-none mt-0.5">•
                                                    {{ $student->name }} <span
                                                        class="text-slate-400 text-[6px]">({{ $student->nis }})</span>
                                                </p>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: QR Code --}}
                                <div class="flex-shrink-0 flex flex-col items-center justify-center">
                                    <div
                                        class="w-20 h-20 bg-white border {{ $borderColor }} rounded-lg p-1 shadow-sm flex items-center justify-center overflow-hidden qrcode-svg-container-ws">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                    <p class="text-[5px] font-mono text-slate-400 mt-1.5 tracking-tighter">
                                        {{ $parentData->qr_code_string }}
                                    </p>

                                    <div
                                        class="mt-2 flex items-center gap-1 px-1.5 py-0.5 {{ $badgeBg }} rounded-full border">
                                        <span
                                            class="material-symbols-rounded {{ $isMother ? 'text-rose-600' : 'text-emerald-600' }} text-[8px]">verified</span>
                                        <span class="text-[5px] font-bold {{ $badgeText }} uppercase">Valid</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Text --}}
                            <div class="absolute bottom-1.5 left-0 right-4 text-right">
                                <p class="text-[5px] font-bold text-slate-400 opacity-60 uppercase tracking-tighter">
                                    Kelompok Tahfidz Griya Qur'an "Tunas Ilmu"
                                </p>
                            </div>

                            {{-- Footer Accent --}}
                            <div
                                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r {{ $accentBar }}">
                            </div>
                        </div></div></div>
