<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\AdminExpense;
use App\Models\Expense;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function tot()
    {
        return view('chat.tot');
    }

public function dashboard()
{
    $user = auth()->user();

    $totalSalesToday = 0;
    $totalMonthlySales = 0;
    $totalSalesByEmployee = 0;
    $totalEmployees = 0;
    $dailyAdminSales = [];
    $dailyEmployeeSales = [];

    if ($user->isAdmin()) {
        $adminId = $user->id;

        $totalSalesToday = Sale::where('admin_id', $adminId)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $todayAdminSales = Sale::where('admin_id', $adminId)->whereNull('employee_id')
            ->whereDate('created_at', today())->sum('total_amount');
        $todayEmployeeSales = $totalSalesToday - $todayAdminSales;

        $totalMonthlySales = Sale::where('admin_id', $adminId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $monthAdminSales = Sale::where('admin_id', $adminId)->whereNull('employee_id')
            ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_amount');
        $monthEmployeeSales = $totalMonthlySales - $monthAdminSales;

        $totalEmployees = User::where('admin_id', $adminId)
            ->where('role', 'employee')->count();
    } else {
        $totalSalesToday = $user->sales()
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $totalMonthlySales = $user->sales()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $totalSalesByEmployee = $user->sales()->sum('total_amount');
        $todayAdminSales = 0;
        $todayEmployeeSales = 0;
        $monthAdminSales = 0;
        $monthEmployeeSales = 0;
    }

    $lowStockProducts = Inventory::where('quantity', '<', 5)
        ->when($user->isAdmin(), function ($query) use ($user) {
            $query->where('admin_id', $user->id);
        })
        ->get();

    // --- Chart data ---
    $dailySales = [];
    $dailyAdminSales = [];
    $dailyEmployeeSales = [];

    if ($user->isAdmin()) {
        $adminId = $user->id;

        // Daily sales for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $adminDay = Sale::where('admin_id', $adminId)->whereNull('employee_id')
                ->whereDate('created_at', $date)->sum('total_amount');
            $empDay = Sale::where('admin_id', $adminId)->whereNotNull('employee_id')
                ->whereDate('created_at', $date)->sum('total_amount');

            $label = $date->format('M d');
            $dailySales[] = ['date' => $label, 'total' => (float) ($adminDay + $empDay)];
            $dailyAdminSales[] = ['date' => $label, 'total' => (float) $adminDay];
            $dailyEmployeeSales[] = ['date' => $label, 'total' => (float) $empDay];
        }

        // Expense breakdown by category
        $expenseCategories = Expense::whereHas('employee', function ($q) use ($adminId) {
                $q->where('admin_id', $adminId);
            })
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $adminExpenseCategories = AdminExpense::where('admin_id', $adminId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        foreach ($adminExpenseCategories as $cat => $total) {
            $expenseCategories[$cat] = ($expenseCategories[$cat] ?? 0) + $total;
        }

        // Total expenses this month
        $monthEmployeeExpenses = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $monthAdminExpenses = AdminExpense::where('admin_id', $adminId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        $totalMonthlyExpenses = $monthEmployeeExpenses + $monthAdminExpenses;
    } else {
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $daySales = $user->sales()->whereDate('created_at', $date)->sum('total_amount');
            $label = $date->format('M d');
            $dailySales[] = ['date' => $label, 'total' => (float) $daySales];
        }
        $expenseCategories = [];
        $totalMonthlyExpenses = 0;
        $monthAdminExpenses = 0;
        $monthEmployeeExpenses = 0;
    }

    $netProfit = $totalMonthlySales - $totalMonthlyExpenses;

    return view('admin.Dashboard', compact(
        'totalSalesToday', 'todayAdminSales', 'todayEmployeeSales',
        'totalMonthlySales', 'monthAdminSales', 'monthEmployeeSales',
        'totalSalesByEmployee',
        'totalEmployees',
        'lowStockProducts',
        'dailySales',
        'dailyAdminSales',
        'dailyEmployeeSales',
        'expenseCategories',
        'totalMonthlyExpenses',
        'monthAdminExpenses',
        'monthEmployeeExpenses',
        'netProfit'
    ));
}

    public function salesReport()
    {
        $sales = Sale::with('user')->latest()->get();
        return view('Admin.admin.sales.report', compact('sales'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = ($user->status == 'active') ? 'inactive' : 'active';
        $user->save();

        return redirect()->route('admin.employees.index')->with('success', __('Employee status updated'));
    }

public function showPurchaseForm()
{
    return view('admin.stocks.purchase');
}

public function storePurchase(Request $request)
{
    $request->validate([
        'product_name.*' => 'required|string|max:255',
        'quantity.*' => 'required|integer|min:1',
        'price_per_unit.*' => 'required|numeric|min:0',
        'purchase_date.*' => 'required|date',
        'notes' => 'nullable|string',
    ]);

    foreach ($request->product_name as $index => $name) {
        Purchase::create([
            'user_id' => auth()->id(),
            'product_name' => $name,
            'quantity' => $request->quantity[$index],
            'price_per_unit' => $request->price_per_unit[$index],
            'purchase_date' => $request->purchase_date[$index],
            'notes' => $request->notes,
        ]);
    }

    return redirect()->back()->with('success', __('Stock purchases recorded successfully.'));
}

public function index()
{
    $userId = Auth::id();

    $purchases = \App\Models\Purchase::where('user_id', $userId)
        ->orderBy('purchase_date', 'desc')
        ->get();

    // Group by formatted date
    $groupedPurchases = $purchases->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->purchase_date)->format('F j, Y');
    });

    $overallTotal = $purchases->sum(function ($p) {
        return $p->quantity * $p->price_per_unit;
    });

    return view('Admin.stocks.index', compact('purchases', 'groupedPurchases', 'overallTotal'));
}
public function cost(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'category' => 'required|string|max:100',
        'date' => 'required|date',
        'description' => 'nullable|string|max:1000',
    ]);

    $adminId = Auth::id();

    // Create AdminExpense
    AdminExpense::create([
        ...$validated,
        'admin_id' => $adminId,
    ]);

    return redirect()->back()
                     ->with('success', __('Expense recorded successfully.'));
}
 public function indexs()
    {
        $admin = Auth::user();

        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Employee expenses under this admin
        $expenses = Expense::with('employee')
            ->whereHas('employee', function ($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })
            ->latest()
            ->paginate(10);

        // Admin's own expenses
        $adminExpenses = AdminExpense::where('admin_id', $admin->id)
            ->orderBy('date', 'desc')
            ->paginate(10, ['*'], 'admin_page');

        return view('expenses.index', compact('expenses', 'adminExpenses'));
    }
}
