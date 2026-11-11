@extends('layouts.admin')

@section('title', 'Ringkasan Eksekutif - RetailSmart Owner')

@section('styles')
<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.15rem;
    }
    .kpi-card {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        padding: 0.95rem 0.85rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kpi-title {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .kpi-value {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-900);
        margin: 0.25rem 0 0.15rem;
        line-height: 1.2;
    }
    .kpi-sub {
        font-size: 0.7rem;
        font-weight: 600;
    }
    .chart-container {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        padding: 1rem;
        margin-bottom: 1.15rem;
        box-shadow: var(--shadow-sm);
    }
    .chart-bars {
        display: flex;
        align-items: flex-end;
        height: 140px;
        gap: 0.35rem;
        padding-top: 1.5rem;
        border-bottom: 1px solid var(--slate-200);
    }
    .chart-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        justify-content: flex-end;
        position: relative;
    }
    .chart-bar-fill {
        width: 100%;
        max-width: 18px;
        background: var(--slate-800);
        border-radius: 3px 3px 0 0;
        transition: height 0.3s ease;
        position: relative;
    }
    .chart-bar-fill.profit {
        background: var(--emerald-500);
    }
    .chart-label {
        font-size: 0.65rem;
        color: var(--slate-500);
        margin-top: 0.4rem;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<!-- Scrollable Period Filter Pills -->
<div class="period-pill-scroll">
    <a href="{{ route('admin.dashboard', ['period' => 'today']) }}" class="period-pill {{ $period === 'today' ? 'active' : '' }}">
        Hari Ini
    </a>
    <a href="{{ route('admin.dashboard', ['period' => 'yesterday']) }}" class="period-pill {{ $period === 'yesterday' ? 'active' : '' }}">
        Kemarin
    </a>
    <a href="{{ route('admin.dashboard', ['period' => '7days']) }}" class="period-pill {{ $period === '7days' ? 'active' : '' }}">
        7 Hari Terakhir
    </a>
    <a href="{{ route('admin.dashboard', ['period' => '30days']) }}" class="period-pill {{ $period === '30days' ? 'active' : '' }}">
        30 Hari
    </a>
    <a href="{{ route('admin.dashboard', ['period' => 'this_month']) }}" class="period-pill {{ $period === 'this_month' ? 'active' : '' }}">
        Bulan Ini
    </a>
</div>

<!-- KPI Summary Cards -->
<div class="kpi-grid">
    <!-- Omset Penjualan -->
    <div class="kpi-card" style="border-top: 3px solid var(--slate-900);">
        <div class="kpi-title">Omset Penjualan</div>
        <div class="kpi-value" style="color: var(--slate-900);">
            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
        </div>
        <div class="kpi-sub" style="color: var(--slate-500);">
            {{ $totalTransactions }} Transaksi
        </div>
    </div>

    <!-- Laba Kotor (Gross Profit) -->
    <div class="kpi-card" style="border-top: 3px solid var(--emerald-500);">
        <div class="kpi-title">Laba Kotor (Gross)</div>
        <div class="kpi-value" style="color: var(--emerald-600);">
            Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}
        </div>
        <div class="kpi-sub" style="color: var(--emerald-700);">
            Margin Laba: {{ $profitMargin }}%
        </div>
    </div>

    <!-- Total Item Terjual -->
    <div class="kpi-card">
        <div class="kpi-title">Volume Penjualan</div>
        <div class="kpi-value" style="font-size: 1.15rem;">
            {{ number_format($totalItemsSold, 0, ',', '.') }} pcs
        </div>
        <div class="kpi-sub" style="color: var(--slate-500);">
            HPP: Rp {{ number_format($totalHpp, 0, ',', '.') }}
        </div>
    </div>

    <!-- Peringatan Stok Kritis -->
    <div class="kpi-card" style="border-top: 3px solid var(--amber-500);">
        <div class="kpi-title">Stok Kritis Gudang</div>
        <div class="kpi-value" style="color: var(--amber-600); font-size: 1.15rem;">
            {{ $lowStockCount }} Produk
        </div>
        <div class="kpi-sub" style="color: var(--amber-700);">
            Sisa &lt; 10 unit fisik
        </div>
    </div>
</div>

<!-- Visual Sales & Profit Chart -->
@php
    $maxVal = max(array_merge($chartRevenues, [1]));
