<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Parse date range from request.
     */
    protected function parseDateRange(Request $request): array
    {
        $period = $request->get('period', '7days');
        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case '30days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                break;
            case 'custom':
                $from = $request->get('start_date');
                $to = $request->get('end_date');
                $startDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subDays(6)->startOfDay();
                $endDate = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
                break;
            case '7days':
            default:
                $period = '7days';
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                break;
        }

        return [$startDate, $endDate, $period];
    }

    /**
     * Mobile Web-First Admin Dashboard with KPI summaries and visual charts.
     */
    public function dashboard(Request $request)
    {
        [$startDate, $endDate, $period] = $this->parseDateRange($request);

        // Transactions in period
        $transactionsQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);

        $totalRevenue = (float) (clone $transactionsQuery)->sum('total_amount');
        $totalTransactions = (clone $transactionsQuery)->count();

        // Transaction details for HPP and Profit calculation
        $transactionIds = (clone $transactionsQuery)->pluck('id');
        $detailsQuery = TransactionDetail::whereIn('transaction_id', $transactionIds);

        $totalHpp = (float) (clone $detailsQuery)->selectRaw('SUM(cost_price * quantity) as total_hpp')->value('total_hpp') ?? 0;
        $totalGrossProfit = $totalRevenue - $totalHpp;
        $profitMargin = $totalRevenue > 0 ? round(($totalGrossProfit / $totalRevenue) * 100, 1) : 0;
        $totalItemsSold = (int) (clone $detailsQuery)->sum('quantity');

        // Low stock alerts count
        $lowStockCount = Product::where('stock', '<', 10)->count();

        // Chart Data: Group by Date (revenue and gross profit)
        $chartDataRaw = Transaction::selectRaw('DATE(created_at) as trx_date, SUM(total_amount) as daily_revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('trx_date')
            ->orderBy('trx_date', 'asc')
            ->get()
            ->keyBy('trx_date');

        // Calculate daily gross profit
        $dailyProfitsRaw = TransactionDetail::selectRaw('DATE(transaction_details.created_at) as trx_date, SUM((transaction_details.price - transaction_details.cost_price) * transaction_details.quantity) as daily_profit')
            ->whereIn('transaction_id', $transactionIds)
            ->groupBy('trx_date')
            ->get()
            ->keyBy('trx_date');

        // Build continuous timeline labels & values
        $chartLabels = [];
        $chartRevenues = [];
        $chartProfits = [];

        $cursor = $startDate->copy();
        while ($cursor <= $endDate) {
            $dateKey = $cursor->format('Y-m-d');
            $displayLabel = $cursor->translatedFormat('d M');

            $rev = isset($chartDataRaw[$dateKey]) ? (float) $chartDataRaw[$dateKey]->daily_revenue : 0;
            $prof = isset($dailyProfitsRaw[$dateKey]) ? (float) $dailyProfitsRaw[$dateKey]->daily_profit : 0;

            $chartLabels[] = $displayLabel;
            $chartRevenues[] = $rev;
            $chartProfits[] = $prof;

            $cursor->addDay();
        }

        // Top 5 selling products in period
        $topProducts = TransactionDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM((price - cost_price) * quantity) as total_profit')
            )
            ->whereIn('transaction_id', $transactionIds)
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Payment method distribution
        $paymentMethods = (clone $transactionsQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Recent 5 transactions (read-only)
        $recentTransactions = Transaction::with(['cashier', 'details.product'])
            ->latest()
            ->take(5)
            ->get();

        // Recent 6 activity logs
        $recentLogs = ActivityLog::latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'period',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalHpp',
            'totalGrossProfit',
            'profitMargin',
            'totalTransactions',
            'totalItemsSold',
            'lowStockCount',
            'chartLabels',
            'chartRevenues',
            'chartProfits',
            'topProducts',
            'paymentMethods',
            'recentTransactions',
            'recentLogs'
        ));
    }

    /**
     * Read-Only Sales Report with detailed filtering.
     */
    public function salesReport(Request $request)
    {
        [$startDate, $endDate, $period] = $this->parseDateRange($request);
        $cashierId = $request->get('cashier_id');
        $paymentMethod = $request->get('payment_method');
        $search = trim($request->get('search', ''));

        $query = Transaction::with(['cashier', 'details.product'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!empty($cashierId)) {
            $query->where('user_id', $cashierId);
        }

        if (!empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        if (!empty($search)) {
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        // Summary aggregates for filtered set
        $summaryQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);
        if (!empty($cashierId)) $summaryQuery->where('user_id', $cashierId);
        if (!empty($paymentMethod)) $summaryQuery->where('payment_method', $paymentMethod);
        if (!empty($search)) $summaryQuery->where('invoice_number', 'like', "%{$search}%");

        $filteredTotalRevenue = (float) (clone $summaryQuery)->sum('total_amount');
        $filteredTransactionCount = (clone $summaryQuery)->count();

        $cashiers = User::where('role', 'UKasir')->get();

        return view('admin.sales', compact(
            'transactions',
            'period',
            'startDate',
            'endDate',
            'cashierId',
            'paymentMethod',
            'search',
            'cashiers',
            'filteredTotalRevenue',
            'filteredTransactionCount'
        ));
    }

    /**
     * Read-Only Gross Profit Report per product and per transaction.
     */
    public function profitReport(Request $request)
    {
        [$startDate, $endDate, $period] = $this->parseDateRange($request);

        $transactionIds = Transaction::whereBetween('created_at', [$startDate, $endDate])->pluck('id');

        // Product level profit breakdown
        $productProfits = TransactionDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as units_sold'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(cost_price * quantity) as total_cost'),
                DB::raw('SUM((price - cost_price) * quantity) as gross_profit')
            )
            ->whereIn('transaction_id', $transactionIds)
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('gross_profit')
            ->paginate(15)
            ->withQueryString();

        // Grand Totals
        $grandRevenue = (float) TransactionDetail::whereIn('transaction_id', $transactionIds)->sum('subtotal');
        $grandCost = (float) TransactionDetail::whereIn('transaction_id', $transactionIds)->selectRaw('SUM(cost_price * quantity) as c')->value('c') ?? 0;
        $grandProfit = $grandRevenue - $grandCost;
        $grandMargin = $grandRevenue > 0 ? round(($grandProfit / $grandRevenue) * 100, 1) : 0;

        return view('admin.profits', compact(
            'productProfits',
            'period',
            'startDate',
            'endDate',
            'grandRevenue',
            'grandCost',
            'grandProfit',
            'grandMargin'
        ));
    }

    /**
     * Read-Only Centralized Store Activity Logs Feed.
     */
    public function activityLogs(Request $request)
    {
        $actionFilter = $request->get('action');
        $roleFilter = $request->get('role');
        $search = trim($request->get('search', ''));

        $query = ActivityLog::query();

        if (!empty($actionFilter)) {
            $query->where('action', $actionFilter);
        }

        if (!empty($roleFilter)) {
            $query->where('user_role', $roleFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        // Get unique action names for filter dropdown
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activities', compact('logs', 'actionFilter', 'roleFilter', 'search', 'actions'));
    }
}
