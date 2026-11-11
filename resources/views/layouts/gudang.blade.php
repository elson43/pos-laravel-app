<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manajemen Inventaris Gudang - RetailSmart')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .gudang-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--slate-100);
        }
        .gudang-header {
            background-color: var(--slate-900);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--amber-500);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .gudang-nav-link {
            padding: 0.5rem 0.95rem;
            border-radius: var(--radius-md);
            color: var(--slate-300);
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.15s ease;
        }
        .gudang-nav-link:hover, .gudang-nav-link.active {
            background-color: var(--slate-800);
            color: var(--amber-400);
        }
        .gudang-main {
            flex: 1;
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="gudang-container">
        <!-- Top Navbar -->
        <header class="gudang-header">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="brand-badge">
                    <div class="brand-icon" style="background: var(--amber-500);">G</div>
                    <span>RetailSmart <span style="font-weight: 400; color: var(--amber-400); font-size: 0.95rem;">Gudang</span></span>
                </div>
                <span class="role-pill role-gudang">UGudang</span>

                <nav style="display: flex; gap: 0.5rem; margin-left: 1rem;">
                    <a href="{{ route('gudang.index') }}" class="gudang-nav-link {{ request()->routeIs('gudang.index') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        Master Barang & Inventaris
                    </a>
                    <a href="{{ route('gudang.mutations') }}" class="gudang-nav-link {{ request()->routeIs('gudang.mutations') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                        Riwayat Mutasi Stok
                    </a>
                </nav>
            </div>

            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="text-align: right; font-size: 0.8rem; color: var(--slate-300);">
                    <div style="font-weight: 700; color: var(--white);">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.725rem; color: var(--amber-400);">Staff Inventaris</div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="border-color: var(--slate-700); color: var(--slate-300);" title="Keluar">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body -->
        <main class="gudang-main">
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

            @if ($errors->any())
                <div class="flash-alert flash-error">
                    <div>
                        <div style="font-weight: 700; margin-bottom: 0.25rem;">Periksa kembali input data Anda:</div>
                        <ul style="margin-left: 1.25rem; font-size: 0.85rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-weight:700;">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
