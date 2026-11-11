<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Display the Desktop-First POS Cashier interface.
     */
    public function pos()
    {
        // Load initial active products with stock > 0
        $products = Product::orderBy('name', 'asc')->get();

        // Load today's transactions handled by this cashier
        $todayTransactions = Transaction::with('details.product')
            ->where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->take(10)
            ->get();

        $todayTotalSales = Transaction::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $todayCount = Transaction::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->count();

        return view('kasir.pos', compact('products', 'todayTransactions', 'todayTotalSales', 'todayCount'));
    }

    /**
     * Search products by barcode or name (for scanner & live lookup).
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));

        if (empty($query)) {
            $products = Product::orderBy('name', 'asc')->take(20)->get();
            return response()->json($products);
        }

        // Exact match on barcode takes absolute priority (for barcode gun scanners)
        $exactBarcode = Product::where('barcode', $query)->first();
        if ($exactBarcode) {
            return response()->json([$exactBarcode]);
        }

        // Partial match on barcode or name
        $products = Product::where('barcode', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->take(15)
            ->get();

        return response()->json($products);
    }

    /**
     * Process checkout transaction with real-time stock deduction and DB::transaction.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'string', 'in:Cash,QRIS,Debit Card,Transfer'],
            'payment_amount' => ['required', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Keranjang belanja tidak boleh kosong.',
            'items.min' => 'Pilih minimal satu produk untuk melakukan transaksi.',
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_amount.required' => 'Nominal pembayaran wajib diisi.',
            'payment_amount.numeric' => 'Nominal pembayaran harus berupa angka.',
        ]);

        $items = $request->input('items');
        $paymentMethod = $request->input('payment_method');
        $paymentAmount = (float) $request->input('payment_amount');

        try {
            $transaction = DB::transaction(function () use ($items, $paymentMethod, $paymentAmount) {
                $totalAmount = 0;
                $detailsToCreate = [];

                // 1. Validate each product and lock the row for update (prevent race conditions)
                foreach ($items as $item) {
                    $productId = $item['product_id'];
                    $qty = (int) $item['quantity'];

                    $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

                    // Check if stock is sufficient
                    if ($product->stock < $qty) {
                        throw new Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->stock}, permintaan: {$qty}.");
                    }

                    $subtotal = (float) ($product->price * $qty);
                    $totalAmount += $subtotal;

                    $detailsToCreate[] = [
                        'product' => $product,
                        'quantity' => $qty,
                        'price' => (float) $product->price,
                        'cost_price' => (float) $product->cost_price,
                        'subtotal' => $subtotal,
                    ];
                }

                // 2. Validate payment amount for Cash
                if ($paymentMethod === 'Cash' && $paymentAmount < $totalAmount) {
                    throw new Exception("Uang yang dibayarkan (Rp " . number_format($paymentAmount, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($totalAmount, 0, ',', '.') . ").");
                }

                // For cashless (QRIS/Debit), payment is exact
                if ($paymentMethod !== 'Cash') {
                    $paymentAmount = $totalAmount;
                }

                $changeAmount = max(0, $paymentAmount - $totalAmount);

                // 3. Generate unique invoice number: TRX-YYYYMMDD-XXXX
                $datePrefix = Carbon::now()->format('Ymd');
                $dailyCount = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
                $invoiceNumber = 'TRX-' . $datePrefix . '-' . str_pad($dailyCount, 4, '0', STR_PAD_LEFT);

                // Ensure unique invoice number
                while (Transaction::where('invoice_number', $invoiceNumber)->exists()) {
                    $dailyCount++;
                    $invoiceNumber = 'TRX-' . $datePrefix . '-' . str_pad($dailyCount, 4, '0', STR_PAD_LEFT);
                }

                // 4. Create Transaction header
                $newTransaction = Transaction::create([
                    'user_id' => Auth::id(),
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $totalAmount,
                    'payment_amount' => $paymentAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                ]);

                // 5. Create details and decrement real-time product stock
                foreach ($detailsToCreate as $d) {
                    TransactionDetail::create([
                        'transaction_id' => $newTransaction->id,
                        'product_id' => $d['product']->id,
                        'quantity' => $d['quantity'],
                        'price' => $d['price'],
                        'cost_price' => $d['cost_price'],
                        'subtotal' => $d['subtotal'],
                    ]);

                    // Real-time stock decrement
                    $d['product']->decrement('stock', $d['quantity']);
                }

                // 6. Record activity log
                ActivityLog::log(
                    'TRANSACTION_CREATED',
                    "Kasir " . Auth::user()->name . " memproses transaksi {$invoiceNumber} senilai Rp " . number_format($totalAmount, 0, ',', '.') . " ({$paymentMethod})",
                    Auth::user(),
                    [
                        'invoice_number' => $invoiceNumber,
                        'total_amount' => $totalAmount,
                        'items_count' => count($detailsToCreate),
                    ]
                );

                return $newTransaction;
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil diproses!',
                    'invoice_number' => $transaction->invoice_number,
                    'transaction_id' => $transaction->id,
                    'receipt_url' => route('kasir.receipt', $transaction->invoice_number),
                ]);
            }

            return redirect()->route('kasir.receipt', $transaction->invoice_number)
                ->with('success', 'Transaksi berhasil disimpan!');

        } catch (Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Thermal Receipt View / Modal Print.
     */
    public function receipt(string $invoice)
    {
        $transaction = Transaction::with(['cashier', 'details.product'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return view('kasir.receipt', compact('transaction'));
    }

    /**
     * Cashier's today transaction history list.
     */
    public function history(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $transactions = Transaction::with(['details.product'])
            ->where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalSales = Transaction::where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->sum('total_amount');

        $transactionCount = Transaction::where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->count();

        return view('kasir.history', compact('transactions', 'date', 'totalSales', 'transactionCount'));
    }
}
