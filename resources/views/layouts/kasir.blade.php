<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Kasir - RetailSmart')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .kasir-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--slate-100);
        }
        .kasir-header {
            background-color: var(--slate-900);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--emerald-600);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .shortcut-bar {
            background-color: var(--slate-850);
            color: var(--slate-300);
            padding: 0.4rem 1.5rem;
            font-size: 0.775rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border-bottom: 1px solid var(--slate-800);
            overflow-x: auto;
        }
        .shortcut-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            white-space: nowrap;
        }
        .shortcut-key {
            background-color: var(--slate-700);
            color: var(--emerald-400);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.75rem;
            border: 1px solid var(--slate-600);
        }
        .kasir-main {
            flex: 1;
            padding: 1rem 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .kasir-nav-link {
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-md);
            color: var(--slate-300);
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.15s ease;
        }
        .kasir-nav-link:hover, .kasir-nav-link.active {
            background-color: var(--slate-800);
            color: var(--emerald-400);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="kasir-container">
        <!-- Top Navbar -->
        <header class="kasir-header">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div class="brand-badge">
                    <div class="brand-icon">K</div>
                    <span>RetailSmart <span style="font-weight: 400; color: var(--emerald-400); font-size: 0.95rem;">POS Kasir</span></span>
                </div>
                <span class="role-pill role-kasir">UKasir</span>

                <nav style="display: flex; gap: 0.5rem; margin-left: 1rem;">
                    <a href="{{ route('kasir.pos') }}" class="kasir-nav-link {{ request()->routeIs('kasir.pos') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Kasir POS
                    </a>
                    <a href="{{ route('kasir.history') }}" class="kasir-nav-link {{ request()->routeIs('kasir.history') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Riwayat Transaksi Hari Ini
                    </a>
                </nav>
            </div>

            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="text-align: right; font-size: 0.8rem; color: var(--slate-300);">
                    <div style="font-weight: 700; color: var(--white);">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.725rem; color: var(--emerald-400);">Online • Shift Kasir</div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="border-color: var(--slate-700); color: var(--slate-300);" title="Keluar">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Keyboard Shortcuts Bar -->
        <div class="shortcut-bar">
            <span style="font-weight: 700; color: var(--slate-400);">Shortcut Kasir:</span>
            <div class="shortcut-item"><span class="shortcut-key">F2</span> Fokus Scan Barcode</div>
            <div class="shortcut-item"><span class="shortcut-key">F4</span> Bersihkan Keranjang</div>
            <div class="shortcut-item"><span class="shortcut-key">F8</span> Ganti Metode Bayar</div>
            <div class="shortcut-item"><span class="shortcut-key">Enter / F9</span> Eksekusi Bayar</div>
            <div class="shortcut-item"><span class="shortcut-key">Esc</span> Tutup Jendela</div>
        </div>

        <!-- Main Body -->
        <main class="kasir-main">
            @if(session('success'))
                <div class="flash-alert flash-success">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-weight:700;">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-alert flash-error">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-weight:700;">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
