<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockAdjustment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GudangController extends Controller
{
    /**
     * Display the Master Product Inventory table with stats & filters.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status', 'all'); // all, low, out

        $query = Product::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status === 'low') {
            $query->where('stock', '<', 10)->where('stock', '>', 0);
        } elseif ($status === 'out') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // Statistics
        $totalProducts = Product::count();
        $totalStockUnits = Product::sum('stock');
        $lowStockCount = Product::where('stock', '<', 10)->count();
        $totalInventoryValue = Product::selectRaw('SUM(stock * cost_price) as total_val')->value('total_val') ?? 0;

        // Recent stock mutations for quick preview widget
        $recentMutations = StockAdjustment::with(['product', 'user'])
            ->latest()
            ->take(6)
            ->get();

        return view('gudang.index', compact(
            'products',
            'search',
            'status',
            'totalProducts',
            'totalStockUnits',
            'lowStockCount',
            'totalInventoryValue',
            'recentMutations'
        ));
    }

    /**
     * Store a newly created product in Master Barang.
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:50', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0', 'gte:cost_price'],
        ], [
            'barcode.required' => 'Barcode / Kode barang wajib diisi.',
            'barcode.unique' => 'Barcode ini sudah terdaftar untuk barang lain.',
            'name.required' => 'Nama barang wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
            'cost_price.required' => 'Harga beli (HPP) wajib diisi.',
            'price.required' => 'Harga jual wajib diisi.',
            'price.gte' => 'Harga jual tidak boleh lebih kecil dari harga beli modal.',
        ]);

        $product = DB::transaction(function () use ($validated) {
            $newProduct = Product::create($validated);

            // If initial stock > 0, record initial stock adjustment
            if ($newProduct->stock > 0) {
                StockAdjustment::create([
                    'product_id' => $newProduct->id,
                    'user_id' => Auth::id(),
                    'type' => 'INCOMING',
                    'quantity_before' => 0,
                    'quantity_change' => $newProduct->stock,
                    'quantity_after' => $newProduct->stock,
                    'reason' => 'Stok awal pembuatan produk baru di Master Barang',
                ]);
            }

            ActivityLog::log(
                'PRODUCT_CREATED',
                "Gudang " . Auth::user()->name . " menambahkan produk baru '{$newProduct->name}' [{$newProduct->barcode}] dengan stok awal {$newProduct->stock} pcs",
                Auth::user()
            );

            return $newProduct;
        });

        return redirect()->route('gudang.index')
            ->with('success', "Barang '{$product->name}' berhasil ditambahkan ke inventaris!");
    }

    /**
     * Update product details.
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0', 'gte:cost_price'],
        ], [
            'barcode.required' => 'Barcode wajib diisi.',
            'barcode.unique' => 'Barcode ini sudah digunakan produk lain.',
            'name.required' => 'Nama barang wajib diisi.',
            'cost_price.required' => 'Harga beli modal wajib diisi.',
            'price.required' => 'Harga jual wajib diisi.',
            'price.gte' => 'Harga jual tidak boleh lebih rendah dari harga beli modal.',
        ]);

        $oldName = $product->name;
        $product->update($validated);

        ActivityLog::log(
            'PRODUCT_UPDATED',
            "Gudang " . Auth::user()->name . " memperbarui informasi produk '{$oldName}'",
            Auth::user()
        );

        return redirect()->route('gudang.index')
            ->with('success', "Data barang '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Remove product from Master Barang.
     */
    public function destroyProduct(Product $product)
    {
        // Check if product already has transactions
        if ($product->transactionDetails()->exists()) {
            return back()->with('error', "Produk '{$product->name}' tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Anda dapat mengubah stoknya menjadi 0.");
        }

        $name = $product->name;
        $barcode = $product->barcode;

        $product->delete();

        ActivityLog::log(
            'PRODUCT_DELETED',
            "Gudang " . Auth::user()->name . " menghapus produk '{$name}' [{$barcode}] dari sistem",
            Auth::user()
        );

        return redirect()->route('gudang.index')
            ->with('success', "Produk '{$name}' berhasil dihapus dari sistem.");
    }

    /**
     * Record incoming stock from supplier (Restock).
     */
    public function stockIncoming(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'product_id.required' => 'Pilih barang yang masuk.',
            'quantity.required' => 'Kuantitas barang masuk wajib diisi.',
            'quantity.min' => 'Kuantitas minimal adalah 1.',
            'supplier_name.required' => 'Nama supplier / distributor wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

                $qtyBefore = $product->stock;
                $qtyChange = (int) $validated['quantity'];
                $qtyAfter = $qtyBefore + $qtyChange;

                $reason = "Penerimaan pasokan dari {$validated['supplier_name']}";
                if (!empty($validated['notes'])) {
                    $reason .= " - Catatan: " . $validated['notes'];
                }

                $product->increment('stock', $qtyChange);

                StockAdjustment::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'INCOMING',
                    'quantity_before' => $qtyBefore,
                    'quantity_change' => $qtyChange,
                    'quantity_after' => $qtyAfter,
                    'reason' => $reason,
                ]);

                ActivityLog::log(
                    'STOCK_INCOMING',
                    "Gudang " . Auth::user()->name . " mencatat pasokan masuk '{$product->name}' (+{$qtyChange} pcs) dari {$validated['supplier_name']}. Stok sekarang: {$qtyAfter} pcs",
                    Auth::user()
                );
            });

            return redirect()->route('gudang.index')
                ->with('success', 'Penerimaan stok barang masuk dari supplier berhasil disimpan!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses stok masuk: ' . $e->getMessage());
        }
    }

    /**
     * Record stock adjustment (Koreksi stok fisik / rusak / hilang).
     */
    public function stockAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', 'in:SET_EXACT,ADD,SUBTRACT'],
            'amount' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'product_id.required' => 'Pilih barang yang disesuaikan.',
            'adjustment_type.required' => 'Tipe penyesuaian harus dipilih.',
            'amount.required' => 'Nilai kuantitas penyesuaian wajib diisi.',
            'reason.required' => 'Alasan penyesuaian stok (rusak/hilang/opname) wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

                $qtyBefore = $product->stock;
                $amount = (int) $validated['amount'];
                $type = $validated['adjustment_type'];

                if ($type === 'SET_EXACT') {
                    $qtyAfter = $amount;
                    $qtyChange = $qtyAfter - $qtyBefore;
                } elseif ($type === 'ADD') {
                    $qtyChange = $amount;
                    $qtyAfter = $qtyBefore + $qtyChange;
                } else { // SUBTRACT
                    $qtyChange = -$amount;
                    $qtyAfter = max(0, $qtyBefore - $amount);
                }

                $product->update(['stock' => $qtyAfter]);

                StockAdjustment::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'ADJUSTMENT',
                    'quantity_before' => $qtyBefore,
                    'quantity_change' => $qtyChange,
                    'quantity_after' => $qtyAfter,
                    'reason' => $validated['reason'],
                ]);

                $changeStr = $qtyChange >= 0 ? "+{$qtyChange}" : "{$qtyChange}";
                ActivityLog::log(
                    'STOCK_ADJUSTMENT',
                    "Gudang " . Auth::user()->name . " menyesuaikan stok '{$product->name}' ({$changeStr} pcs). Stok akhir: {$qtyAfter} pcs. Alasan: {$validated['reason']}",
                    Auth::user()
                );
            });

            return redirect()->route('gudang.index')
                ->with('success', 'Penyesuaian stok (Stock Adjustment) berhasil disimpan!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses penyesuaian stok: ' . $e->getMessage());
        }
    }

    /**
     * Display stock mutation logs table.
     */
    public function mutations(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = trim($request->get('search', ''));

        $query = StockAdjustment::with(['product', 'user']);

        if ($type === 'INCOMING' || $type === 'ADJUSTMENT') {
            $query->where('type', $type);
        }

        if (!empty($search)) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            })->orWhere('reason', 'like', "%{$search}%");
        }

        $mutations = $query->latest()->paginate(15)->withQueryString();

        return view('gudang.mutations', compact('mutations', 'type', 'search'));
    }
}
