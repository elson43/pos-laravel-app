@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi Kasir - RetailSmart')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; width: 100%;">
    <!-- Top Summary & Date Filter -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--slate-900);">Riwayat Transaksi Kasir</h1>
            <p style="font-size: 0.85rem; color: var(--slate-500);">Daftar transaksi penjualan yang Anda tangani pada tanggal terpilih.</p>
        </div>

        <form method="GET" action="{{ route('kasir.history') }}" style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="date" name="date" value="{{ $date }}" class="form-control" style="width: auto; padding: 0.45rem 0.75rem;">
            <button type="submit" class="btn btn-primary btn-sm">Filter Tanggal</button>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--slate-500);">TOTAL PENJUALAN SHIFT</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: var(--emerald-600); margin-top: 0.25rem;">
                Rp {{ number_format($totalSales, 0, ',', '.') }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--slate-500);">JUMLAH TRANSAKSI</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: var(--slate-900); margin-top: 0.25rem;">
                {{ $transactionCount }} Transaksi
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Faktur (Invoice)</th>
                    <th>Waktu</th>
                    <th>Jumlah Item</th>
                    <th>Total Belanja</th>
                    <th>Metode Bayar</th>
                    <th>Uang Bayar</th>
                    <th>Kembalian</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td>
                            <span style="font-family: var(--font-mono); font-weight: 700; color: var(--slate-900);">
                                {{ $trx->invoice_number }}
                            </span>
                        </td>
                        <td>{{ $trx->created_at->format('H:i:s') }}</td>
                        <td>
                            <span class="badge badge-slate">
                                {{ $trx->details->sum('quantity') }} pcs
                            </span>
                        </td>
                        <td>
                            <strong style="color: var(--emerald-700);">
                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge badge-emerald">
                                {{ $trx->payment_method }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($trx->payment_amount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($trx->change_amount, 0, ',', '.') }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('kasir.receipt', $trx->invoice_number) }}" target="_blank" class="btn btn-sm btn-outline" title="Cetak Ulang Struk">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                Cetak Struk
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                            Belum ada transaksi yang tercatat pada tanggal {{ $date }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.25rem;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
