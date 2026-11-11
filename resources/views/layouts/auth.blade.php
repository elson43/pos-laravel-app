<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem POS & Inventaris Retail')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, var(--slate-850), var(--slate-950));
            padding: 1.5rem;
            color: var(--white);
        }
        .auth-card {
            width: 100%;
            max-width: 460px;
            background-color: var(--slate-900);
            border: 1px solid var(--slate-800);
            border-radius: var(--radius-xl);
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--emerald-500), var(--emerald-700));
            border-radius: var(--radius-lg);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--slate-950);
            margin-bottom: 1rem;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.35);
        }
        .auth-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.02em;
        }
        .auth-subtitle {
            font-size: 0.875rem;
            color: var(--slate-400);
            margin-top: 0.35rem;
        }
        .auth-card .form-control {
            background-color: var(--slate-850);
            border-color: var(--slate-700);
            color: var(--white);
        }
        .auth-card .form-control:focus {
            border-color: var(--emerald-500);
            background-color: var(--slate-800);
        }
        .auth-card .form-label {
            color: var(--slate-300);
        }
        .demo-credentials-box {
            background-color: var(--slate-850);
            border: 1px dashed var(--slate-700);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-top: 1.75rem;
            font-size: 0.8rem;
        }
        .demo-tag {
            cursor: pointer;
            padding: 0.35rem 0.65rem;
            border-radius: var(--radius-sm);
            font-size: 0.725rem;
            font-weight: 700;
            display: inline-block;
            margin: 0.2rem;
            transition: all 0.15s ease;
        }
        .demo-kasir { background: var(--emerald-500); color: var(--slate-950); }
        .demo-gudang { background: var(--amber-500); color: var(--slate-950); }
        .demo-admin { background: var(--slate-700); color: var(--emerald-400); }
        .demo-tag:hover { transform: translateY(-1px); filter: brightness(1.1); }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">R</div>
                <h1 class="auth-title">RetailSmart POS</h1>
                <p class="auth-subtitle">Sistem Terintegrasi Kasir, Gudang & Manajemen</p>
            </div>

            @if(session('success'))
                <div class="flash-alert flash-success" style="margin-bottom: 1.5rem;">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-alert flash-error" style="margin-bottom: 1.5rem;">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>
