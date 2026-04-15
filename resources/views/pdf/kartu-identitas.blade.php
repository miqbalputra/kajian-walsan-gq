<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Identitas - {{ $parent->user?->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            padding: 20px;
        }

        .hint {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 24px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .hint h2 { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
        .hint p  { font-size: 13px; color: #64748b; line-height: 1.5; }
        .hint .badge {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 16px;
            background: {{ $isMother ? '#f43f5e' : '#059669' }};
            color: #fff;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        /* ===== KTP Card ===== */
        .card-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 323.4px;
            height: 204px;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            border: 1px solid #e2e8f0;
            font-family: 'Inter', Arial, sans-serif;
        }

        /* Header */
        .card-header {
            height: 32px;
            background: {{ $isMother ? 'linear-gradient(to right,#f43f5e,#ec4899)' : 'linear-gradient(to right,#059669,#14b8a6)' }};
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 12px;
        }
        .header-left { display: flex; align-items: center; gap: 5px; }
        .header-icon { width: 16px; height: 16px; fill: white; }
        .header-title { color: #fff; font-size: 9px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; }
        .header-badge { color: rgba(255,255,255,.8); font-size: 7px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Body */
        .card-body {
            display: flex;
            gap: 10px;
            padding: 14px 14px 14px 14px;
            height: calc(204px - 32px - 6px);
        }

        /* Left info column */
        .card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }
        .field-label {
            font-size: 6px;
            font-weight: 700;
            color: {{ $isMother ? '#e11d48' : '#0d9488' }};
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 10px;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .field-value-sm {
            font-size: 8px;
            font-weight: 700;
            color: #334155;
        }
        .student-item {
            font-size: 8px;
            font-weight: 500;
            color: #334155;
            line-height: 1.4;
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
            gap: 5px;
        }
        .qr-box {
            width: 76px;
            height: 76px;
            background: #fff;
            border: 1px solid {{ $isMother ? '#fecdd3' : '#ccfbf1' }};
            border-radius: 6px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .qr-box svg { width: 100% !important; height: 100% !important; display: block; }
        .qr-string { font-size: 5px; color: #94a3b8; letter-spacing: 0.2px; text-align: center; font-family: monospace; }
        .valid-badge {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 2px 6px;
            background: {{ $isMother ? '#fff1f2' : '#f0fdf4' }};
            border: 1px solid {{ $isMother ? '#fecdd3' : '#bbf7d0' }};
            border-radius: 20px;
        }
        .valid-icon { font-size: 8px; color: {{ $isMother ? '#f43f5e' : '#059669' }}; font-weight: 900; }
        .valid-text { font-size: 5px; font-weight: 700; color: {{ $isMother ? '#be123c' : '#047857' }}; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Footer */
        .card-footer-text {
            position: absolute;
            bottom: 7px;
            right: 14px;
            font-size: 5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            text-align: right;
            opacity: 0.7;
        }
        .card-accent {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: {{ $isMother ? 'linear-gradient(to right,#f43f5e,#ec4899,#f43f5e)' : 'linear-gradient(to right,#059669,#14b8a6,#059669)' }};
        }

        /* ===== Print styles ===== */
        @media print {
            body {
                background: white;
                padding: 0;
                display: block;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .hint { display: none; }
            .card-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
            }
            .card {
                box-shadow: none;
                border: none;
                border-radius: 0;
            }
            @page {
                size: 85.6mm 53.98mm landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="hint">
        <h2>Kartu Identitas Wali Santri</h2>
        <p>Klik tombol di bawah, lalu pilih <strong>"Save as PDF"</strong> atau <strong>"Simpan sebagai PDF"</strong> pada dialog cetak.</p>
        <span class="badge" onclick="window.print()">🖨️ &nbsp; Cetak / Download PDF</span>
    </div>

    <div class="card-wrap">
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <div class="header-left">
                    <svg class="header-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z"/>
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

                {{-- Right: QR --}}
                <div class="card-qr">
                    <div class="qr-box">{!! $qrSvg !!}</div>
                    <div class="qr-string">{{ $parent->qr_code_string }}</div>
                    <div class="valid-badge">
                        <span class="valid-icon">✓</span>
                        <span class="valid-text">Valid</span>
                    </div>
                </div>
            </div>

            <div class="card-footer-text">Kelompok Tahfidz Griya Qur'an "Tunas Ilmu"</div>
            <div class="card-accent"></div>
        </div>
    </div>

    <script>
        // Auto-trigger print after fonts load
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 800);
        });
    </script>
</body>
</html>
