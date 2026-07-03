<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <!-- Total Kajian -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-200">
                    <span class="material-symbols-rounded text-white text-3xl">event</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalKajian }}</p>
                    <p class="text-sm text-gray-500">Total Kajian</p>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <span class="material-symbols-rounded text-white text-3xl">school</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalSiswa }}</p>
                    <p class="text-sm text-gray-500">Total Siswa</p>
                </div>
            </div>
        </div>

        <!-- Total Wali Santri -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-violet-400 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-200">
                    <span class="material-symbols-rounded text-white text-3xl">group</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalWaliSantri }}</p>
                    <p class="text-sm text-gray-500">Wali Santri</p>
                </div>
            </div>
        </div>

        <!-- Pending Validasi (Actionable) -->
        <a href="{{ route('admin.validation.index') }}"
            class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-red-200 hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 {{ $pendingValidation > 0 ? 'bg-gradient-to-br from-red-400 to-red-600 shadow-lg shadow-red-200' : 'bg-gray-100' }} rounded-xl flex items-center justify-center transition-colors">
                    <span
                        class="material-symbols-rounded {{ $pendingValidation > 0 ? 'text-white' : 'text-gray-400' }} text-3xl">verified_user</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingValidation }}</p>
                    <p class="text-sm text-gray-500">Validasi Pending</p>
                </div>
            </div>
        </a>

        <!-- Kehadiran Event Terakhir -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-xl flex items-center justify-center shadow-lg
                    {{ $lastEventAttendance >= 70 ? 'bg-gradient-to-br from-green-400 to-green-600 shadow-green-200' : ($lastEventAttendance >= 50 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-yellow-200' : 'bg-gradient-to-br from-red-400 to-red-600 shadow-red-200') }}">
                    <span class="material-symbols-rounded text-white text-3xl">how_to_reg</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $lastEventAttendance }}%</p>
                    <p class="text-sm text-gray-500">Kehadiran Terakhir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <!-- Attendance Trend Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Tren Kehadiran</h3>
                    <p class="text-sm text-gray-500">Persentase per Sesi Kajian</p>
                </div>
            </div>
            <div id="attendanceTrendChart" class="w-full" style="height: 300px;"></div>
        </div>

        <!-- Attendance Status Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Distribusi Kehadiran</h3>
                    <p class="text-sm text-gray-500">Semua Waktu</p>
                </div>
            </div>
            <div id="attendanceStatusChart" class="w-full" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Monthly Trend & Class Performance -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <!-- Monthly Comparison -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Performa Bulanan {{ date('Y') }}</h3>
                    <p class="text-sm text-gray-500">Persentase kehadiran per bulan vs target 80%</p>
                </div>
            </div>
            <div id="monthlyComparisonChart" class="w-full" style="height: 280px;"></div>
        </div>

    </div>
    
    <!-- Follow-up & Insights Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <!-- Parents Needing Attention (Follow-up) -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-rose-50/30">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-rounded text-rose-500">priority_high</span>
                        Perlu Perhatian (Alpha {{ $parentsNeedingAttention['count'] > 1 ? 'Berturut-turut' : '' }})
                    </h3>
                    <p class="text-sm text-gray-500">Wali santri yang tidak hadir dalam {{ $parentsNeedingAttention['count'] }} kajian terakhir</p>
                </div>
                <span class="px-2.5 py-1 bg-rose-100 text-rose-700 text-xs font-black rounded-lg uppercase tracking-wider">High Priority</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($parentsNeedingAttention['data'] as $p)
                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
                                <span class="material-symbols-rounded text-gray-400">person</span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 truncate">{{ $p['user']['name'] }}</p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $p['students'][0]['name'] ?? '-' }} ({{ $p['students'][0]['class_room']['name'] ?? '-' }})
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @php
                                $phone = $p['user']['phone'] ?? '';
                                if($phone && !str_starts_with($phone, '62')) {
                                    $phone = '62' . ltrim($phone, '0');
                                }
                                $message = "Assalamu'alaikum Bapak/Ibu " . $p['user']['name'] . ", kami dari tim admin Presensi Wali Santri memperhatikan Bapak/Ibu sudah beberapa kali absen. Apakah ada kendala yang bisa kami bantu?";
                            @endphp
                            <a href="https://wa.me/{{ $phone }}?text={{ urlencode($message) }}" target="_blank"
                                class="p-2 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-colors"
                                title="Chat WhatsApp">
                                <span class="material-symbols-rounded">chat</span>
                            </a>
                            <button wire:click="openFollowUpModal({{ $p['id'] }})"
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-rounded text-sm">edit_note</span>
                                Catat Alasan
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400">
                        <span class="material-symbols-rounded text-5xl text-emerald-100">task_alt</span>
                        <p class="mt-2 font-medium">Semua wali santri aktif hadir!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Class Performance / Insights -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-symbols-rounded text-amber-500">insights</span>
                    Insight Per Kelas
                </h3>
                <p class="text-sm text-gray-500">Persentase kehadiran rata-rata</p>
            </div>
            <div class="space-y-4">
                @foreach(array_slice($topClasses, 0, 4) as $class)
                    <div>
                        <div class="flex items-center justify-between mb-1 text-sm">
                            <span class="font-medium text-gray-700">{{ $class['name'] }}</span>
                            <span class="font-bold {{ $class['percentage'] >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $class['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $class['percentage'] >= 80 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $class['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
                <div class="pt-4 mt-4 border-t border-gray-50">
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        Lihat Laporan Lengkap
                        <span class="material-symbols-rounded text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Events -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Kajian Terbaru</h3>
                    <a href="{{ route('admin.kajian.index') }}"
                        class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentEvents as $event)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-md">
                                <span class="material-symbols-rounded text-white">event</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500">{{ $event->date->translatedFormat('d M Y') }} •
                                    {{ $event->speaker ?? '-' }}
                                </p>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium 
                                                        {{ $event->status === 'open' ? 'bg-green-100 text-green-700' : ($event->status === 'closed' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <span class="material-symbols-rounded text-4xl text-gray-300">event_busy</span>
                        <p class="mt-2">Belum ada kajian</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Presensi Terbaru</h3>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentAttendance as $attendance)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-{{ $attendance->parent?->type === 'father' ? 'blue-100 dark:bg-blue-900/40' : 'pink-100 dark:bg-pink-900/40' }} rounded-full flex items-center justify-center transition-colors shadow-sm">
                                <span
                                    class="material-symbols-rounded text-{{ $attendance->parent?->type === 'father' ? 'blue-600 dark:text-blue-400' : 'pink-600 dark:text-pink-400' }} text-xl">
                                    {{ $attendance->parent?->type === 'father' ? 'man' : 'woman' }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $attendance->parent?->user?->name ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $attendance->kajianEvent?->title ?? '-' }}</p>
                            </div>
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-medium 
                                                        {{ $attendance->status === 'hadir_fisik' ? 'bg-green-100 text-green-700' : ($attendance->status === 'hadir_online' ? 'bg-blue-100 text-blue-700' : ($attendance->status === 'izin' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                                {{ $attendance->status_display }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <span class="material-symbols-rounded text-4xl text-gray-300">how_to_reg</span>
                        <p class="mt-2">Belum ada presensi</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Follow-up Modal -->
    @if($showFollowUpModal && $selectedParent)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="$set('showFollowUpModal', false)"></div>

                <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 z-10">
                    <div class="mb-6">
                        <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center mb-4">
                            <span class="material-symbols-rounded text-rose-600 text-3xl">edit_note</span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">Catat Alasan Alpha</h3>
                        <p class="text-sm text-gray-500 mt-1">Mencatat hasil follow-up untuk <strong>{{ $selectedParent->user->name }}</strong></p>
                    </div>

                    <form wire:submit="submitFollowUp">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Hasil Follow-up / Alasan Alpha</label>
                                <textarea wire:model="followUpReason" rows="3" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                    placeholder="Contoh: Sakit, Lupa, Ada acara keluarga, dll."></textarea>
                                @error('followUpReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" wire:click="$set('showFollowUpModal', false)"
                                class="flex-1 px-4 py-3 border border-gray-200 rounded-2xl font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                class="flex-1 px-4 py-3 bg-primary-600 text-white rounded-2xl font-bold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-200">
                                Simpan Alasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart Theme Colors
            const colors = {
                primary: '#10B981',
                secondary: '#3B82F6',
                warning: '#F59E0B',
                danger: '#EF4444',
                purple: '#8B5CF6',
                pink: '#EC4899'
            };

            // 1. Attendance Trend Chart (Stacked Area)
            const trendData = @json($attendanceTrendData);
            const trendOptions = {
                series: [
                    { name: 'Hadir Fisik', data: trendData.hadirFisik },
                    { name: 'Hadir Online', data: trendData.hadirOnline },
                    { name: 'Izin', data: trendData.izin },
                    { name: 'Alpha', data: trendData.alpha }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: true,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '50%',
                    }
                },
                colors: [colors.primary, colors.secondary, colors.warning, colors.danger],
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                fill: { opacity: 1 },
                xaxis: {
                    categories: trendData.labels,
                    labels: {
                        style: { colors: '#64748B', fontSize: '12px' }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748B', fontSize: '12px' },
                        formatter: val => Math.round(val)
                    }
                },
                grid: {
                    borderColor: '#E2E8F0',
                    strokeDashArray: 4,
                    padding: { left: 0, right: 0 }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: '#64748B' }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: 'light',
                    y: { formatter: val => val + ' orang' }
                }
            };

            if (document.getElementById('attendanceTrendChart')) {
                new ApexCharts(document.getElementById('attendanceTrendChart'), trendOptions).render();
            }

            // 2. Attendance Status Distribution (Donut)
            const statusData = @json($attendanceByStatus);
            const statusOptions = {
                series: [statusData.hadirFisik, statusData.hadirOnline, statusData.izin, statusData.alpha],
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Inter, sans-serif'
                },
                labels: ['Hadir Fisik', 'Hadir Online', 'Izin', 'Alpha'],
                colors: [colors.primary, colors.secondary, colors.warning, colors.danger],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { fontSize: '14px', color: '#64748B' },
                                value: { fontSize: '24px', fontWeight: 700, color: '#1E293B' },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '12px',
                                    color: '#64748B',
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    labels: { colors: '#64748B' }
                },
                stroke: { width: 0 },
                tooltip: {
                    y: { formatter: val => val + ' presensi' }
                }
            };

            if (document.getElementById('attendanceStatusChart')) {
                new ApexCharts(document.getElementById('attendanceStatusChart'), statusOptions).render();
            }

            // 3. Monthly Comparison Chart (Line + Target)
            const monthlyData = @json($monthlyComparison);
            const monthlyOptions = {
                series: [
                    { name: 'Kehadiran', data: monthlyData.attendance, type: 'column' },
                    { name: 'Target (80%)', data: monthlyData.target, type: 'line' }
                ],
                chart: {
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: [colors.primary, colors.danger],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '60%'
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth',
                    dashArray: [0, 5]
                },
                xaxis: {
                    categories: monthlyData.months,
                    labels: {
                        style: { colors: '#64748B', fontSize: '11px' }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    labels: {
                        style: { colors: '#64748B', fontSize: '12px' },
                        formatter: val => val + '%'
                    }
                },
                grid: {
                    borderColor: '#E2E8F0',
                    strokeDashArray: 4
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: '#64748B' }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: { formatter: val => val + '%' }
                }
            };

            if (document.getElementById('monthlyComparisonChart')) {
                new ApexCharts(document.getElementById('monthlyComparisonChart'), monthlyOptions).render();
            }
        });
    </script>
@endpush