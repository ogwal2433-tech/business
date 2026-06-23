<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\BusinessSnapshot;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\AdminExpense;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        if (!$user->planHasFeature('advanced_analytics')) {
            return redirect()->route('admin.subscription.my')
                ->with('error', __('Advanced analytics are not available on your current plan.'));
        }

        // Daily sales for last 30 days
        $dailySales = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $daySales = Sale::where('admin_id', $user->id)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $dailySales[] = [
                'date' => $date->format('M d'),
                'total' => (float) $daySales,
            ];
        }

        // Monthly sales for last 12 months
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthSales = Sale::where('admin_id', $user->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            $monthlySales[] = [
                'month' => $date->format('M Y'),
                'total' => (float) $monthSales,
            ];
        }

        // Expense breakdown by category
        $expenseCategories = Expense::whereHas('employee', function ($q) use ($user) {
                $q->where('admin_id', $user->id);
            })
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $adminExpenseCategories = AdminExpense::where('admin_id', $user->id)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        foreach ($adminExpenseCategories as $cat => $total) {
            $expenseCategories[$cat] = ($expenseCategories[$cat] ?? 0) + $total;
        }

        // Monthly sales vs expenses (for bar chart)
        $monthlyComparison = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $sales = Sale::where('admin_id', $user->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            $exp = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $user->id))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $adminExp = AdminExpense::where('admin_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
            $monthlyComparison[] = [
                'month' => $date->format('M Y'),
                'sales' => (float) $sales,
                'expenses' => (float) ($exp + $adminExp),
            ];
        }

        // Top selling products
        $topProducts = Sale::where('admin_id', $user->id)
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_amount) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->with('product')
            ->get();

        // Sales by employee
        $salesByEmployee = Sale::where('admin_id', $user->id)
            ->selectRaw('employee_id, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('employee_id')
            ->with('employee')
            ->orderByDesc('total')
            ->get();

        return view('admin.analytics', compact(
            'dailySales',
            'monthlySales',
            'expenseCategories',
            'monthlyComparison',
            'topProducts',
            'salesByEmployee'
        ));
    }

    public function financialPosition()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) abort(403);

        if (!$user->planHasFeature('financial_position')) {
            return redirect()->route('admin.subscription.my')
                ->with('error', __('Financial Position is not available on your current plan.'));
        }

        $adminId = $user->id;
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $prevMonthStart = $now->copy()->subMonth()->startOfMonth()->toDateString();
        $prevMonthEnd = $now->copy()->subMonth()->endOfMonth()->toDateString();

        // ── Assets ──
        $inventoryValue = Inventory::where('admin_id', $adminId)->get()
            ->sum(function ($p) {
                $bulk = $p->purchase_price_bulk ?? 0;
                $unit = $p->purchase_price ?? 0;
                return in_array(strtolower($p->unit), ['dozen', 'carton'])
                    ? $p->quantity * $bulk
                    : $p->quantity * $unit;
            });

        $outstandingCredit = Sale::where('admin_id', $adminId)
            ->where('status', 'credit')
            ->whereColumn('amount_paid', '<', 'total_amount')
            ->select(DB::raw('SUM(total_amount - amount_paid) as total'))
            ->value('total') ?? 0;

        // ── All-Time Totals ──
        $allTimeSales = Sale::where('admin_id', $adminId)->sum('total_amount');

        $employeeExpensesTotal = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))->sum('amount');
        $adminExpensesTotal = AdminExpense::where('admin_id', $adminId)->sum('amount');
        $allTimeExpenses = $employeeExpensesTotal + $adminExpensesTotal;

        // ── This Month ──
        $monthSales = Sale::where('admin_id', $adminId)
            ->whereDate('created_at', '>=', $monthStart)->sum('total_amount');

        $monthEmpExp = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
            ->whereDate('date', '>=', $monthStart)->sum('amount');
        $monthAdmExp = AdminExpense::where('admin_id', $adminId)
            ->whereDate('date', '>=', $monthStart)->sum('amount');
        $monthExpenses = $monthEmpExp + $monthAdmExp;

        $netCashFlow = $monthSales - $monthExpenses;
        $estNetWorth = $inventoryValue + $allTimeSales - $allTimeExpenses;

        // ── Previous Month (for growth) ──
        $prevSales = Sale::where('admin_id', $adminId)
            ->whereDate('created_at', '>=', $prevMonthStart)
            ->whereDate('created_at', '<=', $prevMonthEnd)->sum('total_amount');

        $prevEmpExp = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
            ->whereDate('date', '>=', $prevMonthStart)->whereDate('date', '<=', $prevMonthEnd)->sum('amount');
        $prevAdmExp = AdminExpense::where('admin_id', $adminId)
            ->whereDate('date', '>=', $prevMonthStart)->whereDate('date', '<=', $prevMonthEnd)->sum('amount');
        $prevExpenses = $prevEmpExp + $prevAdmExp;

        $salesGrowth = $prevSales > 0 ? round(($monthSales - $prevSales) / $prevSales * 100, 1) : 0;
        $expenseGrowth = $prevExpenses > 0 ? round(($monthExpenses - $prevExpenses) / $prevExpenses * 100, 1) : 0;
        $profitMargin = $monthSales > 0 ? round(($monthSales - $monthExpenses) / $monthSales * 100, 1) : 0;

        // ── Top Product ──
        $topProduct = Sale::where('admin_id', $adminId)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_amount) as total_rev'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->first();

        // ── Top Employee ──
        $topEmployee = Sale::where('admin_id', $adminId)
            ->whereNotNull('employee_id')
            ->select('employee_id', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as txns'))
            ->groupBy('employee_id')
            ->orderByDesc('total')
            ->with('employee')
            ->first();

        // ── Monthly Breakdown (last 6 months) ──
        $monthlyBreakdown = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $start = $date->copy()->startOfMonth()->toDateString();
            $end = $date->copy()->endOfMonth()->toDateString();

            $sales = Sale::where('admin_id', $adminId)
                ->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)
                ->sum('total_amount');

            $empExp = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
                ->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->sum('amount');
            $admExp = AdminExpense::where('admin_id', $adminId)
                ->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->sum('amount');
            $expenses = $empExp + $admExp;

            $monthlyBreakdown[] = [
                'month' => $date->format('M Y'),
                'sales' => (float) $sales,
                'expenses' => (float) $expenses,
                'profit' => (float) ($sales - $expenses),
                'margin' => $sales > 0 ? round(($sales - $expenses) / $sales * 100, 1) : 0,
            ];
        }

        // ── Net Worth History (from snapshots, fallback to on-the-fly) ──
        $snapshots = BusinessSnapshot::where('admin_id', $adminId)
            ->where('snapshot_date', '>=', now()->subMonths(6)->startOfMonth()->toDateString())
            ->orderBy('snapshot_date')
            ->get();

        $hasRealSnapshots = $snapshots->contains(fn($s) => $s->net_worth > 0);

        if ($hasRealSnapshots && $snapshots->count() >= 2) {
            $netWorthHistory = $snapshots->map(function ($s) {
                return [
                    'month' => $s->snapshot_date->format('M Y'),
                    'netWorth' => (float) $s->net_worth,
                    'profit' => 0,
                ];
            })->toArray();
            for ($i = 1; $i < count($netWorthHistory); $i++) {
                $invDiff = (float) $snapshots[$i]->inventory_value - (float) $snapshots[$i - 1]->inventory_value;
                $salesDelta = (float) $snapshots[$i]->cumulative_sales - (float) $snapshots[$i - 1]->cumulative_sales;
                $expDelta = (float) $snapshots[$i]->cumulative_expenses - (float) $snapshots[$i - 1]->cumulative_expenses;
                $netWorthHistory[$i]['profit'] = round($invDiff + $salesDelta - $expDelta, 2);
            }
            $netWorthHistory[0]['profit'] = 0;
        } else {
            // Fallback: on-the-fly estimation
            $netWorthHistory = [];
            $sixMonthCumProfit = array_sum(array_column($monthlyBreakdown, 'profit'));
            $openingNetWorth = $estNetWorth - $sixMonthCumProfit;
            $cumulativeNW = 0;
            foreach ($monthlyBreakdown as $row) {
                $cumulativeNW += $row['profit'];
                $netWorthHistory[] = [
                    'month' => $row['month'],
                    'netWorth' => round($openingNetWorth + $cumulativeNW, 2),
                    'profit' => $row['profit'],
                ];
            }
        }

        // ── Sales & Expense Trends (avg last 3 months vs avg first 3 months) ──
        $salesVals = array_column($monthlyBreakdown, 'sales');
        $expVals = array_column($monthlyBreakdown, 'expenses');
        $avgRecentSales = count($salesVals) >= 3 ? array_sum(array_slice($salesVals, 3)) / 3 : ($salesVals[count($salesVals)-1] ?? 0);
        $avgOlderSales = count($salesVals) >= 3 ? array_sum(array_slice($salesVals, 0, 3)) / 3 : ($salesVals[0] ?? 1);
        $salesTrendPct = $avgOlderSales > 0 ? round(($avgRecentSales - $avgOlderSales) / $avgOlderSales * 100, 1) : 0;

        $avgRecentExp = count($expVals) >= 3 ? array_sum(array_slice($expVals, 3)) / 3 : ($expVals[count($expVals)-1] ?? 0);
        $avgOlderExp = count($expVals) >= 3 ? array_sum(array_slice($expVals, 0, 3)) / 3 : ($expVals[0] ?? 1);
        $expTrendPct = $avgOlderExp > 0 ? round(($avgRecentExp - $avgOlderExp) / $avgOlderExp * 100, 1) : 0;

        // ── Net Worth Growth over 6 months ──
        $netWorthGrowth = $netWorthHistory[0]['netWorth'] != 0
            ? round(($netWorthHistory[count($netWorthHistory)-1]['netWorth'] - $netWorthHistory[0]['netWorth']) / abs($netWorthHistory[0]['netWorth']) * 100, 1)
            : ($netWorthHistory[count($netWorthHistory)-1]['netWorth'] > 0 ? 100 : 0);

        // ── Business Progress Score (0-100) ──
        $scoreFromMargin = min(40, max(0, ($profitMargin / 25) * 40));
        $scoreFromSales = $salesTrendPct >= 0 ? min(35, 17.5 + ($salesTrendPct / 60) * 17.5) : max(0, 17.5 - (abs($salesTrendPct) / 60) * 17.5);
        $scoreFromExpenses = $expTrendPct <= 0 ? 25 : max(0, 25 - ($expTrendPct / 60) * 25);
        $progressScore = min(100, max(0, round($scoreFromMargin + $scoreFromSales + $scoreFromExpenses)));

        return view('admin.financial-position', compact(
            'inventoryValue', 'outstandingCredit', 'allTimeSales', 'allTimeExpenses',
            'monthSales', 'monthExpenses', 'netCashFlow', 'estNetWorth',
            'salesGrowth', 'expenseGrowth', 'profitMargin',
            'topProduct', 'topEmployee', 'monthlyBreakdown',
            'prevSales', 'netWorthHistory', 'netWorthGrowth',
            'salesTrendPct', 'expTrendPct', 'progressScore',
            'scoreFromMargin', 'scoreFromSales', 'scoreFromExpenses'
        ));
    }
}