@endphp
<div class="chart-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <div>
            <div style="font-weight: 800; font-size: 0.85rem; color: var(--slate-900);">Tren Penjualan Harian</div>
            <div style="font-size: 0.7rem; color: var(--slate-500);">Omset (Hitam) & Laba Bersih (Hijau Emerald)</div>
        </div>
        <div style="display: flex; gap: 0.4rem; font-size: 0.675rem; font-weight: 700;">
            <span style="display: flex; align-items: center; gap: 0.2rem; color: var(--slate-700);">
                <span style="width: 8px; height: 8px; background: var(--slate-900); border-radius: 2px;"></span> Omset
            </span>
            <span style="display: flex; align-items: center; gap: 0.2rem; color: var(--emerald-700);">
                <span style="width: 8px; height: 8px; background: var(--emerald-500); border-radius: 2px;"></span> Laba
            </span>
        </div>
    </div>

    <div class="chart-bars">
        @foreach($chartLabels as $idx => $label)
            @php
                $revH = $maxVal > 0 ? ($chartRevenues[$idx] / $maxVal) * 100 : 0;
                $profH = $maxVal > 0 ? ($chartProfits[$idx] / $maxVal) * 100 : 0;
            @endphp
            <div class="chart-col">
                <div style="display: flex; gap: 2px; align-items: flex-end; height: 100%; width: 100%; justify-content: center;">
                    <div class="chart-bar-fill" style="height: {{ max(4, $revH) }}%;" title="Omset: Rp {{ number_format($chartRevenues[$idx], 0, ',', '.') }}"></div>
                    <div class="chart-bar-fill profit" style="height: {{ max(4, $profH) }}%;" title="Laba: Rp {{ number_format($chartProfits[$idx], 0, ',', '.') }}"></div>
                </div>
                <div class="chart-label">{{ $label }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Top Selling Products -->
<div class="card" style="margin-bottom: 1.15rem; padding: 1rem;">
    <div style="font-weight: 800; font-size: 0.875rem; color: var(--slate-900); margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
        <span>5 Produk Terlaris</span>
        <span style="font-size: 0.725rem; font-weight: 600; color: var(--slate-400);">Periode Terpilih</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.65rem;">
        @forelse($topProducts as $idx => $tp)
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.825rem; border-bottom: 1px solid var(--slate-100); padding-bottom: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                    <span style="font-weight: 800; color: var(--slate-400); width: 18px; font-size: 0.75rem;">#{{ $idx + 1 }}</span>
                    <div style="min-width: 0;">
                        <div style="font-weight: 700; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                            {{ $tp->product->name ?? 'Produk Dihapus' }}
                        </div>
                        <div style="font-size: 0.7rem; color: var(--slate-500);">
                            {{ $tp->total_sold }} pcs terjual
                        </div>
                    </div>
                </div>

                <div style="text-align: right; flex-shrink: 0;">
                    <div style="font-weight: 700; color: var(--emerald-700);">
                        Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 0.7rem; color: var(--emerald-600);">
                        Laba: Rp {{ number_format($tp->total_profit, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--slate-400); font-size: 0.8rem; padding: 1rem 0;">
                Belum ada transaksi di periode ini.
            </div>
        @endforelse
    </div>
</div>

<!-- Metode Pembayaran Breakdown -->
<div class="card" style="margin-bottom: 1.15rem; padding: 1rem;">
    <div style="font-weight: 800; font-size: 0.875rem; color: var(--slate-900); margin-bottom: 0.75rem;">
        Distribusi Metode Pembayaran
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.5rem;">
        @forelse($paymentMethods as $pm)
            <div style="background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-md); padding: 0.65rem 0.75rem;">
                <div style="font-weight: 700; font-size: 0.775rem; color: var(--slate-700);">{{ $pm->payment_method }}</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--slate-900); margin-top: 0.15rem;">
                    Rp {{ number_format($pm->total, 0, ',', '.') }}
                </div>
                <div style="font-size: 0.675rem; color: var(--slate-500);">{{ $pm->count }} transaksi</div>
            </div>
        @empty
            <div style="text-align: center; color: var(--slate-400); font-size: 0.8rem; padding: 1rem;">
                Tidak ada data pembayaran.
            </div>
        @endforelse
    </div>
</div>

<!-- Recent Transactions (Read-Only) -->
<div class="card" style="margin-bottom: 1.15rem; padding: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <span style="font-weight: 800; font-size: 0.875rem; color: var(--slate-900);">Transaksi Penjualan Terakhir</span>
        <a href="{{ route('admin.sales') }}" style="font-size: 0.75rem; color: var(--emerald-600); font-weight: 700;">Lihat Semua →</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        @forelse($recentTransactions as $t)
            <div style="background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 700; font-size: 0.825rem; font-family: var(--font-mono); color: var(--slate-900);">
                        {{ $t->invoice_number }}
                    </div>
                    <div style="font-size: 0.7rem; color: var(--slate-500);">
                        {{ $t->created_at->format('d M H:i') }} • Kasir: {{ $t->cashier->name ?? 'Kasir' }}
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-weight: 800; font-size: 0.875rem; color: var(--emerald-700);">
                        Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                    </div>
                    <span class="badge badge-slate" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                        {{ $t->payment_method }}
                    </span>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--slate-400); font-size: 0.8rem; padding: 1rem;">
                Belum ada transaksi tercatat.
            </div>
        @endforelse
    </div>
</div>

<!-- Recent Store Activity (Read-Only) -->
<div class="card" style="padding: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <span style="font-weight: 800; font-size: 0.875rem; color: var(--slate-900);">Log Aktivitas Toko Terkini</span>
        <a href="{{ route('admin.activities') }}" style="font-size: 0.75rem; color: var(--emerald-600); font-weight: 700;">Semua Log →</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        @forelse($recentLogs as $log)
            <div style="border-left: 2px solid var(--slate-300); padding-left: 0.65rem; font-size: 0.775rem;">
                <div style="display: flex; justify-content: space-between; color: var(--slate-500); font-size: 0.675rem;">
                    <span><strong>{{ $log->user_name }}</strong> ({{ $log->user_role }})</span>
                    <span>{{ $log->created_at->diffForHumans() }}</span>
                </div>
                <div style="color: var(--slate-800); margin-top: 0.15rem; font-weight: 500;">
                    {{ $log->description }}
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--slate-400); font-size: 0.8rem; padding: 1rem;">
                Belum ada aktivitas.
            </div>
        @endforelse
    </div>
</div>
@endsection
