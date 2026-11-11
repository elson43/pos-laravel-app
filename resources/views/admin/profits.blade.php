@extends('layouts.admin')

@section('title', 'Laporan Laba Kotor - RetailSmart Owner')

@section('content')
<div style="margin-bottom: 1rem;">
    <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--slate-900);">Laporan Laba Kotor</h1>
    <p style="font-size: 0.775rem; color: var(--slate-500);">Analisis margin keuntungan kotor (Revenue − HPP) per produk.</p>
</div>

<!-- Scrollable Period Filter Pills -->
<div class="period-pill-scroll">
    <a href="{{ route('admin.profits', ['period' => 'today']) }}" class="period-pill {{ $period === 'today' ? 'active' : '' }}">
        Hari Ini
    </a>
    <a href="{{ route('admin.profits', ['period' => 'yesterday']) }}" class="period-pill {{ $period === 'yesterday' ? 'active' : '' }}">
        Kemarin
    </a>
    <a href="{{ route('admin.profits', ['period' => '7days']) }}" class="period-pill {{ $period === '7days' ? 'active' : '' }}">
        7 Hari
    </a>
    <a href="{{ route('admin.profits', ['period' => '30days']) }}" class="period-pill {{ $period === '30days' ? 'active' : '' }}">
        30 Hari
    </a>
    <a href="{{ route('admin.profits', ['period' => 'this_month']) }}" class="period-pill {{ $period === 'this_month' ? 'active' : '' }}">
        Bulan Ini
    </a>
</div>

<!-- Grand Total Profit Metric Card -->
<div class="card" style="margin-bottom: 1rem; padding: 1.15rem; background: var(--slate-900); color: var(--white); border: none;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <div style="font-size: 0.725rem; font-weight: 700; color: var(--emerald-400); text-transform: uppercase;">
                TOTAL LABA KOTOR (GROSS PROFIT)
            </div>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--white); margin-top: 0.25rem;">
                Rp {{ number_format($grandProfit, 0, ',', '.') }}
            </div>
        </div>

        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--emerald-500); border-radius: var(--radius-md); padding: 0.35rem 0.65rem; text-align: center;">
            <div style="font-size: 0.65rem; color: var(--emerald-400); font-weight: 700;">MARGIN</div>
            <div style="font-size: 1.15rem; font-weight: 900; color: var(--emerald-400);">{{ $grandMargin }}%</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--slate-800); font-size: 0.775rem;">
        <div>
            <span style="color: var(--slate-400);">Total Omset:</span>
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--slate-100); margin-top: 2px;">
                Rp {{ number_format($grandRevenue, 0, ',', '.') }}
            </div>
        </div>
        <div>
            <span style="color: var(--slate-400);">Total Modal (HPP):</span>
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--slate-100); margin-top: 2px;">
                Rp {{ number_format($grandCost, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>

<!-- Product Profit Ranking List -->
<div style="display: flex; flex-direction: column; gap: 0.65rem;">
    <div style="font-weight: 800; font-size: 0.85rem; color: var(--slate-900); padding: 0 0.25rem;">
        Rincian Margin per Produk Terjual:
    </div>

    @forelse($productProfits as $p)
        @php
            $marginPct = $p->total_revenue > 0 ? round(($p->gross_profit / $p->total_revenue) * 100, 1) : 0;
        @endphp
        <div class="card" style="padding: 0.85rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--slate-900);">
                        {{ $p->product->name ?? 'Produk Dihapus' }}
                    </div>
                    <div style="font-size: 0.7rem; color: var(--slate-500); font-family: var(--font-mono);">
                        Terjual: <strong>{{ $p->units_sold }} pcs</strong>
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-weight: 900; font-size: 0.95rem; color: var(--emerald-700);">
                        +Rp {{ number_format($p->gross_profit, 0, ',', '.') }}
                    </div>
                    <span class="badge badge-emerald" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                        Margin {{ $marginPct }}%
                    </span>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 0.725rem; color: var(--slate-600); background: var(--slate-50); border-radius: var(--radius-sm); padding: 0.4rem 0.6rem; border: 1px solid var(--slate-200);">
                <span>Omset: Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</span>
                <span>HPP: Rp {{ number_format($p->total_cost, 0, ',', '.') }}</span>
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; color: var(--slate-400); padding: 2.5rem 1rem; font-size: 0.85rem;">
            Tidak ada transaksi di periode ini.
        </div>
    @endforelse
</div>

<div style="margin-top: 1rem;">
    {{ $productProfits->links() }}
</div>
@endsection
