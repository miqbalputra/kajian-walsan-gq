<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 85.6mm 53.98mm landscape;
            margin: 0;
        }

        body {
            width: 85.6mm;
            height: 53.98mm;
            font-family: Arial, Helvetica, sans-serif;
            overflow: hidden;
        }

        .card {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }

        /* Header */
        .card-header {
            height: 8.5mm;
            background: {{ $isMother ? 'linear-gradient(to right, #f43f5e, #ec4899)' : 'linear-gradient(to right, #059669, #14b8a6)' }};
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 3mm;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5mm;
        }

        .header-icon {
            width: 4mm;
            height: 4mm;
        }

        .header-title {
            color: #ffffff;
            font-size: 2.8mm;
            font-weight: 900;
            letter-spacing: 0.3mm;
            text-transform: uppercase;
        }

        .header-badge {
            color: rgba(255,255,255,0.85);
            font-size: 2mm;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2mm;
        }

        /* Card Body */
        .card-body {
            display: flex;
            height: calc(53.98mm - 8.5mm - 2mm);
            padding: 2.5mm 3mm;
            gap: 3mm;
        }

        .card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .field-label {
            font-size: 1.7mm;
            font-weight: 700;
            color: {{ $isMother ? '#e11d48' : '#0d9488' }};
            text-transform: uppercase;
            letter-spacing: 0.5mm;
            line-height: 1;
            margin-bottom: 0.5mm;
        }

        .field-value {
            font-size: 2.8mm;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .field-value-sm {
            font-size: 2mm;
            font-weight: 700;
            color: #334155;
            line-height: 1.2;
        }

        .student-item {
            font-size: 2mm;
            font-weight: 500;
            color: #334155;
            line-height: 1.3;
        }

        .student-nis {
            font-size: 1.6mm;
            color: #94a3b8;
        }

        /* QR Column */
        .card-qr {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5mm;
        }

        .qr-box {
            width: 18mm;
            height: 18mm;
            background: #ffffff;
            border: 0.5mm solid {{ $isMother ? '#fecdd3' : '#ccfbf1' }};
            border-radius: 1.5mm;
            padding: 0.8mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
        }

        .qr-string {
            font-size: 1.4mm;
            color: #94a3b8;
            letter-spacing: 0.1mm;
            text-align: center;
        }

        .valid-badge {
            display: flex;
            align-items: center;
            gap: 0.8mm;
            padding: 0.5mm 2mm;
            background: {{ $isMother ? '#fff1f2' : '#f0fdf4' }};
            border: 0.3mm solid {{ $isMother ? '#fecdd3' : '#bbf7d0' }};
            border-radius: 10mm;
        }

        .valid-dot {
            width: 2.5mm;
            height: 2.5mm;
            background: {{ $isMother ? '#f43f5e' : '#059669' }};
            border-radius: 50%;
        }

        .valid-text {
            font-size: 1.5mm;
            font-weight: 700;
            color: {{ $isMother ? '#be123c' : '#047857' }};
            text-transform: uppercase;
            letter-spacing: 0.3mm;
        }

        /* Footer */
        .card-footer-text {
            position: absolute;
            bottom: 2.2mm;
            right: 3mm;
            font-size: 1.4mm;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.2mm;
            text-align: right;
        }

        .card-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 0.8mm;
            background: {{ $isMother ? 'linear-gradient(to right, #f43f5e, #ec4899, #f43f5e)' : 'linear-gradient(to right, #059669, #14b8a6, #059669)' }};
        }
    </style>
</head>
<body>
    <div class="card">

        {{-- Header --}}
        <div class="card-header">
            <div class="header-left">
                {{-- Mosque icon as SVG --}}
                <svg class="header-icon" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.5 2.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .41-.17.79-.44 1.06L12 7.5l2.94-3.94A1.5 1.5 0 0114.5 2.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .47-.22.88-.56 1.15L19 6H5L7.06 3.65A1.5 1.5 0 016.5 2.5zM3 7v1h18V7H3zm1 2v10h3v-6h10v6h3V9H4zm5 4v6h6v-6H9z"/>
                </svg>
                <span class="header-title">Kajian Walsan</span>
            </div>
            <span class="header-badge">ID Wali Santri</span>
        </div>

        {{-- Body --}}
        <div class="card-body">
            {{-- Left: Info --}}
            <div class="card-info">
                <div>
                    <div class="field-label">Nama Lengkap</div>
                    <div class="field-value">{{ strtoupper($parent->user?->name ?? '') }}</div>
                </div>

                <div>
                    <div class="field-label">Peran</div>
                    <div class="field-value-sm">{{ strtoupper($parent->type_display ?? '') }}</div>
                </div>

                <div>
                    <div class="field-label">Wali Dari / NIS</div>
                    @foreach($parent->students->take(3) as $student)
                        <div class="student-item">• {{ $student->name }} <span class="student-nis">({{ $student->nis }})</span></div>
                    @endforeach
                </div>
            </div>

            {{-- Right: QR Code --}}
            <div class="card-qr">
                <div class="qr-box">
                    <img src="{{ $qrDataUrl }}" alt="QR Code" />
                </div>
                <div class="qr-string">{{ $parent->qr_code_string }}</div>
                <div class="valid-badge">
                    <div class="valid-dot"></div>
                    <span class="valid-text">Valid</span>
                </div>
            </div>
        </div>

        {{-- Footer text --}}
        <div class="card-footer-text">
            Kelompok Tahfidz Griya Qur'an "Tunas Ilmu"
        </div>

        {{-- Accent bar --}}
        <div class="card-accent"></div>

    </div>
</body>
</html>
