<?php
namespace App\Http\Controllers;

use App\Models\backend\Expense;
use App\Models\backend\Sale;
use App\Models\backend\User;
use App\Models\backend\WebsiteSetting;
use App\Support\BranchScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CoreController extends Controller
{
    public function home()
    {
        // Compute dashboard metrics and return view
        $today        = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth   = $today->copy()->endOfMonth();

        // apply branch scope: if superadmin selected 'all' show aggregation for all branches,
        // otherwise restrict to selected branch id
        $branchAll = BranchScope::isAll();
        $branchId  = current_branch_id();

        $saleQueryToday = Sale::whereDate('created_at', $today);
        $saleQueryMonth = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        if (! $branchAll && $branchId) {
            $saleQueryToday->where('branch_id', $branchId);
            $saleQueryMonth->where('branch_id', $branchId);
        }

        $todaySales  = (float) $saleQueryToday->sum('total');
        $grossSales  = (float) $saleQueryMonth->sum('total');
        $totalOrders = (int) $saleQueryMonth->count();

        $purchaseQuery = DB::table('purchase_receipt_items')
            ->join('purchase_receipts', 'purchase_receipt_items.receipt_id', '=', 'purchase_receipts.id')
            ->whereBetween('purchase_receipts.receipt_date', [$startOfMonth, $endOfMonth]);

        if (! $branchAll && $branchId) {
            $purchaseQuery->where('purchase_receipts.branch_id', $branchId);
        }

        $totalPurchase = (float) $purchaseQuery
            ->selectRaw('SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost) as total')
            ->value('total') ?: 0;

        $expenseQuery = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth]);
        if (! $branchAll && $branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $totalExpense = (float) $expenseQuery->sum('total_amount');

        $totalPurchaseAndExpense = $totalPurchase + $totalExpense;
        $netIncome = $grossSales - $totalPurchase - $totalExpense;

        // ======================== OVERALL REPORT DATA (For Donut Chart) ========================
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek   = $today->copy()->endOfWeek();
        $startOfYear = $today->copy()->startOfYear();
        $endOfYear   = $today->copy()->endOfYear();

        // Helper to get totals
        $getOverallData = function ($start, $end) use ($branchAll, $branchId) {
            $sales = Sale::whereBetween('created_at', [$start, $end]);
            if (! $branchAll && $branchId) {
                $sales->where('branch_id', $branchId);
            }

            $salesTotal = (float) $sales->sum('total');

            $purchase = DB::table('purchase_receipt_items')
                ->join('purchase_receipts', 'purchase_receipt_items.receipt_id', '=', 'purchase_receipts.id')
                ->whereBetween('purchase_receipts.receipt_date', [$start, $end]);
            if (! $branchAll && $branchId) {
                $purchase->where('purchase_receipts.branch_id', $branchId);
            }

            $purchaseTotal = (float) $purchase->selectRaw('SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost) as total')->value('total') ?: 0;

            $expense = Expense::whereBetween('expense_date', [$start, $end]);
            if (! $branchAll && $branchId) {
                $expense->where('branch_id', $branchId);
            }

            $expenseTotal = (float) $expense->sum('total_amount');

            $profit = $salesTotal - $purchaseTotal - $expenseTotal;
            return [$purchaseTotal, $salesTotal, $expenseTotal, $profit > 0 ? $profit : 0];
        };

        $overallReportData = [
            'today'   => $getOverallData($today->copy()->startOfDay(), $today->copy()->endOfDay()),
            'weekly'  => $getOverallData($startOfWeek, $endOfWeek),
            'monthly' => [$totalPurchase, $grossSales, $totalExpense, $netIncome > 0 ? $netIncome : 0],
            'yearly'  => $getOverallData($startOfYear, $endOfYear),
        ];

        // ======================== MONTHLY INCOME vs EXPENSE CHART DATA ========================
        // Get all 12 months of current year for chart
        $currentYear    = $today->year;
        $monthlyIncome  = [];
        $monthlyExpense = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $monthEnd   = $monthStart->copy()->endOfMonth();

            $incomeQuery = Sale::whereBetween('created_at', [$monthStart, $monthEnd]);
            if (! $branchAll && $branchId) {
                $incomeQuery->where('branch_id', $branchId);
            }
            $monthlyIncome[] = (float) $incomeQuery->sum('total');

            // Monthly Purchase (COGS)
            $purchaseQuery = DB::table('purchase_receipt_items')
                ->join('purchase_receipts', 'purchase_receipt_items.receipt_id', '=', 'purchase_receipts.id')
                ->whereBetween('purchase_receipts.receipt_date', [$monthStart, $monthEnd]);
            if (! $branchAll && $branchId) {
                $purchaseQuery->where('purchase_receipts.branch_id', $branchId);
            }
            $monthlyPurchaseValue = (float) $purchaseQuery
                ->selectRaw('SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost) as total')
                ->value('total') ?: 0;

            // Monthly Operating Expense
            $expenseQuery = Expense::whereBetween('expense_date', [$monthStart, $monthEnd]);
            if (! $branchAll && $branchId) {
                $expenseQuery->where('branch_id', $branchId);
            }
            $monthlyExpenseValue = (float) $expenseQuery->sum('total_amount');

            // Total Expense = Purchase + Operating Expense
            $monthlyExpense[] = $monthlyPurchaseValue + $monthlyExpenseValue;
        }
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // ======================== WEEKLY INCOME vs EXPENSE (Last 12 weeks) ========================
        $weeklyIncome  = [];
        $weeklyExpense = [];
        $weeklyLabels  = [];

        for ($week = 11; $week >= 0; $week--) {
            $weekStart = $today->copy()->subWeeks($week)->startOfWeek();
            $weekEnd   = $weekStart->copy()->endOfWeek();

            $incomeQuery = Sale::whereBetween('created_at', [$weekStart, $weekEnd]);
            if (! $branchAll && $branchId) {
                $incomeQuery->where('branch_id', $branchId);
            }
            $weeklyIncome[] = (float) $incomeQuery->sum('total');

            // Weekly Purchase
            $purchaseQuery = DB::table('purchase_receipt_items')
                ->join('purchase_receipts', 'purchase_receipt_items.receipt_id', '=', 'purchase_receipts.id')
                ->whereBetween('purchase_receipts.receipt_date', [$weekStart, $weekEnd]);
            if (! $branchAll && $branchId) {
                $purchaseQuery->where('purchase_receipts.branch_id', $branchId);
            }
            $weeklyPurchaseValue = (float) $purchaseQuery
                ->selectRaw('SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost) as total')
                ->value('total') ?: 0;

            // Weekly Operating Expense
            $expenseQuery = Expense::whereBetween('expense_date', [$weekStart, $weekEnd]);
            if (! $branchAll && $branchId) {
                $expenseQuery->where('branch_id', $branchId);
            }
            $weeklyExpenseValue = (float) $expenseQuery->sum('total_amount');

            $weeklyExpense[] = $weeklyPurchaseValue + $weeklyExpenseValue;
            $weeklyLabels[]  = 'W' . $weekStart->weekOfYear;
        }

        // ======================== DAILY INCOME vs EXPENSE (Last 30 days) ========================
        $dailyIncome  = [];
        $dailyExpense = [];
        $dailyLabels  = [];

        for ($day = 29; $day >= 0; $day--) {
            $date = $today->copy()->subDays($day);

            $incomeQuery = Sale::whereDate('created_at', $date);
            if (! $branchAll && $branchId) {
                $incomeQuery->where('branch_id', $branchId);
            }
            $dailyIncome[] = (float) $incomeQuery->sum('total');

            // Daily Purchase
            $purchaseQuery = DB::table('purchase_receipt_items')
                ->join('purchase_receipts', 'purchase_receipt_items.receipt_id', '=', 'purchase_receipts.id')
                ->whereDate('purchase_receipts.receipt_date', $date);
            if (! $branchAll && $branchId) {
                $purchaseQuery->where('purchase_receipts.branch_id', $branchId);
            }
            $dailyPurchaseValue = (float) $purchaseQuery
                ->selectRaw('SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost) as total')
                ->value('total') ?: 0;

            // Daily Operating Expense
            $expenseQuery = Expense::whereDate('expense_date', $date);
            if (! $branchAll && $branchId) {
                $expenseQuery->where('branch_id', $branchId);
            }
            $dailyExpenseValue = (float) $expenseQuery->sum('total_amount');

            $dailyExpense[] = $dailyPurchaseValue + $dailyExpenseValue;
            $dailyLabels[]  = $date->format('d/m');
        }

        // ======================== YEARLY INCOME vs EXPENSE (Last 5 years) ========================
        $yearlyIncome  = [];
        $yearlyExpense = [];
        $yearlyLabels  = [];

        for ($y = 4; $y >= 0; $y--) {
            $year      = $currentYear - $y;
            $yearStart = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $yearEnd   = $yearStart->copy()->endOfYear();

            $incomeQuery = Sale::whereBetween('created_at', [$yearStart, $yearEnd]);
            if (! $branchAll && $branchId) {
                $incomeQuery->where('branch_id', $branchId);
            }
            $yearlyIncome[] = (float) $incomeQuery->sum('total');

            // Yearly Purchase
            $purchaseQuery = DB::table('purchase_receipt_items')
                ->join('purchase_receipts', 'purchase_receipt_items.receipt_id', '=', 'purchase_receipts.id')
                ->whereBetween('purchase_receipts.receipt_date', [$yearStart, $yearEnd]);
            if (! $branchAll && $branchId) {
                $purchaseQuery->where('purchase_receipts.branch_id', $branchId);
            }
            $yearlyPurchaseValue = (float) $purchaseQuery
                ->selectRaw('SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost) as total')
                ->value('total') ?: 0;

            // Yearly Operating Expense
            $expenseQuery = Expense::whereBetween('expense_date', [$yearStart, $yearEnd]);
            if (! $branchAll && $branchId) {
                $expenseQuery->where('branch_id', $branchId);
            }
            $yearlyExpenseValue = (float) $expenseQuery->sum('total_amount');

            $yearlyExpense[] = $yearlyPurchaseValue + $yearlyExpenseValue;
            $yearlyLabels[]  = (string) $year;
        }

        // Fetch users with branch filtering
        $branchAll = BranchScope::isAll();
        $branchId  = current_branch_id();

        $userQuery = User::with('role');

        if (! $branchAll && $branchId) {
            $userQuery->where('branch_id', $branchId);
        }

        $users = $userQuery->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ======================== PURCHASE & SALES CHART DATA ========================
        $purchaseSalesMetrics = [
            'this_month' => ['purchase' => $totalPurchase, 'sales' => $grossSales],
            'this_week'  => ['purchase' => $overallReportData['weekly'][0], 'sales' => $overallReportData['weekly'][1]],
            'this_year'  => ['purchase' => $overallReportData['yearly'][0], 'sales' => $overallReportData['yearly'][1]],
        ];

        // 1. Weekly (Last 7 Days)
        $thisWeekDailyPurchase = [];
        $thisWeekDailySales    = [];
        $weekDayLabels         = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        for ($i = 0; $i < 7; $i++) {
            $date                    = $today->copy()->startOfWeek()->addDays($i);
            $dData                   = $getOverallData($date->copy()->startOfDay(), $date->copy()->endOfDay());
            $thisWeekDailyPurchase[] = $dData[0];
            $thisWeekDailySales[]    = $dData[1];
        }

        // 2. Monthly (Last 4 Weeks)
        $thisMonthWeeklyPurchase = [];
        $thisMonthWeeklySales    = [];
        $monthWeekLabels         = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        for ($i = 3; $i >= 0; $i--) {
            $wStart                    = $today->copy()->subWeeks($i)->startOfWeek();
            $wEnd                      = $wStart->copy()->endOfWeek();
            $wData                     = $getOverallData($wStart, $wEnd);
            $thisMonthWeeklyPurchase[] = $wData[0];
            $thisMonthWeeklySales[]    = $wData[1];
        }

        // 3. Yearly (12 Months)
        $thisYearMonthlyPurchase = [];
        $thisYearMonthlySales    = [];
        $yearMonthLabels         = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        for ($m = 1; $m <= 12; $m++) {
            $mStart                    = Carbon::createFromDate($currentYear, $m, 1)->startOfMonth();
            $mEnd                      = $mStart->copy()->endOfMonth();
            $mData                     = $getOverallData($mStart, $mEnd);
            $thisYearMonthlyPurchase[] = $mData[0];
            $thisYearMonthlySales[]    = $mData[1];
        }

        $purchaseSaleChartData = [
            'this_week'  => [
                'purchase' => $thisWeekDailyPurchase,
                'sales'    => $thisWeekDailySales,
                'labels'   => $weekDayLabels,
            ],
            'this_month' => [
                'purchase' => $thisMonthWeeklyPurchase,
                'sales'    => $thisMonthWeeklySales,
                'labels'   => $monthWeekLabels,
            ],
            'this_year'  => [
                'purchase' => $thisYearMonthlyPurchase,
                'sales'    => $thisYearMonthlySales,
                'labels'   => $yearMonthLabels,
            ],
        ];

        // ======================== TOP 5 SUPPLIERS BY PURCHASE AMOUNT (Last 30 days) ========================
        $supplierQuery = DB::table('suppliers')
            ->leftJoin('purchase_receipts', 'suppliers.id', '=', 'purchase_receipts.supplier_id')
            ->leftJoin('purchase_receipt_items', 'purchase_receipts.id', '=', 'purchase_receipt_items.receipt_id')
            ->select(
                'suppliers.id',
                'suppliers.name',
                DB::raw('COALESCE(SUM(purchase_receipt_items.quantity * purchase_receipt_items.unit_cost), 0) as total_purchase')
            )
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('purchase_receipts.branch_id', $branchId);
            })
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_purchase')
            ->take(5)
            ->get();

        // ======================== TOP 5 CUSTOMERS BY SALE AMOUNT ========================
        $customerQuery = DB::table('customers')
            ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'customers.id',
                'customers.name',
                DB::raw('COALESCE(SUM(sales.total), 0) as total_sale')
            )
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('sales.branch_id', $branchId);
            })
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_sale')
            ->take(5)
            ->get();

        // ======================== RECENT TRANSACTIONS (Sale & Purchase Payments) ========================
        $recentSalePayments = DB::table('sale_payments')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
            ->select(
                'sale_payments.id',
                'sale_payments.paid_at as date',
                'sale_payments.payment_type as type',
                'sale_payments.amount as paid',
                'sales.due_amount as due',
                'sales.total as total',
                DB::raw("'Sale' as source")
            )
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('sales.branch_id', $branchId);
            });

        $recentPurchasePayments = DB::table('purchase_payments')
            ->leftJoin('purchase_orders', 'purchase_payments.purchase_order_id', '=', 'purchase_orders.id')
            ->select(
                'purchase_payments.id',
                'purchase_payments.payment_date as date',
                'purchase_payments.method as type',
                'purchase_payments.amount as paid',
                'purchase_orders.outstanding_amount as due',
                'purchase_orders.total_amount as total',
                DB::raw("'Purchase' as source")
            )
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('purchase_payments.branch_id', $branchId);
            });

        $recentTransactions = $recentSalePayments->union($recentPurchasePayments)
            ->orderByDesc('date')
            ->take(5)
            ->get();

            $companySettings = WebsiteSetting::first();

        return view('backend.modules.dashboard.home', compact(
            'todaySales', 'grossSales', 'totalPurchase', 'totalOrders', 'totalExpense', 'netIncome',
            'totalPurchaseAndExpense',
            'monthlyIncome', 'monthlyExpense', 'monthNames',
            'weeklyIncome', 'weeklyExpense', 'weeklyLabels',
            'dailyIncome', 'dailyExpense', 'dailyLabels',
            'yearlyIncome', 'yearlyExpense', 'yearlyLabels',
            'users',
            'supplierQuery',
            'customerQuery',
            'overallReportData',
            'purchaseSalesMetrics',
            'purchaseSaleChartData',
            'recentTransactions',
            'companySettings'
        ));
    }
    // public function home2()
    // {
    //     // Handle the request and return a view
    //     return view('backend.modules.dashboard.home2');
    // }
}
