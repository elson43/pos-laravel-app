<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->invoice_number }} - RetailSmart</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background-color: var(--slate-100);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .receipt-action-bar {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="receipt-action-bar no-print">
        <a href="{{ route('kasir.pos') }}" class="btn btn-outline">
            ← Kembali ke POS
        </a>
        <button type="button" class="btn btn-emerald" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Struk (Print)
        </button>
    </div>

    <!-- 58mm/80mm Thermal Receipt Layout -->
    <div class="thermal-receipt">
        <div class="receipt-header">
            <div class="store-name">TOKO RETAIL SMART</div>
            <div>Jl. Merdeka No. 45, Indonesia</div>
            <div>Telp: (021) 555-0199</div>
            <div class="receipt-divider"></div>
            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                <span>No: {{ $transaction->invoice_number }}</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div style="text-align: left; font-size: 11px;">
                Kasir: {{ $transaction->cashier->name ?? 'Kasir' }}
            </div>
        </div>

        <div style="margin: 8px 0;">
            @foreach($transaction->details as $detail)
                <div style="margin-bottom: 5px;">
                    <div style="display: flex; justify-content: space-between; font-weight: 600;">
                        <span>{{ $detail->product->name ?? 'Item' }}</span>
                        <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div style="font-size: 11px; color: #475569;">
                        {{ $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Total Belanja:</span>
                <span style="font-weight: 700;">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Metode Pembayaran:</span>
                <span>{{ $transaction->payment_method }}</span>
            </div>
            <div class="total-row">
                <span>Uang Diterima:</span>
                <span>Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-footer">
            <div>*** TERIMA KASIH ***</div>
            <div>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
            <div style="margin-top: 4px; font-size: 10px;">Layanan Konsumen: 0812-3456-7890</div>
        </div>
    </div>
</body>
</html>
