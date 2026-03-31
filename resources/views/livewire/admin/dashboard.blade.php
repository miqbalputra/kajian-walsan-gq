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

        <!-- Top Classes Leaderboard -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 text-lg">🏆 Top Kelas</h3>
                <p class="text-sm text-gray-500">Berdasarkan kehadiran</p>
            </div>
            <div class="space-y-4">
                @forelse($topClasses as $index => $class)
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                                                            {{ $index === 0 ? 'bg-gradient-to-br from-yellow-400 to-amber-500 text-white' :
                    ($index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400 text-white' :
                        ($index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-500 text-white' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-medium text-gray-800 text-sm">{{ $class['name'] }}</span>
                                        <span
                                            class="text-sm font-bold {{ $class['percentage'] >= 70 ? 'text-green-600' : ($class['percentage'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $class['percentage'] }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-500
                                                                                    {{ $class['percentage'] >= 70 ? 'bg-gradient-to-r from-green-400 to-green-500' :
                    ($class['percentage'] >= 50 ? 'bg-gradient-to-r from-yellow-400 to-yellow-500' : 'bg-gradient-to-r from-red-400 to-red-500') }}"
                                            style="width: {{ $class['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-rounded text-4xl">leaderboard</span>
                        <p class="mt-2 text-sm">Belum ada data</p>
                    </div>
                @endforelse
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