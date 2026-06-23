<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\AdminExpense;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\User;
use App\Models\BusinessSubscription;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class RealtimeController extends Controller
{
    public function stockCheck($id)
    {
        $product = Inventory::findOrFail($id);
        return response()->json([
            'quantity' => (int) $product->quantity,
            'name' => $product->name,
        ]);
    }

    public function adminDashboardStats()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) return response()->json([], 403);

        $adminId = $user->id;

        $totalSalesToday = Sale::where('admin_id', $adminId)
            ->whereDate('created_at', today())->sum('total_amount');

        $todayAdminSales = Sale::where('admin_id', $adminId)->whereNull('employee_id')
            ->whereDate('created_at', today())->sum('total_amount');

        $totalMonthlySales = Sale::where('admin_id', $adminId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->sum('total_amount');

        $monthAdminSales = Sale::where('admin_id', $adminId)->whereNull('employee_id')
            ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_amount');

        $monthEmployeeExpenses = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->sum('amount');

        $monthAdminExpenses = AdminExpense::where('admin_id', $adminId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)->sum('amount');

        $totalMonthlyExpenses = $monthEmployeeExpenses + $monthAdminExpenses;

        $lowStockCount = Inventory::where('admin_id', $adminId)->where('quantity', '<', 5)->count();
        $totalEmployees = User::where('admin_id', $adminId)->where('role', 'employee')->count();

        return response()->json([
            'totalSalesToday' => (float) $totalSalesToday,
            'todayAdminSales' => (float) $todayAdminSales,
            'todayEmployeeSales' => (float) ($totalSalesToday - $todayAdminSales),
            'totalMonthlySales' => (float) $totalMonthlySales,
            'monthAdminSales' => (float) $monthAdminSales,
            'monthEmployeeSales' => (float) ($totalMonthlySales - $monthAdminSales),
            'totalMonthlyExpenses' => (float) $totalMonthlyExpenses,
            'monthAdminExpenses' => (float) $monthAdminExpenses,
            'monthEmployeeExpenses' => (float) $monthEmployeeExpenses,
            'netProfit' => (float) ($totalMonthlySales - $totalMonthlyExpenses),
            'lowStockCount' => $lowStockCount,
            'totalEmployees' => $totalEmployees,
        ]);
    }

    public function adminDashboardCharts()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) return response()->json([], 403);

        $adminId = $user->id;
        $dailyAdminSales = [];
        $dailyEmployeeSales = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $adminDay = Sale::where('admin_id', $adminId)->whereNull('employee_id')
                ->whereDate('created_at', $date)->sum('total_amount');
            $empDay = Sale::where('admin_id', $adminId)->whereNotNull('employee_id')
                ->whereDate('created_at', $date)->sum('total_amount');
            $label = $date->format('M d');
            $dailyAdminSales[] = ['date' => $label, 'total' => (float) $adminDay];
            $dailyEmployeeSales[] = ['date' => $label, 'total' => (float) $empDay];
        }

        $expenseCategories = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')->pluck('total', 'category')->toArray();

        $adminExpenseCategories = AdminExpense::where('admin_id', $adminId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')->pluck('total', 'category')->toArray();

        foreach ($adminExpenseCategories as $cat => $total) {
            $expenseCategories[$cat] = ($expenseCategories[$cat] ?? 0) + $total;
        }

        $lowStockProducts = Inventory::where('admin_id', $adminId)
            ->where('quantity', '<', 5)->get(['id', 'name', 'quantity']);

        return response()->json([
            'dailyAdminSales' => $dailyAdminSales,
            'dailyEmployeeSales' => $dailyEmployeeSales,
            'expenseCategories' => $expenseCategories,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function employeeDashboardStats()
    {
        $user = auth()->user();
        if (!$user->isEmployee()) return response()->json([], 403);

        $todaySales = Sale::where('employee_id', $user->id)
            ->whereDate('created_at', today())->sum('total_amount');

        $monthlySales = Sale::where('employee_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->sum('total_amount');

        $totalProductsSold = Sale::where('employee_id', $user->id)->sum('quantity');

        $recentSales = Sale::with('product')
            ->where('employee_id', $user->id)
            ->latest()->take(5)
            ->get(['id', 'product_id', 'quantity', 'total_amount', 'created_at']);

        return response()->json([
            'todaySales' => (float) $todaySales,
            'monthlySales' => (float) $monthlySales,
            'totalProductsSold' => (int) $totalProductsSold,
            'recentSales' => $recentSales->map(fn($s) => [
                'id' => $s->id,
                'product' => $s->product?->name ?? '-',
                'quantity' => $s->quantity,
                'total_amount' => (float) $s->total_amount,
                'created_at' => $s->created_at->format('H:i'),
            ]),
        ]);
    }

    public function systemAdminDashboardStats()
    {
        return response()->json([
            'totalBusinesses' => User::where('role', 'admin')->count(),
            'totalEmployees' => User::where('role', 'employee')->count(),
            'activeSubscriptions' => BusinessSubscription::whereIn('status', ['active', 'trial'])->count(),
            'pendingSubscriptions' => BusinessSubscription::where('status', 'pending')->count(),
            'expiredSubscriptions' => BusinessSubscription::where('status', 'expired')->count(),
            'totalRevenue' => (float) PaymentTransaction::where('status', 'paid')->sum('amount'),
        ]);
    }

    public function newMessages(Request $request)
    {
        $user = auth()->user();
        $since = $request->input('since');

        $query = \App\Models\Message::with('user')->latest();

        if ($user->isAdmin()) {
            $query->where('deleted_for_admin', false)->where('status', '!=', 'pending');
        } else {
            $query->where('user_id', $user->id)->where('deleted_for_user', false);
        }

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $messages = $query->take(20)->get();

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'user' => $m->user?->name ?? '-',
                'message' => $m->admin_message ?? $m->message ?? '-',
                'admin_reply' => $m->admin_reply,
                'status' => $m->status,
                'created_at' => $m->created_at->format('H:i'),
                'time_ago' => $m->created_at->diffForHumans(),
            ]),
            'count' => $messages->count(),
        ]);
    }

    public function inventoryStats()
    {
        $user = auth()->user();
        $query = Inventory::query();
        if ($user->isAdmin()) {
            $query->where('admin_id', $user->id);
        }

        $total = $query->count();
        $inStock = (clone $query)->where('quantity', '>', 5)->count();
        $lowStock = (clone $query)->where('quantity', '>', 0)->where('quantity', '<=', 5)->count();
        $outOfStock = (clone $query)->where('quantity', '<=', 0)->count();

        return response()->json(compact('total', 'inStock', 'lowStock', 'outOfStock'));
    }

    public function creditSalesUpdates(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) return response()->json([], 403);

        $since = $request->input('since');

        $query = Sale::with(['product', 'repayments'])
            ->where('admin_id', $user->id)
            ->where('status', 'credit');

        if ($since) {
            $query->where('updated_at', '>', $since);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'sales' => $sales->map(fn($s) => [
                'id' => $s->id,
                'product' => $s->product?->name ?? '-',
                'client_name' => $s->client_name,
                'quantity' => $s->quantity,
                'total_amount' => (float) $s->total_amount,
                'paid_amount' => (float) $s->paid_amount,
                'balance' => (float) ($s->total_amount - $s->paid_amount),
                'status' => $s->status,
                'updated_at' => $s->updated_at->toISOString(),
            ]),
            'count' => $sales->count(),
        ]);
    }
}
