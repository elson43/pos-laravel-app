<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Admin Mobile Portal - RetailSmart')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background-color: var(--slate-950);
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        /* Mobile Shell Frame */
        .admin-mobile-shell {
            width: 100%;
            max-width: 540px;
            min-height: 100vh;
            background-color: var(--slate-100);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.4);
            padding-bottom: 70px; /* Space for fixed bottom nav */
        }

        /* Mobile App Header */
        .mobile-app-header {
            background-color: var(--slate-900);
            color: var(--white);
            padding: 0.85rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 2px solid var(--emerald-500);
        }

        .admin-badge-strip {
            background-color: var(--slate-850);
            padding: 0.45rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.775rem;
            color: var(--slate-300);
            border-bottom: 1px solid var(--slate-800);
        }

        /* Mobile Bottom Navigation Bar */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 540px;
            background-color: var(--slate-900);
            border-top: 1px solid var(--slate-800);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            padding: 0.4rem 0.25rem 0.5rem;
            z-index: 60;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.25);
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--slate-400);
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            padding: 0.3rem 0.2rem;
            border-radius: var(--radius-md);
            transition: all 0.15s ease;
            gap: 0.2rem;
        }

        .mobile-nav-item.active {
            color: var(--emerald-400);
        }

        .mobile-nav-item.active svg {
            stroke: var(--emerald-400);
            transform: translateY(-1px);
        }

        .mobile-content {
            padding: 1.15rem;
            flex: 1;
        }

        .period-pill-scroll {
            display: flex;
            gap: 0.4rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            scrollbar-width: none;
        }
        .period-pill-scroll::-webkit-scrollbar {
            display: none;
        }

        .period-pill {
            padding: 0.35rem 0.85rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
            background-color: var(--white);
            color: var(--slate-700);
            border: 1px solid var(--slate-300);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .period-pill.active {
            background-color: var(--slate-900);
            color: var(--white);
            border-color: var(--slate-900);
        }

        .read-only-notice {
            background-color: var(--slate-800);
            border-left: 3px solid var(--amber-500);
            color: var(--slate-300);
            padding: 0.65rem 0.85rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="admin-mobile-shell">
        <!-- Top App Bar -->
        <header class="mobile-app-header">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div class="brand-icon" style="width: 28px; height: 28px; font-size: 0.85rem; background: var(--emerald-500);">A</div>
                <div>
                    <div style="font-weight: 800; font-size: 0.95rem; line-height: 1.1;">RetailSmart</div>
                    <div style="font-size: 0.675rem; color: var(--emerald-400); font-weight: 600;">PORTAL PEMILIK (OWNER)</div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="border-color: var(--slate-700); color: var(--slate-300); padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Read-Only Badge Strip -->
        <div class="admin-badge-strip">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span class="role-pill role-admin" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">UAdmin</span>
                <span style="font-weight: 600; color: var(--white);">{{ Auth::user()->name }}</span>
            </div>
            <span style="font-size: 0.7rem; color: var(--emerald-400); font-weight: 600;">
                ● Mode Monitoring Terenkripsi
            </span>
        </div>

        <!-- Content Area -->
        <main class="mobile-content">
            <!-- Strict Read-Only Notice -->
            <div class="read-only-notice">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--amber-400); flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><strong>Akses Read-Only:</strong> Seluruh laporan dan data bersifat pemantauan murni (View Only tanpa izin manipulasi/edit data).</span>
            </div>

            @if(session('success'))
                <div class="flash-alert flash-success" style="font-size: 0.8rem; padding: 0.65rem 1rem;">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-alert flash-error" style="font-size: 0.8rem; padding: 0.65rem 1rem;">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-nav">
            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Ringkasan</span>
            </a>

            <a href="{{ route('admin.sales') }}" class="mobile-nav-item {{ request()->routeIs('admin.sales') ? 'active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Penjualan</span>
            </a>

            <a href="{{ route('admin.profits') }}" class="mobile-nav-item {{ request()->routeIs('admin.profits') ? 'active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <span>Laba Kotor</span>
            </a>

            <a href="{{ route('admin.activities') }}" class="mobile-nav-item {{ request()->routeIs('admin.activities') ? 'active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Aktivitas</span>
            </a>
        </nav>
    </div>

    @yield('scripts')
</body>
</html>
