@extends('layouts.kasir')

@section('title', 'POS Kasir Desktop - RetailSmart')

@section('styles')
<style>
    .pos-grid {
        display: grid;
        grid-template-columns: 1fr 440px;
        gap: 1.25rem;
        flex: 1;
        height: calc(100vh - 145px);
        min-height: 580px;
    }
    .pos-catalog-panel {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        height: 100%;
        overflow: hidden;
    }
    .pos-search-bar {
        display: flex;
        gap: 0.75rem;
        background: var(--white);
        padding: 0.75rem 1rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--slate-200);
        box-shadow: var(--shadow-sm);
    }
    .scanner-input-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
    }
    .scanner-icon {
        position: absolute;
        left: 0.85rem;
        color: var(--emerald-600);
    }
    .scanner-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        font-size: 1rem;
        font-family: var(--font-mono);
        font-weight: 600;
        border: 2px solid var(--slate-300);
        border-radius: var(--radius-md);
        outline: none;
        transition: all 0.15s ease;
        background-color: var(--slate-50);
    }
    .scanner-input:focus {
        border-color: var(--emerald-500);
        background-color: var(--white);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.85rem;
        overflow-y: auto;
        padding-right: 0.35rem;
        flex: 1;
    }
    .product-item-card {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        padding: 0.85rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.15s ease;
        user-select: none;
        position: relative;
    }
    .product-item-card:hover {
        border-color: var(--emerald-500);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .product-item-card.out-of-stock {
        opacity: 0.55;
        cursor: not-allowed;
        background-color: var(--slate-50);
    }
    .product-item-card.out-of-stock:hover {
        transform: none;
        border-color: var(--slate-300);
        box-shadow: none;
    }
    .pos-cart-panel {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    .cart-header {
        padding: 0.85rem 1.25rem;
        background: var(--slate-900);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cart-items-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .cart-item-row {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        padding: 0.65rem 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        transition: background-color 0.15s ease;
    }
    .cart-item-row:hover {
        background: #f8fafc;
        border-color: var(--slate-300);
    }
    .qty-control-btn {
        width: 28px;
        height: 28px;
        background: var(--slate-200);
        border: 1px solid var(--slate-300);
        border-radius: 4px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.1s ease;
    }
    .qty-control-btn:hover {
        background: var(--slate-300);
    }
    .cart-footer-section {
        background: var(--slate-50);
        border-top: 1px solid var(--slate-200);
        padding: 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .quick-cash-pill {
        background: var(--slate-200);
        border: 1px solid var(--slate-300);
        border-radius: var(--radius-sm);
        padding: 0.3rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        color: var(--slate-800);
        transition: all 0.12s ease;
    }
    .quick-cash-pill:hover {
        background: var(--slate-300);
        border-color: var(--slate-400);
    }
    .pay-method-btn {
        flex: 1;
        padding: 0.5rem 0.35rem;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: var(--radius-md);
        border: 1px solid var(--slate-300);
        background: var(--white);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .pay-method-btn.active {
        background: var(--slate-900);
        color: var(--white);
        border-color: var(--slate-900);
    }
</style>
@endsection

@section('content')
<div class="pos-grid">
    <!-- LEFT PANEL: Catalog, Barcode Search & Fast Scanner -->
    <div class="pos-catalog-panel">
        <!-- Search & Scanner Input Header -->
        <div class="pos-search-bar">
            <div class="scanner-input-wrapper">
                <svg class="scanner-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="5" height="5" rx="1"/>
                    <rect x="16" y="3" width="5" height="5" rx="1"/>
                    <rect x="3" y="16" width="5" height="5" rx="1"/>
                    <path d="M21 16v3a2 2 0 0 1-2 2h-3"/>
                    <line x1="12" y1="7" x2="12" y2="17"/>
                    <line x1="7" y1="12" x2="17" y2="12"/>
                </svg>
                <input 
                    type="text" 
                    id="barcodeInput" 
                    class="scanner-input" 
                    placeholder="Scan Barcode / Cari nama barang lalu tekan Enter (F2)..." 
                    autocomplete="off"
                    autofocus
                >
            </div>
            <button type="button" class="btn btn-emerald" onclick="handleManualSearch()">
                Cari / Scan
            </button>
            <button type="button" class="btn btn-outline" onclick="resetSearchFilter()" title="Reset pencarian">
                Semua
            </button>
        </div>

        <!-- Product Grid View -->
        <div class="product-grid" id="productGrid">
            @foreach($products as $product)
                <div 
                    class="product-item-card {{ $product->stock <= 0 ? 'out-of-stock' : '' }}" 
                    data-id="{{ $product->id }}"
                    data-barcode="{{ $product->barcode }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-stock="{{ $product->stock }}"
                    onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->barcode }}', {{ $product->price }}, {{ $product->stock }})"
                >
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.25rem; margin-bottom: 0.35rem;">
                            <span style="font-family: var(--font-mono); font-size: 0.725rem; color: var(--slate-500);">
                                {{ $product->barcode }}
                            </span>
                            @if($product->stock <= 0)
                                <span class="badge badge-rose">Habis</span>
                            @elseif($product->stock < 10)
                                <span class="badge badge-amber">Sisa {{ $product->stock }}</span>
                            @else
                                <span class="badge badge-emerald">Stok {{ $product->stock }}</span>
                            @endif
                        </div>
                        <div style="font-weight: 700; font-size: 0.875rem; color: var(--slate-900); line-height: 1.3; margin-bottom: 0.5rem;">
                            {{ $product->name }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--emerald-700);">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT PANEL: Current Cart & Payment Checkout -->
    <div class="pos-cart-panel">
        <div class="cart-header">
            <div>
                <div style="font-weight: 800; font-size: 1.05rem;">Keranjang Kasir</div>
                <div style="font-size: 0.75rem; color: var(--slate-300);">Kasir: {{ Auth::user()->name }}</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline" style="border-color: var(--slate-700); color: var(--slate-300);" onclick="clearCart()" title="Bersihkan Keranjang (F4)">
                Bersihkan (F4)
            </button>
        </div>

        <!-- Scrollable Items Area -->
        <div class="cart-items-scroll" id="cartItemsContainer">
            <div id="cartEmptyState" style="text-align: center; color: var(--slate-400); padding: 3rem 1rem;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 0.75rem; color: var(--slate-300);">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <div style="font-weight: 600; font-size: 0.95rem; color: var(--slate-600);">Keranjang Masih Kosong</div>
                <div style="font-size: 0.8rem; margin-top: 0.25rem;">Scan barcode atau klik produk di katalog sebelah kiri.</div>
            </div>
        </div>

        <!-- Cart Footer & Payment Calculation -->
        <div class="cart-footer-section">
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--slate-600);">
                <span>Total Item Terpilih:</span>
                <span id="totalItemsBadge" style="font-weight: 700; color: var(--slate-900);">0 item</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 1px dashed var(--slate-300); padding-bottom: 0.5rem;">
                <span style="font-size: 1.1rem; font-weight: 800; color: var(--slate-900);">Total Belanja:</span>
                <span id="grandTotalDisplay" style="font-size: 1.45rem; font-weight: 900; color: var(--emerald-600);">Rp 0</span>
            </div>

            <!-- Payment Method Selection -->
            <div>
                <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem;">Metode Pembayaran (F8):</label>
                <div style="display: flex; gap: 0.4rem;">
                    <button type="button" class="pay-method-btn active" id="btnMethodCash" onclick="setPaymentMethod('Cash')">
                        Tunai (Cash)
                    </button>
                    <button type="button" class="pay-method-btn" id="btnMethodQRIS" onclick="setPaymentMethod('QRIS')">
                        QRIS
                    </button>
                    <button type="button" class="pay-method-btn" id="btnMethodDebit" onclick="setPaymentMethod('Debit Card')">
                        Kartu Debit
                    </button>
                </div>
            </div>

            <!-- Cash Payment Nominal Section -->
            <div id="cashInputSection">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                    <label for="paymentAmountInput" class="form-label" style="font-size: 0.775rem; margin-bottom: 0;">Nominal Uang Diterima:</label>
                    <div style="display: flex; gap: 0.25rem;">
                        <span class="quick-cash-pill" onclick="quickCashExact()">Uang Pas</span>
                        <span class="quick-cash-pill" onclick="quickCashAdd(50000)">50k</span>
                        <span class="quick-cash-pill" onclick="quickCashAdd(100000)">100k</span>
                    </div>
                </div>
                <input 
                    type="number" 
                    id="paymentAmountInput" 
                    class="form-control" 
                    style="font-family: var(--font-mono); font-size: 1.1rem; font-weight: 700; padding: 0.5rem 0.75rem;" 
                    placeholder="0"
                    oninput="calculateChange()"
                >

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.45rem; font-size: 0.85rem;">
                    <span style="font-weight: 600; color: var(--slate-600);">Kembalian:</span>
                    <span id="changeDisplay" style="font-size: 1.05rem; font-weight: 800; font-family: var(--font-mono); color: var(--slate-800);">Rp 0</span>
                </div>
            </div>

            <!-- Checkout Action Button -->
            <button 
                type="button" 
                id="btnCheckout" 
                class="btn btn-emerald btn-lg" 
                style="width: 100%; font-size: 1.05rem; font-weight: 800; padding: 0.85rem;" 
                onclick="processCheckout()"
                disabled
            >
                BAYAR SEKARANG (F9)
            </button>
        </div>
    </div>
</div>

<!-- THERMAL RECEIPT PRINT MODAL -->
<div class="modal-overlay" id="receiptModal">
    <div class="modal-content" style="max-width: 380px; padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="font-weight: 800; font-size: 1rem; color: var(--slate-900);">Struk Transaksi Penjualan</div>
            <button type="button" class="btn btn-sm btn-outline" onclick="closeReceiptModal()" style="border-radius: var(--radius-full); width: 28px; height: 28px; padding: 0;">✕</button>
        </div>

        <!-- Renderable Thermal Area -->
        <div id="receiptPrintArea">
            <div class="thermal-receipt" id="thermalReceiptContent">
                <!-- Dynamically generated receipt -->
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem; margin-top: 1.25rem;">
            <button type="button" class="btn btn-primary" style="flex: 1;" onclick="printReceipt()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak Struk (Print)
            </button>
            <button type="button" class="btn btn-outline" onclick="closeReceiptModal()">
                Transaksi Baru (Esc)
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Global In-Memory Cart State
    let cart = [];
    let selectedPaymentMethod = 'Cash';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Focus scanner input on load
    window.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('barcodeInput');
        if (input) input.focus();
    });

    // Barcode Scanner Listener (Enter key triggers instant add)
    document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length > 0) {
                lookupProduct(query);
            }
        }
    });

    // Global Keyboard Shortcuts
    document.addEventListener('keydown', function(e) {
        // F2: Focus scanner
        if (e.key === 'F2') {
            e.preventDefault();
            document.getElementById('barcodeInput').focus();
            document.getElementById('barcodeInput').select();
        }
        // F4: Clear cart
        else if (e.key === 'F4') {
            e.preventDefault();
            clearCart();
        }
        // F8: Cycle payment methods
        else if (e.key === 'F8') {
            e.preventDefault();
            cyclePaymentMethod();
        }
        // F9: Checkout
        else if (e.key === 'F9') {
            e.preventDefault();
            processCheckout();
        }
        // Escape: Close modal
        else if (e.key === 'Escape') {
            closeReceiptModal();
        }
    });

    // Lookup Product via AJAX
    function lookupProduct(query) {
        fetch(`{{ route('kasir.products.search') }}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    alert(`Barang dengan barcode/kata kunci "${query}" tidak ditemukan.`);
                } else if (data.length === 1) {
                    const p = data[0];
                    addToCart(p.id, p.name, p.barcode, parseFloat(p.price), parseInt(p.stock));
                    document.getElementById('barcodeInput').value = '';
                } else {
                    // Filter product cards in view
                    filterCards(query);
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    function handleManualSearch() {
        const query = document.getElementById('barcodeInput').value.trim();
        if (query) lookupProduct(query);
    }

    function filterCards(query) {
        const cards = document.querySelectorAll('.product-item-card');
        const q = query.toLowerCase();
        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const barcode = card.dataset.barcode.toLowerCase();
            if (name.includes(q) || barcode.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function resetSearchFilter() {
        document.getElementById('barcodeInput').value = '';
        document.querySelectorAll('.product-item-card').forEach(c => c.style.display = 'flex');
        document.getElementById('barcodeInput').focus();
    }

    // Add product to cart
    function addToCart(id, name, barcode, price, stock) {
        if (stock <= 0) {
            alert(`Produk "${name}" stoknya habis!`);
            return;
        }

        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            if (existingItem.quantity + 1 > stock) {
                alert(`Jumlah di keranjang sudah mencapai batas sisa stok (${stock} pcs).`);
                return;
            }
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                barcode: barcode,
                price: price,
                stock: stock,
                quantity: 1
            });
        }

        renderCart();
        document.getElementById('barcodeInput').focus();
    }

    // Change Item Quantity
    function updateQuantity(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        const newQty = item.quantity + delta;
        if (newQty <= 0) {
            removeFromCart(id);
            return;
        }

        if (newQty > item.stock) {
            alert(`Maksimal stok tersedia adalah ${item.stock} pcs.`);
            return;
        }

        item.quantity = newQty;
        renderCart();
    }

    function setDirectQuantity(id, inputElem) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        let val = parseInt(inputElem.value);
        if (isNaN(val) || val <= 0) {
            val = 1;
        }
        if (val > item.stock) {
            alert(`Stok hanya tersedia ${item.stock} pcs.`);
            val = item.stock;
        }

        item.quantity = val;
        renderCart();
    }

    // Remove single item from cart
    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    // Clear entire cart
    function clearCart() {
        if (cart.length === 0) return;
        if (confirm('Bersihkan semua produk dari keranjang kasir?')) {
            cart = [];
            renderCart();
        }
    }

    // Render Cart DOM
    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyState = document.getElementById('cartEmptyState');
        const totalItemsBadge = document.getElementById('totalItemsBadge');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const btnCheckout = document.getElementById('btnCheckout');

        if (cart.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyState);
            emptyState.style.display = 'block';
            totalItemsBadge.innerText = '0 item';
            grandTotalDisplay.innerText = 'Rp 0';
            btnCheckout.disabled = true;
            document.getElementById('paymentAmountInput').value = '';
            document.getElementById('changeDisplay').innerText = 'Rp 0';
            return;
        }

        let html = '';
        let totalItems = 0;
        let grandTotal = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            totalItems += item.quantity;
            grandTotal += subtotal;

            html += `
                <div class="cart-item-row">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${item.name}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); font-family: var(--font-mono);">
                            Rp ${formatRupiah(item.price)} × ${item.quantity} = <strong style="color: var(--emerald-700);">Rp ${formatRupiah(subtotal)}</strong>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <button type="button" class="qty-control-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
                        <input 
                            type="number" 
                            value="${item.quantity}" 
                            min="1" 
                            max="${item.stock}" 
                            style="width: 38px; text-align: center; font-size: 0.85rem; font-weight: 700; border: 1px solid var(--slate-300); border-radius: 4px; padding: 2px;"
                            onchange="setDirectQuantity(${item.id}, this)"
                        >
                        <button type="button" class="qty-control-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                        <button type="button" style="background:none; border:none; color: var(--rose-600); cursor:pointer; padding: 4px; margin-left: 2px;" onclick="removeFromCart(${item.id})" title="Hapus">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        totalItemsBadge.innerText = `${totalItems} item`;
        grandTotalDisplay.innerText = `Rp ${formatRupiah(grandTotal)}`;
        btnCheckout.disabled = false;

        // Auto update calculation for cash
        calculateChange();
    }

    // Payment method switching
    function setPaymentMethod(method) {
        selectedPaymentMethod = method;
        ['Cash', 'QRIS', 'Debit'].forEach(m => {
            const btn = document.getElementById(`btnMethod${m}`);
            if (btn) btn.classList.remove('active');
        });

        const activeBtn = document.getElementById(`btnMethod${method.replace(' Card', '')}`);
        if (activeBtn) activeBtn.classList.add('active');

        const cashSection = document.getElementById('cashInputSection');
        if (method === 'Cash') {
            cashSection.style.display = 'block';
            calculateChange();
        } else {
            cashSection.style.display = 'none';
        }
    }

    function cyclePaymentMethod() {
        if (selectedPaymentMethod === 'Cash') setPaymentMethod('QRIS');
        else if (selectedPaymentMethod === 'QRIS') setPaymentMethod('Debit Card');
        else setPaymentMethod('Cash');
    }

    // Quick Cash Helpers
    function getGrandTotal() {
        return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    function quickCashExact() {
        document.getElementById('paymentAmountInput').value = getGrandTotal();
        calculateChange();
    }

    function quickCashAdd(amount) {
        const input = document.getElementById('paymentAmountInput');
        const cur = parseFloat(input.value) || 0;
        input.value = cur + amount;
        calculateChange();
    }

    function calculateChange() {
        if (selectedPaymentMethod !== 'Cash') return;

        const grandTotal = getGrandTotal();
        const payVal = parseFloat(document.getElementById('paymentAmountInput').value) || 0;
        const changeDisplay = document.getElementById('changeDisplay');

        const diff = payVal - grandTotal;
        if (diff >= 0) {
            changeDisplay.style.color = 'var(--emerald-600)';
            changeDisplay.innerText = `Rp ${formatRupiah(diff)}`;
        } else {
            changeDisplay.style.color = 'var(--rose-600)';
            changeDisplay.innerText = `Kurang Rp ${formatRupiah(Math.abs(diff))}`;
        }
    }

    // Process Checkout Transaction
    function processCheckout() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        const grandTotal = getGrandTotal();
        let payAmount = grandTotal;

        if (selectedPaymentMethod === 'Cash') {
            payAmount = parseFloat(document.getElementById('paymentAmountInput').value) || 0;
            if (payAmount < grandTotal) {
                alert(`Nominal pembayaran (Rp ${formatRupiah(payAmount)}) kurang dari total belanja (Rp ${formatRupiah(grandTotal)})!`);
                document.getElementById('paymentAmountInput').focus();
                return;
            }
        }

        const btnCheckout = document.getElementById('btnCheckout');
        btnCheckout.disabled = true;
        btnCheckout.innerText = 'MEMPROSES TRANSAKSI...';

        const payload = {
            items: cart.map(i => ({
                product_id: i.id,
                quantity: i.quantity
            })),
            payment_method: selectedPaymentMethod,
            payment_amount: payAmount
        };

        fetch('{{ route('kasir.checkout') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btnCheckout.disabled = false;
            btnCheckout.innerText = 'BAYAR SEKARANG (F9)';

            if (data.success) {
                // Show Thermal Receipt Modal
                openReceiptModal(data.invoice_number, cart, grandTotal, payAmount, payAmount - grandTotal, selectedPaymentMethod);
                // Clear cart
                cart = [];
                renderCart();
            } else {
                alert('Gagal memproses transaksi: ' + (data.message || 'Terjadi kesalahan sistem.'));
            }
        })
        .catch(err => {
            btnCheckout.disabled = false;
            btnCheckout.innerText = 'BAYAR SEKARANG (F9)';
            console.error(err);
            alert('Terjadi kesalahan jaringan/server saat memproses transaksi.');
        });
    }

    // Modal Receipt Generator
    function openReceiptModal(invoice, items, total, pay, change, method) {
        const modal = document.getElementById('receiptModal');
        const container = document.getElementById('thermalReceiptContent');
        const now = new Date();
        const formattedDate = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        let itemsHtml = '';
        items.forEach(i => {
            itemsHtml += `
                <div style="margin-bottom: 4px;">
                    <div style="display: flex; justify-content: space-between; font-weight: 600;">
                        <span>${i.name}</span>
                        <span>Rp ${formatRupiah(i.price * i.quantity)}</span>
                    </div>
                    <div style="font-size: 11px; color: #475569;">
                        ${i.quantity} x Rp ${formatRupiah(i.price)}
                    </div>
                </div>
            `;
        });

        container.innerHTML = `
            <div class="receipt-header">
                <div class="store-name">TOKO RETAIL SMART</div>
                <div>Jl. Merdeka No. 45, Indonesia</div>
                <div>Telp: (021) 555-0199</div>
                <div class="receipt-divider"></div>
                <div style="display: flex; justify-content: space-between; font-size: 11px;">
                    <span>No: ${invoice}</span>
                    <span>${formattedDate}</span>
                </div>
                <div style="text-align: left; font-size: 11px;">
                    Kasir: {{ Auth::user()->name }}
                </div>
            </div>

            <div style="margin: 8px 0;">
                ${itemsHtml}
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Total Belanja:</span>
                    <span style="font-weight: 700;">Rp ${formatRupiah(total)}</span>
                </div>
                <div class="total-row">
                    <span>Metode:</span>
                    <span>${method}</span>
                </div>
                <div class="total-row">
                    <span>Bayar:</span>
                    <span>Rp ${formatRupiah(pay)}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Kembali:</span>
                    <span>Rp ${formatRupiah(change)}</span>
                </div>
            </div>

            <div class="receipt-footer">
                <div>*** TERIMA KASIH ***</div>
                <div>Barang yang dibeli tidak dapat ditukar/dikembalikan</div>
                <div style="margin-top: 4px; font-size: 10px;">Layanan Konsumen: 0812-3456-7890</div>
            </div>
        `;

        modal.classList.add('active');
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.remove('active');
        document.getElementById('barcodeInput').focus();
    }

    function printReceipt() {
        window.print();
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(Math.round(number));
    }
</script>
@endsection
