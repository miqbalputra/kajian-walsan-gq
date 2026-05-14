<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Identitas - {{ $parent->user?->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        /* ---- Hint box (hidden on print) ---- */
        .hint {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 28px;
            margin-bottom: 28px;
            text-align: center;
            max-width: 420px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
        }
        .hint h2 { font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .hint p  { font-size: 13px; color: #64748b; line-height: 1.5; }
        .hint .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            padding: 10px 22px;
            background: {{ $isMother ? 'linear-gradient(to right,#f43f5e,#ec4899)' : 'linear-gradient(to right,#059669,#14b8a6)' }};
            color: #fff;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 14px {{ $isMother ? 'rgba(244,63,94,.35)' : 'rgba(5,150,105,.35)' }};
        }

        /* ---- Card wrap ---- */
        .card-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ---- ID Card: exact same dimensions as modal ---- */
        .id-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,.22);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            width: 323.4px;
            height: 204px;
            min-width: 323.4px;
            min-height: 204px;
            font-family: 'Inter', sans-serif;
        }

        /* Header bar — exact gradient from modal */
        .card-header {
            height: 32px;                            /* h-8 */
            background: {{ $isMother
                ? 'linear-gradient(to right, #f43f5e, #ec4899)'   /* rose-500 → pink-500 */
                : 'linear-gradient(to right, #059669, #14b8a6)' }};/* emerald-600 → teal-500 */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }
        .header-left { display: flex; align-items: center; gap: 6px; }
        .header-icon {                                /* material-symbols-rounded text-base */
            font-family: 'Material Symbols Rounded';
            font-size: 16px;
            color: #ffffff;
            font-style: normal;
            line-height: 1;
            font-weight: 400;
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }
        .header-title {
            color: #ffffff;
            font-weight: 900;
            font-size: 9px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .header-badge {
            color: rgba(255,255,255,0.8);
            font-weight: 700;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Background diamond pattern — exact same SVG as modal */
        .card-pattern {
            position: absolute;
            inset: 0;
            top: 32px;
            pointer-events: none;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10 0 L20 10 L10 20 L0 10 Z' fill='none' stroke='black' stroke-width='0.5'/%3E%3C/svg%3E");
        }

        /* Card body — flex identical to modal (p-4 flex gap-4 h-[calc(100%-32px)]) */
        .card-body {
            padding: 16px;
            display: flex;
            gap: 16px;
            height: calc(204px - 32px);
        }

        /* Left info column */
        .card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-top: 4px;
            padding-bottom: 4px;
            min-width: 0;
        }
        .field-label {
            font-size: 6px;
            font-weight: 700;
            color: {{ $isMother ? '#e11d48' : '#0d9488' }};   /* text-rose-600 / text-teal-600 */
            text-transform: uppercase;
            letter-spacing: 1.2px;
            line-height: 1;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 10px;
            font-weight: 900;
            color: #1e293b;                 /* slate-800 */
            text-transform: uppercase;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .field-value-sm {
            font-size: 8px;
            font-weight: 700;
            color: #334155;                 /* slate-700 */
            line-height: 1;
        }
        .student-item {
            font-size: 8px;
            font-weight: 500;
            color: #334155;
            line-height: 1;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .student-nis { font-size: 6px; color: #94a3b8; }

        /* Right QR column */
        .card-qr {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-box {
            width: 80px;
            height: 80px;
            background: #fff;
            border: 1px solid {{ $isMother ? '#ffe4e6' : '#ccfbf1' }}; /* rose-100 / teal-100 */
            border-radius: 8px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .qr-box svg { width: 100% !important; height: 100% !important; display: block; }
        .qr-string {
            font-size: 5px;
            font-family: monospace;
            color: #94a3b8;
            margin-top: 6px;
            letter-spacing: 0.3px;
            text-align: center;
        }

        /* Valid badge — exact match to modal */
        .valid-badge {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 2px 6px;
            background: {{ $isMother ? '#fff1f2' : '#ecfdf5' }};      /* rose-50 / emerald-50 */
            border: 1px solid {{ $isMother ? '#ffe4e6' : '#d1fae5' }}; /* rose-100 / emerald-100 */
            border-radius: 9999px;
        }
        .valid-icon {
            font-family: 'Material Symbols Rounded';
            font-size: 8px;
            color: {{ $isMother ? '#e11d48' : '#059669' }};  /* rose-600 / emerald-600 */
            font-style: normal;
            line-height: 1;
            font-weight: 400;
        }
        .valid-text {
            font-size: 5px;
            font-weight: 700;
            color: {{ $isMother ? '#be123c' : '#047857' }};  /* rose-700 / emerald-700 */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Footer text */
        .card-footer-text {
            position: absolute;
            bottom: 6px;
            left: 0;
            right: 16px;
            text-align: right;
            font-size: 5px;
            font-weight: 700;
            color: #94a3b8;
            opacity: 0.6;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        /* Bottom accent bar — exact same gradient */
        .card-accent {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 4px;
            background: {{ $isMother
                ? 'linear-gradient(to right, #f43f5e, #f9a8d4, #f43f5e)'
                : 'linear-gradient(to right, #059669, #5eead4, #059669)' }};
        }

        /* ===== Print ===== */
        @media print {
            html,
            body {
                width: 90mm;
                height: 58mm;
                margin: 0;
                overflow: hidden;
            }
            body {
                background: white;
                padding: 2mm;
                min-height: unset;
                display: flex;
                align-items: center;
                justify-content: center;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .hint { display: none !important; }
            .card-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 85.6mm;
                height: 53.98mm;
            }
            .id-card {
                box-shadow: none;
                border-radius: 2mm;
                width: 85.6mm !important;
                height: 53.98mm !important;
                min-width: 85.6mm !important;
                min-height: 53.98mm !important;
                max-width: 85.6mm !important;
                max-height: 53.98mm !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: 90mm 58mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="hint">
        <h2>Kartu Identitas Wali Santri</h2>
        <p>Klik tombol di bawah, lalu pilih <strong>"Save as PDF"</strong> atau <strong>"Simpan sebagai PDF"</strong> pada dialog cetak untuk mengunduh kartu.</p>
        <button class="btn" onclick="window.print()">
            <span style="font-family:'Material Symbols Rounded';font-size:18px;font-style:normal;line-height:1;">print</span>
            Cetak / Download PDF
        </button>
    </div>

    <div class="card-wrap">
        <div class="id-card">

            {{-- Header bar --}}
            <div class="card-header">
                <div class="header-left">
                    <span class="header-icon">mosque</span>
                    <span class="header-title">Kajian Walsan</span>
                </div>
                <span class="header-badge">ID Wali Santri</span>
            </div>

            {{-- Background diamond pattern --}}
            <div class="card-pattern"></div>

            {{-- Body --}}
            <div class="card-body">
                {{-- Left: Info --}}
                <div class="card-info">
                    <div>
                        <p class="field-label">Nama Lengkap</p>
                        <p class="field-value">{{ strtoupper($parent->user?->name ?? '') }}</p>
                    </div>
                    <div>
                        <p class="field-label">Peran</p>
                        <p class="field-value-sm">{{ strtoupper($parent->type_display ?? '') }}</p>
                    </div>
                    <div>
                        <p class="field-label">Wali Dari / NIS</p>
                        @foreach($parent->students->take(3) as $student)
                            <p class="student-item">• {{ $student->name }} <span class="student-nis">({{ $student->nis }})</span></p>
                        @endforeach
                    </div>
                </div>

                {{-- Right: QR Code --}}
                <div class="card-qr">
                    <div class="qr-box">{!! $qrSvg !!}</div>
                    <p class="qr-string">{{ $parent->qr_code_string }}</p>
                    <div class="valid-badge">
                        <span class="valid-icon">verified</span>
                        <span class="valid-text">Valid</span>
                    </div>
                </div>
            </div>

            {{-- Footer text --}}
            <div class="card-footer-text">Kelompok Tahfidz Griya Qur'an "Tunas Ilmu"</div>

            {{-- Bottom accent --}}
            <div class="card-accent"></div>

        </div>
    </div>

    <script>
        // Auto-open print dialog after fonts have loaded
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 900);
        });
    </script>
</body>
</html>
