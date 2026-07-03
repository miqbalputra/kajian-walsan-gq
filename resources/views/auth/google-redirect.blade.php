<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Mengalihkan ke Google - Presensi Wali Santri</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #047857, #0f766e);
            color: #ffffff;
        }

        body {
            min-height: 100svh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .panel {
            width: min(100%, 360px);
            text-align: center;
        }

        .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 22px;
            border-radius: 24px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            box-shadow: 0 24px 50px rgba(0, 0, 0, .18);
        }

        .spinner {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 4px solid rgba(255, 255, 255, .35);
            border-top-color: #ffffff;
            animation: spin .8s linear infinite;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 850;
            letter-spacing: 0;
        }

        p {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .82);
            font-size: 14px;
            line-height: 1.55;
            font-weight: 600;
        }

        .fallback {
            display: inline-flex;
            margin-top: 22px;
            padding: 12px 16px;
            border-radius: 14px;
            background: #ffffff;
            color: #047857;
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <main class="panel">
        <div class="icon" aria-hidden="true">
            <div class="spinner"></div>
        </div>
        <h1>Mengalihkan ke Google</h1>
        <p>Silakan lanjutkan login memakai akun Google yang emailnya terdaftar di aplikasi.</p>
        <a class="fallback" href="{{ $redirectUrl }}" rel="nofollow">Lanjutkan</a>
    </main>

    <script>
        window.location.replace(@json($redirectUrl));
    </script>
</body>

</html>
