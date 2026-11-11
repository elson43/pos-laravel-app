@extends('layouts.admin')

@section('title', 'Laporan Penjualan (Read-Only) - RetailSmart Owner')

@section('content')
<div style="margin-bottom: 1rem;">
    <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--slate-900);">Laporan Penjualan</h1>
    <p style="font-size: 0.775rem; color: var(--slate-500);">Pemantauan transaksi penjualan secara terperinci (Read-Only).</p>
</div>

<!-- Scrollable Period Filter Pills -->
<div class="period-pill-scroll">
    <a href="{{ route('admin.sales', array_merge(request()->query(), ['period' => 'today'])) }}" class="period-pill {{ $period === 'today' ? 'active' : '' }}">
        Hari Ini
    </a>
    <a href="{{ route('admin.sales', array_merge(request()->query(), ['period' => 'yesterday'])) }}" class="period-pill {{ $period === 'yesterday' ? 'active' : '' }}">
        Kemarin
    </a>
    <a href="{{ route('admin.sales', array_merge(request()->query(), ['period' => '7days'])) }}" class="period-pill {{ $period === '7days' ? 'active' : '' }}">
        7 Hari
    </a>
    <a href="{{ route('admin.sales', array_merge(request()->query(), ['period' => '30days'])) }}" class="period-pill {{ $period === '30days' ? 'active' : '' }}">
        30 Hari
    </a>
    <a href="{{ route('admin.sales', array_merge(request()->query(), ['period' => 'this_month'])) }}" class="period-pill {{ $period === 'this_month' ? 'active' : '' }}">
        Bulan Ini
    </a>
</div>

<!-- Filter Dropdowns Card -->
<div class="card" style="margin-bottom: 1rem; padding: 0.85rem;">
    <form method="GET" action="{{ route('admin.sales') }}" style="display: flex; flex-direction: column; gap: 0.5rem;">
        <input type="hidden" name="period" value="{{ $period }}">

        <div style="display: flex; gap: 0.5rem;">
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                class="form-control" 
                style="padding: 0.45rem 0.65rem; font-size: 0.8rem;" 
                placeholder="Cari No. Faktur (TRX-...)"
            >
            <button type="submit" class="btn btn-sm btn-primary" style="padding: 0.45rem 0.85rem;">Filter</button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <select name="cashier_id" class="form-control" style="padding: 0.4rem 0.5rem; font-size: 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Kasir</option>
                @foreach($cashiers as $c)
                    <option value="{{ $c->id }}" {{ $cashierId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>

            <select name="payment_method" class="form-control" style="padding: 0.4rem 0.5rem; font-size: 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Metode</option>
                <option value="Cash" {{ $paymentMethod === 'Cash' ? 'selected' : '' }}>Cash (Tunai)</option>
                <option value="QRIS" {{ $paymentMethod === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                <option value="Debit Card" {{ $paymentMethod === 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
            </select>
        </div>
    </form>
</div>

<!-- Aggregated Summary Pill -->
<div style="background: var(--slate-900); color: var(--white); border-radius: var(--radius-lg); padding: 0.85rem 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <div style="font-size: 0.7rem; color: var(--slate-400); text-transform: uppercase;">Total Omset Tersaring</div>
        <div style="font-size: 1.25rem; font-weight: 900; color: var(--emerald-400); margin-top: 0.1rem;">
            Rp {{ number_format($filteredTotalRevenue, 0, ',', '.') }}
        </div>
    </div>
    <div style="text-align: right;">
        <div style="font-size: 0.7rem; color: var(--slate-400); text-transform: uppercase;">Volume Transaksi</div>
        <div style="font-size: 1.15rem; font-weight: 800; color: var(--white); margin-top: 0.1rem;">
            {{ $filteredTransactionCount }} Transaksi
        </div>
    </div>
</div>

<!-- Transaction Cards List -->
<div style="display: flex; flex-direction: column; gap: 0.65rem;">
    @forelse($transactions as $trx)
        <div class="card" style="padding: 0.85rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
                <div>
                    <span style="font-family: var(--font-mono); font-weight: 800; font-size: 0.825rem; color: var(--slate-900);">
                        {{ $trx->invoice_number }}
                    </span>
                    <div style="font-size: 0.7rem; color: var(--slate-500); margin-top: 2px;">
                        {{ $trx->created_at->format('d/m/Y H:i') }} • Kasir: {{ $trx->cashier->name ?? 'Kasir' }}
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-weight: 900; font-size: 0.95rem; color: var(--emerald-700);">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                    </div>
                    <span class="badge badge-emerald" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                        {{ $trx->payment_method }}
                    </span>
                </div>
            </div>

            <!-- Item details accordion / collapsible -->
            <div style="background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-md); padding: 0.6rem 0.75rem; margin-top: 0.5rem; font-size: 0.75rem;">
                <div style="font-weight: 700; color: var(--slate-700); margin-bottom: 0.35rem; display: flex; justify-content: space-between;">
                    <span>Rincian Item ({{ $trx->details->sum('quantity') }} pcs):</span>
                    <span style="color: var(--emerald-700);">Laba: Rp {{ number_format($trx->total_profit, 0, ',', '.') }}</span>
                </div>
                @foreach($trx->details as $d)
                    <div style="display: flex; justify-content: space-between; padding: 2px 0; border-top: 1px dashed var(--slate-200); font-size: 0.725rem;">
                        <span style="color: var(--slate-800); max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $d->product->name ?? 'Item' }} <span style="color: var(--slate-500); font-family: var(--font-mono);">× {{ $d->quantity }}</span>
                        </span>
                        <span style="font-family: var(--font-mono); font-weight: 600;">
                            Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; color: var(--slate-400); padding: 2.5rem 1rem; font-size: 0.85rem;">
            Tidak ada transaksi penjualan yang cocok dengan filter.
        </div>
    @endforelse
</div>

<div style="margin-top: 1rem;">
    {{ $transactions->links() }}
</div>
@endsection
