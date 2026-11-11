@extends('layouts.admin')

@section('title', 'Log Aktivitas Toko - RetailSmart Owner')

@section('content')
<div style="margin-bottom: 1rem;">
    <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--slate-900);">Log Aktivitas Toko</h1>
    <p style="font-size: 0.775rem; color: var(--slate-500);">Pemantauan audit trail & linimasa operasional toko secara real-time.</p>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1rem; padding: 0.85rem;">
    <form method="GET" action="{{ route('admin.activities') }}" style="display: flex; flex-direction: column; gap: 0.5rem;">
        <div style="display: flex; gap: 0.5rem;">
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                class="form-control" 
                style="padding: 0.45rem 0.65rem; font-size: 0.8rem;" 
                placeholder="Cari aktivitas atau nama staff..."
            >
            <button type="submit" class="btn btn-sm btn-primary" style="padding: 0.45rem 0.85rem;">Cari</button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <select name="role" class="form-control" style="padding: 0.4rem 0.5rem; font-size: 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Peran</option>
                <option value="UKasir" {{ $roleFilter === 'UKasir' ? 'selected' : '' }}>Kasir (UKasir)</option>
                <option value="UGudang" {{ $roleFilter === 'UGudang' ? 'selected' : '' }}>Gudang (UGudang)</option>
                <option value="UAdmin" {{ $roleFilter === 'UAdmin' ? 'selected' : '' }}>Admin (UAdmin)</option>
            </select>

            <select name="action" class="form-control" style="padding: 0.4rem 0.5rem; font-size: 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Tipe Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ $actionFilter === $act ? 'selected' : '' }}>
                        {{ $act }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(!empty($search) || !empty($roleFilter) || !empty($actionFilter))
            <div style="text-align: right;">
                <a href="{{ route('admin.activities') }}" style="font-size: 0.725rem; color: var(--rose-600); font-weight: 600;">✕ Reset Filter</a>
            </div>
        @endif
    </form>
</div>

<!-- Timeline Activity Feed -->
<div style="display: flex; flex-direction: column; gap: 0.75rem;">
    @forelse($logs as $log)
        @php
            $roleBadgeClass = match($log->user_role) {
                'UKasir' => 'badge-emerald',
                'UGudang' => 'badge-amber',
                'UAdmin' => 'badge-slate',
                default => 'badge-slate',
            };

            $actionBorderColor = match($log->action) {
                'TRANSACTION_CREATED' => 'var(--emerald-500)',
                'STOCK_INCOMING' => 'var(--amber-500)',
                'STOCK_ADJUSTMENT' => 'var(--orange-500)',
                'LOGIN', 'LOGOUT' => 'var(--slate-400)',
                'PRODUCT_CREATED', 'PRODUCT_UPDATED', 'PRODUCT_DELETED' => 'var(--slate-700)',
                default => 'var(--slate-300)',
            };
        @endphp
        <div class="card" style="padding: 0.85rem; border-left: 4px solid {{ $actionBorderColor }};">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <span class="badge {{ $roleBadgeClass }}" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">
                        {{ $log->user_role }}
                    </span>
                    <strong style="font-size: 0.8rem; color: var(--slate-900);">{{ $log->user_name }}</strong>
                </div>

                <span style="font-size: 0.675rem; color: var(--slate-400); font-family: var(--font-mono);">
                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                </span>
            </div>

            <div style="font-size: 0.8rem; color: var(--slate-800); font-weight: 500; line-height: 1.4;">
                {{ $log->description }}
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.45rem; padding-top: 0.35rem; border-top: 1px solid var(--slate-100); font-size: 0.675rem; color: var(--slate-400);">
                <span style="font-family: var(--font-mono);">AKSI: {{ $log->action }}</span>
                <span>IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; color: var(--slate-400); padding: 3rem 1rem; font-size: 0.85rem;">
            Tidak ada log aktivitas yang cocok dengan filter.
        </div>
    @endforelse
</div>

<div style="margin-top: 1rem;">
    {{ $logs->links() }}
</div>
@endsection
