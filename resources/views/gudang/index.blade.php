@extends('layouts.gudang')

@section('title', 'Master Barang & Inventaris - RetailSmart Gudang')

@section('content')
<!-- Header & Action Row -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--slate-900); letter-spacing: -0.02em;">Master Barang & Inventaris</h1>
        <p style="font-size: 0.875rem; color: var(--slate-500); margin-top: 0.15rem;">Kelola data produk, catat pasokan distributor, dan lakukan penyesuaian stok opname.</p>
    </div>

    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-emerald" onclick="openAddModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Barang Baru
        </button>

        <button type="button" class="btn btn-amber" onclick="openIncomingModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Pasokan Masuk Supplier
        </button>

        <button type="button" class="btn btn-primary" onclick="openAdjustmentModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Koreksi Stok (Adjustment)
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.775rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase;">Total Master Barang</div>
        <div style="font-size: 1.65rem; font-weight: 900; color: var(--slate-900); margin-top: 0.25rem;">
            {{ number_format($totalProducts, 0, ',', '.') }} SKU
        </div>
        <div style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">Tercatat aktif di database</div>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.775rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase;">Total Unit Stok Fisik</div>
        <div style="font-size: 1.65rem; font-weight: 900; color: var(--emerald-600); margin-top: 0.25rem;">
            {{ number_format($totalStockUnits, 0, ',', '.') }} Pcs
        </div>
        <div style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">Akumulasi seluruh barang</div>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.775rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase;">Stok Kritis (&lt; 10 pcs)</div>
        <div style="font-size: 1.65rem; font-weight: 900; color: var(--amber-600); margin-top: 0.25rem;">
            {{ $lowStockCount }} SKU
        </div>
        <div style="font-size: 0.75rem; color: var(--amber-700); margin-top: 0.25rem;">Perlu pengajuan restock</div>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.775rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase;">Nilai Aset Inventaris (HPP)</div>
        <div style="font-size: 1.65rem; font-weight: 900; color: var(--slate-900); margin-top: 0.25rem;">
            Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}
        </div>
        <div style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">Berdasarkan harga modal beli</div>
    </div>
</div>

<!-- Filters & Search Bar -->
<div class="card" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem;">
    <form method="GET" action="{{ route('gudang.index') }}" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px;">
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                class="form-control" 
                placeholder="Cari berdasarkan Barcode atau Nama Produk..."
            >
        </div>

        <div style="display: flex; gap: 0.35rem; align-items: center;">
            <a href="{{ route('gudang.index', ['status' => 'all', 'search' => $search]) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline' }}">
                Semua Barang
            </a>
            <a href="{{ route('gudang.index', ['status' => 'low', 'search' => $search]) }}" class="btn btn-sm {{ $status === 'low' ? 'btn-amber' : 'btn-outline' }}">
                Stok Kritis (&lt;10)
            </a>
            <a href="{{ route('gudang.index', ['status' => 'out', 'search' => $search]) }}" class="btn btn-sm {{ $status === 'out' ? 'btn-danger' : 'btn-outline' }}">
                Stok Habis (0)
            </a>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(!empty($search) || $status !== 'all')
            <a href="{{ route('gudang.index') }}" class="btn btn-sm btn-outline" title="Reset Filter">Reset</a>
        @endif
    </form>
</div>

