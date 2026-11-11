@extends('layouts.gudang')

@section('title', 'Riwayat Mutasi Stok - RetailSmart Gudang')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--slate-900); letter-spacing: -0.02em;">Log Mutasi & Riwayat Stok</h1>
        <p style="font-size: 0.875rem; color: var(--slate-500); margin-top: 0.15rem;">Rekam jejak setiap perubahan stok fisik akibat pasokan masuk distributor atau koreksi opname.</p>
    </div>

    <div>
        <a href="{{ route('gudang.index') }}" class="btn btn-outline">
            ← Kembali ke Master Barang
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem;">
    <form method="GET" action="{{ route('gudang.mutations') }}" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px;">
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                class="form-control" 
                placeholder="Cari berdasarkan nama barang, barcode, atau alasan..."
            >
        </div>

        <div style="display: flex; gap: 0.35rem; align-items: center;">
            <a href="{{ route('gudang.mutations', ['type' => 'all', 'search' => $search]) }}" class="btn btn-sm {{ $type === 'all' ? 'btn-primary' : 'btn-outline' }}">
                Semua Mutasi
            </a>
            <a href="{{ route('gudang.mutations', ['type' => 'INCOMING', 'search' => $search]) }}" class="btn btn-sm {{ $type === 'INCOMING' ? 'btn-amber' : 'btn-outline' }}">
                Pasokan Masuk (Supplier)
            </a>
            <a href="{{ route('gudang.mutations', ['type' => 'ADJUSTMENT', 'search' => $search]) }}" class="btn btn-sm {{ $type === 'ADJUSTMENT' ? 'btn-primary' : 'btn-outline' }}">
                Koreksi Stok Opname
            </a>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(!empty($search) || $type !== 'all')
            <a href="{{ route('gudang.mutations') }}" class="btn btn-sm btn-outline" title="Reset">Reset</a>
        @endif
    </form>
</div>

<!-- Mutations Table -->
<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Waktu Transaksi</th>
                <th>Nama Produk</th>
                <th>Tipe Mutasi</th>
                <th>Stok Sebelum</th>
                <th>Perubahan Qty</th>
                <th>Stok Akhir</th>
                <th>Keterangan / Alasan</th>
                <th>Staff Gudang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $m)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--slate-800);">{{ $m->created_at->format('d/m/Y') }}</div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); font-family: var(--font-mono);">{{ $m->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--slate-900);">{{ $m->product->name ?? 'Produk Dihapus' }}</div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); font-family: var(--font-mono);">{{ $m->product->barcode ?? '-' }}</div>
                    </td>
                    <td>
                        @if($m->type === 'INCOMING')
                            <span class="badge badge-amber">Pasokan Masuk</span>
                        @else
                            <span class="badge badge-slate">Koreksi Stok</span>
                        @endif
                    </td>
                    <td style="font-family: var(--font-mono); color: var(--slate-600);">
                        {{ $m->quantity_before }} pcs
                    </td>
                    <td>
                        @if($m->quantity_change > 0)
                            <strong style="color: var(--emerald-600); font-family: var(--font-mono);">+{{ $m->quantity_change }} pcs</strong>
                        @elseif($m->quantity_change < 0)
                            <strong style="color: var(--rose-600); font-family: var(--font-mono);">{{ $m->quantity_change }} pcs</strong>
                        @else
                            <span style="color: var(--slate-500); font-family: var(--font-mono);">0 pcs</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color: var(--slate-900); font-family: var(--font-mono);">
                            {{ $m->quantity_after }} pcs
                        </strong>
                    </td>
                    <td>
                        <span style="font-size: 0.85rem; color: var(--slate-700);">
                            {{ $m->reason ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-slate">
                            {{ $m->user->name ?? 'Staff Gudang' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                        Belum ada riwayat mutasi stok yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.25rem;">
    {{ $mutations->links() }}
</div>
@endsection
