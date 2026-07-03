<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Presensi Wali Santri</title>
    <style>
        /* Konfigurasi Halaman */
        @page {
            margin: 1cm;
            size: A4 landscape;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 9pt;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        /* Tipografi & Warna */
        .text-emerald {
            color: #10B981;
        }

        .text-slate {
            color: #64748b;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Header */
        .header-section {
            width: 100%;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .brand-title {
            font-size: 20pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }

        /* Summary Cards - Grid using Table */
        .stats-table {
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px 0;
        }

        .stats-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
        }

        .stats-label {
            font-size: 7pt;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        .stats-value {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
        }

        /* Filter Box */
        .filter-panel {
            background-color: #f0fdfa;
            border: 1px solid #ccfbf1;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .filter-table {
            width: 100%;
            font-size: 8pt;
        }

        /* Table Utama */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }

        .main-table th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            font-size: 8pt;
            text-transform: uppercase;
            border: 1px solid #1e293b;
        }

        .main-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .main-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Status Badge - Stable Version */
        .status-box {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            display: block;
            width: 60px;
            margin: 0 auto;
        }

        .bg-hadir {
            background-color: #dcfce7;
            color: #166534;
        }

        .bg-online {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .bg-izin {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .bg-alpha {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Width Definitions */
        .col-no {
            width: 30px;
        }

        .col-date {
            width: 80px;
        }

        .col-study {
            width: 180px;
        }

        .col-parent {
            width: 140px;
        }

        .col-type {
            width: 50px;
        }

        .col-child {
            width: 140px;
        }

        .col-class {
            width: 60px;
        }

        .col-status {
            width: 80px;
        }

        /* Signature Footer */
        .footer-section {
            margin-top: 30px;
            width: 100%;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #334155;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <table class="header-section">
        <tr>
            <td>
                <h1 class="brand-title">KAJIAN WALSAN</h1>
                <div class="text-slate">Laporan Kehadiran Wali Santri</div>
            </td>
            <td class="text-right" style="vertical-align: top;">
                <div class="font-bold text-emerald" style="font-size: 14pt;">LAPORAN PRESENSI</div>
                <div class="text-slate" style="font-size: 8pt;">Tgl Cetak: {{ $generatedAt }}</div>
            </td>
        </tr>
    </table>

    <!-- Dashboard Cards -->
    @php
        $totalData = $attendances->count();
        $totalHadir = $attendances->whereIn('status', ['hadir_fisik', 'hadir_online'])->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
    @endphp

    <table class="stats-table">
        <tr>
            <td width="25%">
                <div class="stats-card">
                    <span class="stats-label">Total Kehadiran</span>
                    <div class="stats-value">{{ $totalData }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="stats-card">
                    <span class="stats-label">Hadir (Fisik/OL)</span>
                    <div class="stats-value" style="color: #10B981;">{{ $totalHadir }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="stats-card">
                    <span class="stats-label">Izin / Sakit</span>
                    <div class="stats-value" style="color: #f59e0b;">{{ $totalIzin }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="stats-card">
                    <span class="stats-label">Alpha</span>
                    <div class="stats-value" style="color: #ef4444;">{{ $totalAlpha }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Filters -->
    <div class="filter-panel">
        <table class="filter-table">
            <tr>
                <td width="25%"><strong>Tahun:</strong> {{ $filters['academicYear'] }}</td>
                <td width="25%"><strong>Kajian:</strong> {{ $filters['kajian'] }}</td>
                <td width="25%"><strong>Kelas:</strong> {{ $filters['class'] }}</td>
                <td width="25%"><strong>Filter Status:</strong> {{ $filters['status'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th class="col-no text-center">No</th>
                <th class="col-date">Tanggal</th>
                <th class="col-study">Judul Kajian</th>
                <th class="col-parent">Nama Orang Tua</th>
                <th class="col-type text-center">Tipe</th>
                <th class="col-child">Anak</th>
                <th class="col-class text-center">Kelas</th>
                <th class="col-status text-center">Status</th>
                <th class="text-center" width="80">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $attendance)
                <tr>
                    <td class="text-center font-bold text-slate">{{ $index + 1 }}</td>
                    <td>{{ $attendance->kajianEvent?->date?->format('d/m/Y') }}</td>
                    <td>
                        <div class="font-bold">{{ Str::limit($attendance->kajianEvent?->title, 40) }}</div>
                        <div style="font-size: 7pt; color: #94a3b8;">{{ $attendance->kajianEvent?->academicYear?->name }}
                        </div>
                    </td>
                    <td>
                        <div class="font-bold">{{ Str::limit($attendance->parent?->user?->name, 25) }}</div>
                    </td>
                    <td class="text-center">{{ $attendance->parent?->type === 'father' ? 'Ayah' : 'Ibu' }}</td>
                    <td>{{ Str::limit($attendance->parent?->students->first()?->name ?? '-', 25) }}</td>
                    <td class="text-center">{{ $attendance->parent?->students->first()?->classRoom?->name ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = '';
                            $statusLabel = '';
                            if ($attendance->status === 'hadir_fisik') {
                                $statusClass = 'bg-hadir';
                                $statusLabel = 'HADIR';
                            } elseif ($attendance->status === 'hadir_online') {
                                $statusClass = 'bg-online';
                                $statusLabel = 'ONLINE';
                            } elseif ($attendance->status === 'izin') {
                                $statusClass = 'bg-izin';
                                $statusLabel = 'IZIN';
                            } else {
                                $statusClass = 'bg-alpha';
                                $statusLabel = 'ALPHA';
                            }
                        @endphp
                        <span class="status-box {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="text-center text-slate" style="font-size: 8pt;">
                        @if($attendance->method === 'scan_qr') Scan QR
                        @elseif($attendance->method === 'manual') Manual
                        @else Upload @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Digital Footer -->
    <table class="footer-section">
        <tr>
            <td width="70%" style="font-size: 8pt; color: #94a3b8;">
                &copy; 2026 Presensi Wali Santri - Laporan ini dihasilkan secara otomatis oleh sistem.
            </td>
            <td width="30%">
                <div class="signature-box" style="float: right;">
                    <div class="text-slate" style="font-size: 8pt;">Mengetahui,</div>
                    <div class="signature-line">
                        <strong>Administrator</strong>
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>