<!-- Product Table -->
<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Nama Produk</th>
                <th>Harga Modal (HPP)</th>
                <th>Harga Jual</th>
                <th>Margin Laba</th>
                <th>Stok Gudang</th>
                <th style="text-align: right;">Aksi Manajemen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        <span style="font-family: var(--font-mono); font-weight: 700; color: var(--slate-800); font-size: 0.85rem;">
                            {{ $p->barcode }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--slate-900);">{{ $p->name }}</div>
                        @if($p->description)
                            <div style="font-size: 0.775rem; color: var(--slate-500); max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $p->description }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span style="color: var(--slate-600); font-family: var(--font-mono);">
                            Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight: 700; color: var(--emerald-700); font-family: var(--font-mono);">
                            Rp {{ number_format($p->price, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-emerald">
                            +{{ $p->margin_percent }}% (Rp {{ number_format($p->margin_amount, 0, ',', '.') }})
                        </span>
                    </td>
                    <td>
                        @if($p->stock <= 0)
                            <span class="badge badge-rose" style="font-size: 0.85rem; padding: 0.25rem 0.65rem;">
                                0 pcs (Habis)
                            </span>
                        @elseif($p->stock < 10)
                            <span class="badge badge-amber" style="font-size: 0.85rem; padding: 0.25rem 0.65rem;">
                                {{ $p->stock }} pcs (Kritis)
                            </span>
                        @else
                            <span class="badge badge-emerald" style="font-size: 0.85rem; padding: 0.25rem 0.65rem;">
                                {{ $p->stock }} pcs
                            </span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <!-- Edit Button -->
                            <button 
                                type="button" 
                                class="btn btn-sm btn-outline" 
                                onclick="openEditModal({{ $p->id }}, '{{ addslashes($p->barcode) }}', '{{ addslashes($p->name) }}', '{{ addslashes($p->description ?? '') }}', {{ $p->cost_price }}, {{ $p->price }})"
                                title="Ubah Data Produk"
                            >
                                Edit
                            </button>

                            <!-- Quick Adjust Button -->
                            <button 
                                type="button" 
                                class="btn btn-sm btn-outline" 
                                style="border-color: var(--amber-400); color: var(--amber-700);" 
                                onclick="preselectAdjustment({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->stock }})"
                                title="Koreksi Stok Opname"
                            >
                                Sesuaikan
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('gudang.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ addslashes($p->name) }}?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline" style="border-color: var(--rose-200); color: var(--rose-600);" title="Hapus Barang">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 3rem 1rem;">
                        Tidak ada barang yang cocok dengan kriteria pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.25rem;">
    {{ $products->links() }}
</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH BARANG BARU -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="modalAddProduct">
    <div class="modal-content">
        <form action="{{ route('gudang.products.store') }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 1.15rem; font-weight: 800;">Tambah Produk Baru</h3>
                <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalAddProduct')" style="border-radius: var(--radius-full); width: 28px; height: 28px; padding: 0;">✕</button>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="new_barcode" class="form-label">Barcode / Kode Produk <span style="color: var(--emerald-600);">*</span></label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="barcode" id="new_barcode" class="form-control" placeholder="Contoh: 899999900125" required style="font-family: var(--font-mono);">
                        <button type="button" class="btn btn-outline btn-sm" onclick="generateRandomBarcode()">Auto</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_name" class="form-label">Nama Barang <span style="color: var(--emerald-600);">*</span></label>
                    <input type="text" name="name" id="new_name" class="form-control" placeholder="Contoh: Kopi Luwak White Koffie 20g" required>
                </div>

                <div class="form-group">
                    <label for="new_description" class="form-label">Deskripsi Produk (Opsional)</label>
                    <textarea name="description" id="new_description" class="form-control" rows="2" placeholder="Keterangan singkat produk..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group">
                        <label for="new_cost_price" class="form-label">Harga Modal HPP (Rp) <span style="color: var(--emerald-600);">*</span></label>
                        <input type="number" name="cost_price" id="new_cost_price" class="form-control" placeholder="0" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="new_price" class="form-label">Harga Jual Konsumen (Rp) <span style="color: var(--emerald-600);">*</span></label>
                        <input type="number" name="price" id="new_price" class="form-control" placeholder="0" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_stock" class="form-label">Stok Awal Fisik (Pcs) <span style="color: var(--emerald-600);">*</span></label>
                    <input type="number" name="stock" id="new_stock" class="form-control" placeholder="0" min="0" value="0" required>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddProduct')">Batal</button>
                <button type="submit" class="btn btn-emerald">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: EDIT BARANG -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="modalEditProduct">
    <div class="modal-content">
        <form id="formEditProduct" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h3 style="font-size: 1.15rem; font-weight: 800;">Perbarui Data Barang</h3>
                <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalEditProduct')" style="border-radius: var(--radius-full); width: 28px; height: 28px; padding: 0;">✕</button>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="edit_barcode" class="form-label">Barcode / Kode Produk</label>
                    <input type="text" name="barcode" id="edit_barcode" class="form-control" required style="font-family: var(--font-mono);">
                </div>

                <div class="form-group">
                    <label for="edit_name" class="form-label">Nama Barang</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group">
                        <label for="edit_cost_price" class="form-label">Harga Modal (Rp)</label>
                        <input type="number" name="cost_price" id="edit_cost_price" class="form-control" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_price" class="form-label">Harga Jual (Rp)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEditProduct')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: PASOKAN MASUK SUPPLIER (RESTOCK) -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="modalIncomingStock">
    <div class="modal-content">
        <form action="{{ route('gudang.stock.incoming') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom-color: var(--amber-300);">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--amber-700);">📥 Penerimaan Pasokan Supplier (Stock In)</h3>
                <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalIncomingStock')" style="border-radius: var(--radius-full); width: 28px; height: 28px; padding: 0;">✕</button>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="incoming_product_id" class="form-label">Pilih Barang yang Masuk <span style="color: var(--amber-600);">*</span></label>
                    <select name="product_id" id="incoming_product_id" class="form-control" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach(App\Models\Product::orderBy('name')->get() as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->name }} [Stok Sekarang: {{ $prod->stock }} pcs]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="incoming_quantity" class="form-label">Kuantitas Masuk (+ Pcs) <span style="color: var(--amber-600);">*</span></label>
                    <input type="number" name="quantity" id="incoming_quantity" class="form-control" placeholder="Contoh: 50" min="1" required>
                </div>

                <div class="form-group">
                    <label for="incoming_supplier" class="form-label">Nama Supplier / Distributor <span style="color: var(--amber-600);">*</span></label>
                    <input type="text" name="supplier_name" id="incoming_supplier" class="form-control" placeholder="Contoh: PT Indofood Sukses Makmur" required>
                </div>

                <div class="form-group">
                    <label for="incoming_notes" class="form-label">Nomor Surat Jalan / Faktur / Catatan</label>
                    <input type="text" name="notes" id="incoming_notes" class="form-control" placeholder="Contoh: SJ-2026/X/8892">
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalIncomingStock')">Batal</button>
                <button type="submit" class="btn btn-amber">Simpan Pasokan Masuk</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: KOREKSI STOK OPNAME (STOCK ADJUSTMENT) -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="modalAdjustmentStock">
    <div class="modal-content">
        <form action="{{ route('gudang.stock.adjustment') }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--slate-900);">⚖️ Penyesuaian Stok Opname (Adjustment)</h3>
                <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalAdjustmentStock')" style="border-radius: var(--radius-full); width: 28px; height: 28px; padding: 0;">✕</button>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="adjust_product_id" class="form-label">Pilih Produk yang Dikoreksi <span style="color: var(--slate-900);">*</span></label>
                    <select name="product_id" id="adjust_product_id" class="form-control" required onchange="handleProductSelectForAdjustment(this)">
                        <option value="">-- Pilih Barang --</option>
                        @foreach(App\Models\Product::orderBy('name')->get() as $prod)
                            <option value="{{ $prod->id }}" data-stock="{{ $prod->stock }}">
                                {{ $prod->name }} (Stok Sistem: {{ $prod->stock }} pcs)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Koreksi Stok <span style="color: var(--slate-900);">*</span></label>
                    <select name="adjustment_type" id="adjust_type" class="form-control" required>
                        <option value="SET_EXACT">Setel Jumlah Stok Fisik Aktual (Opname)</option>
                        <option value="SUBTRACT">Kurangi Stok (Barang Rusak / Kadaluarsa / Hilang)</option>
                        <option value="ADD">Tambah Stok (Kelebihan Fisik Ditemukan)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="adjust_amount" class="form-label">Jumlah Kuantitas (Pcs) <span style="color: var(--slate-900);">*</span></label>
                    <input type="number" name="amount" id="adjust_amount" class="form-control" placeholder="0" min="0" required>
                </div>

                <div class="form-group">
                    <label for="adjust_reason" class="form-label">Alasan Koreksi <span style="color: var(--slate-900);">*</span></label>
                    <input type="text" name="reason" id="adjust_reason" class="form-control" placeholder="Contoh: Kemasan bocor saat display di rak / Selisih opname fisik" required>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdjustmentStock')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Koreksi Stok</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openAddModal() {
        document.getElementById('modalAddProduct').classList.add('active');
        document.getElementById('new_barcode').focus();
    }

    function generateRandomBarcode() {
        const rand = '899' + Math.floor(100000000 + Math.random() * 900000000);
        document.getElementById('new_barcode').value = rand;
    }

    function openEditModal(id, barcode, name, description, costPrice, price) {
        const form = document.getElementById('formEditProduct');
        form.action = `/gudang/products/${id}`;
        document.getElementById('edit_barcode').value = barcode;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_cost_price').value = costPrice;
        document.getElementById('edit_price').value = price;
        document.getElementById('modalEditProduct').classList.add('active');
    }

    function openIncomingModal() {
        document.getElementById('modalIncomingStock').classList.add('active');
    }

    function openAdjustmentModal() {
        document.getElementById('modalAdjustmentStock').classList.add('active');
    }

    function preselectAdjustment(productId, productName, currentStock) {
        const select = document.getElementById('adjust_product_id');
        select.value = productId;
        document.getElementById('adjust_amount').value = currentStock;
        document.getElementById('modalAdjustmentStock').classList.add('active');
    }

    function handleProductSelectForAdjustment(select) {
        const selectedOption = select.options[select.selectedIndex];
        const stock = selectedOption.getAttribute('data-stock');
        if (stock !== null) {
            document.getElementById('adjust_amount').value = stock;
        }
    }
</script>
@endsection
