<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Sale;
use App\Models\InventoryHistory;
use App\Models\User;
use App\Models\Expense;

use Exception;
use App\Models\Inventory;
use App\Models\Repayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
public function create()
{
    $user = auth()->user();

    if ($user->role === 'admin') {
        // Admin sees only their own products
        $products = Inventory::where('quantity', '>', 0)
            ->where('admin_id', $user->id)
            ->get();

        return view('Admin.sale', compact('products'));

    } elseif ($user->role === 'employee') {
        // Employee sees products of their admin
        $products = Inventory::where('quantity', '>', 0)
            ->where('admin_id', $user->admin_id) // <- VERY IMPORTANT
            ->get();

        return view('employee.sales.create', compact('products'));

    } else {
        abort(403); // unauthorized
    }
}

public function history(Request $request)
{
    $user = Auth::user();

    $period = $request->get('period', 'today');
    $search = $request->get('search', '');

    // Base query depends on user role
    $query = Sale::with('product')->orderByDesc('created_at');

    if ($user->isAdmin()) {
        // Admin sees all sales under their admin ID
        $query->where('admin_id', $user->id);
    } else {
        // Employee sees only their own sales under their admin
        $query->where('admin_id', $user->admin_id)
              ->where('employee_id', $user->id);
    }

    // Filter by period
    if ($period === 'today') {
        $query->whereDate('created_at', now()->toDateString());
    } elseif ($period === 'week') {
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($period === 'month') {
        $query->whereYear('created_at', now()->year)
              ->whereMonth('created_at', now()->month);
    }

    // Filter by product search
    if ($search) {
        $query->whereHas('product', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('id', $search);
        });
    }

    $sales = $query->paginate(15);

    // Total sales for filtered query
    $totalSales = (clone $query)->sum('total_amount');

    // Most sold products with same filtering
    $mostSold = Sale::select('product_id')
        ->with('product')
        ->selectRaw('SUM(quantity) as total_quantity')
        ->groupBy('product_id')
        ->orderByDesc('total_quantity');

    if ($user->isAdmin()) {
        $mostSold->where('admin_id', $user->id);
    } else {
        $mostSold->where('admin_id', $user->admin_id)
                 ->where('employee_id', $user->id);
    }

    // Apply period filter to most sold products as well
    if ($period === 'today') {
        $mostSold->whereDate('created_at', now()->toDateString());
    } elseif ($period === 'week') {
        $mostSold->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($period === 'month') {
        $mostSold->whereYear('created_at', now()->year)
                 ->whereMonth('created_at', now()->month);
    }

    $mostSold = $mostSold->get()->map(function ($sale) {
        $sale->name = $sale->product->name ?? 'Unknown';
        return $sale;
    });

    return view('employee.sales.history', compact('sales', 'totalSales', 'mostSold'));
}
public function report(Request $request)
{
    $employee = auth()->user();
    $period = $request->input('period', 'daily');

    // Base query: only this employee's sales under their admin
    $query = Sale::where('employee_id', $employee->id)
                 ->where('admin_id', $employee->admin_id)
                 ->with('product')
                 ->orderByDesc('created_at');

    // Apply period filter
    switch ($period) {
        case 'daily':
            $date = $request->input('daily_date', now()->toDateString());
            $query->whereDate('created_at', $date);
            break;

        case 'weekly':
            $date = $request->input('weekly_date', now()->toDateString());
            $startOfWeek = \Carbon\Carbon::parse($date)->startOfWeek();
            $endOfWeek = \Carbon\Carbon::parse($date)->endOfWeek();
            $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            break;

        case 'monthly':
            $month = $request->input('monthly_month', now()->format('Y-m'));
            $year = substr($month, 0, 4);
            $monthNum = substr($month, 5, 2);
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $monthNum);
            break;

        case 'custom':
            $from = $request->input('from');
            $to = $request->input('to');
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            } else {
                return back()->with('error', 'Please provide both From and To dates for a custom period.');
            }
            break;
    }

    // Fetch sales
    $sales = $query->get();

    // Total sales amount for this employee in the selected period
    $totalSalesAmount = $sales->sum('total_amount');

    // Group by product for totals and most sold calculation
    $grouped = $sales->groupBy('product_id')->map(function ($items) {
        $product = $items->first()->product;
        return [
            'product_name'   => $product?->name ?? 'Unknown',
            'unit'           => $product?->unit ?? 'piece',
            'unit_price'     => $product?->price ?? 0,
            'total_quantity' => $items->sum('quantity'),
            'total_amount'   => $items->sum('total_amount'),
        ];
    });

    $mostSoldProducts = $grouped->sortByDesc('total_quantity')->take(5);

    $noSalesMessage = ($sales->isEmpty() && $period === 'custom')
        ? "No sales found for the selected date range: {$from} to {$to}."
        : null;

    return view('employee.sales.report', [
        'sales'            => $sales,
        'grouped'          => $grouped,
        'totalSalesAmount' => $totalSalesAmount,
        'mostSoldProducts' => $mostSoldProducts,
        'period'           => $period,
        'from'             => $from ?? '',
        'to'               => $to ?? '',
        'daily_date'       => $request->input('daily_date', now()->toDateString()),
        'weekly_date'      => $request->input('weekly_date', now()->toDateString()),
        'monthly_month'    => $request->input('monthly_month', now()->format('Y-m')),
        'noSalesMessage'   => $noSalesMessage,
    ]);
}
//sale report to admin by employee
public function salesReport(Request $request)
{
    $admin = auth()->user();

    if (! $admin->isAdmin()) {
        abort(403, 'Unauthorized access');
    }

    $search = $request->input('search', '');
    $period = $request->input('period', 'daily');
    $from = $request->input('from');
    $to = $request->input('to');
    $employeeId = $request->input('employee_id');
    $type = $request->input('type'); // 'admin', 'employee', or null

    // 1. Determine date range
    switch ($period) {
        case 'weekly':
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            break;

        case 'monthly':
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            break;

        case 'custom':
            $start = $from ? Carbon::parse($from)->startOfDay() : Carbon::today();
            $end = $to ? Carbon::parse($to)->endOfDay() : Carbon::today()->endOfDay();
            if ($end->lt($start)) [$start, $end] = [$end, $start];
            break;

        case 'daily':
        default:
            $start = Carbon::today();
            $end = Carbon::today()->endOfDay();
            break;
    }

    // 2. Get employees under this admin
    $employees = User::where('role', 'employee')
                     ->where('admin_id', $admin->id)
                     ->get();

    // 3. Build base query
    $baseQuery = Sale::with(['employee', 'user', 'product'])
                     ->where('admin_id', $admin->id)
                     ->whereBetween('created_at', [$start, $end])
                     ->when($search, fn($q) =>
                         $q->where(function($sub) use ($search) {
                             $sub->whereHas('employee', fn($q2) => $q2->where('name', 'like', "%$search%"))
                                 ->orWhereHas('product', fn($q2) => $q2->where('name', 'like', "%$search%"));
                         })
                     )
                     ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId));

    // 4. Separate admin (employee_id is null) and employee sales, respect type filter
    $adminQuery = (clone $baseQuery)->whereNull('employee_id');
    $employeeQuery = (clone $baseQuery)->whereNotNull('employee_id');

    if ($type === 'admin') {
        $adminSales = $adminQuery->get();
        $employeeSales = collect();
        $groupedByEmployee = collect();
        $mostSoldProducts = collect();
        $adminExpenses = collect();
    } elseif ($type === 'employee') {
        $adminSales = collect();
        $employeeSales = $employeeQuery->get();
    } else {
        $adminSales = $adminQuery->get();
        $employeeSales = $employeeQuery->get();
    }

    // 5. Group employee sales by employee → product
    if ($employeeSales->isNotEmpty()) {
        $groupedByEmployee = $employeeSales->groupBy(fn($sale) => $sale->employee?->name ?? __('Unknown'))
                                           ->map(fn($empSales) => [
                                               'products' => $empSales->groupBy(fn($sale) => $sale->product->name ?? __('Unknown Product'))
                                                                      ->map(fn($prodSales) => [
                                                                          'price' => $prodSales->first()->product->price ?? 0,
                                                                          'quantity_sold' => $prodSales->sum('quantity'),
                                                                          'total_sales' => $prodSales->sum('total_amount'),
                                                                      ]),
                                               'total_sales' => $empSales->sum('total_amount'),
                                               'total_quantity' => $empSales->sum('quantity'),
                                           ]);
    } else {
        $groupedByEmployee = collect();
    }

    // 6. All sales combined for overall totals
    $allSales = $type === 'admin' ? $adminSales : ($type === 'employee' ? $employeeSales : (clone $baseQuery)->get());
    $totalSalesAmount = $allSales->sum('total_amount');
    $totalQuantity = $allSales->sum('quantity');
    $adminTotalAmount = $adminSales->sum('total_amount');
    $employeeTotalAmount = $employeeSales->sum('total_amount');

    // 7. Top-selling products
    if ($allSales->isNotEmpty()) {
        $mostSoldProducts = $allSales->groupBy(fn($sale) => $sale->product->name ?? __('Unknown Product'))
                                     ->map(fn($group) => [
                                         'quantity_sold' => $group->sum('quantity'),
                                         'price' => $group->first()->product->price ?? 0,
                                         'total_sales' => $group->sum('total_amount'),
                                     ])
                                     ->sortByDesc('quantity_sold')
                                     ->take(5);
    } else {
        $mostSoldProducts = collect();
    }

    // 8. Admin expenses
    if ($type !== 'admin') {
        $adminExpensesQuery = $admin->employeeExpenses()
                                    ->with('employee')
                                    ->when($search, fn($q) =>
                                        $q->whereHas('employee', fn($q2) => $q2->where('name', 'like', "%$search%"))
                                          ->orWhere('title', 'like', "%$search%")
                                          ->orWhere('category', 'like', "%$search%")
                                    )
                                    ->latest();
        $adminExpenses = $adminExpensesQuery->paginate(10);
    } else {
        $adminExpenses = collect();
    }

    return view('admin.report', compact(
        'allSales', 'adminSales', 'employeeSales', 'employees', 'groupedByEmployee', 'mostSoldProducts',
        'totalSalesAmount', 'totalQuantity', 'adminTotalAmount', 'employeeTotalAmount',
        'adminExpenses', 'period', 'from', 'to', 'search', 'employeeId', 'type'
    ) + ['validated' => [
        'admin_id' => $admin->id,
        'start_date' => $start->toDateString(),
        'end_date' => $end->toDateString(),
    ]]);
}

//credit

public function salesReports(Request $request)
{
    $user = Auth::user();

    // Restrict to admin users only
    abort_unless($user->isAdmin(), 403, 'Unauthorized access');

    // Vali te optional date filter
    $request->validate([
        'date' => 'nullable|date',
    ]);

    $date = $request->input('date');

    // Query: sales made by admin personally (no employee involved)
    $query = Sale::where('admin_id', $user->id)
                 ->whereNull('employee_id');

    // Optional date filter
    if ($date) {
        $query->whereDate('created_at', $date);
    }

    // Get sales for display
    $sales = $query->with(['product', 'user'])
                   ->orderBy('created_at', 'desc')
                   ->paginate(15);

    // Totals
    $totalAmount = (clone $query)->sum('total_amount');
    $totalQuantity = (clone $query)->sum('quantity');

    // Daily and Monthly summaries
    $today = now()->startOfDay();
    $startOfMonth = now()->startOfMonth();

    $adminSalesToday = Sale::where('admin_id', $user->id)
                           ->whereNull('employee_id')
                           ->where('created_at', '>=', $today)
                           ->sum('total_amount');

    $adminMonthlySales = Sale::where('admin_id', $user->id)
                              ->whereNull('employee_id')
                              ->where('created_at', '>=', $startOfMonth)
                              ->sum('total_amount');

    // Filters for UI (to retain date in input)
    $filters = ['date' => $date];

    return view('adminR', compact(
        'sales',
        'totalAmount',
        'totalQuantity',
        'filters',
        'adminSalesToday',
        'adminMonthlySales'
    ));
}

public function storesales(Request $request)
{
    // 1. Validation
    try {
        $data = $request->validate([
            'product_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|in:piece,dozen,carton',
            'amount_sold' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'total_pieces_value' => 'required|integer|min:1',
            'price_value' => 'required|numeric|min:0',
            'amount_display' => 'nullable|string',
            'status' => 'nullable|in:paid,credit,pending',
            'client_name' => 'nullable|string|max:255',
            'full_total' => 'nullable|numeric|min:0',
            'balance_left' => 'nullable|numeric|min:0',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }

    // 2. Type casting
    $data['product_id'] = (int) $data['product_id'];
    $data['quantity'] = (int) $data['quantity'];
    $data['total_pieces_value'] = (int) $data['total_pieces_value'];
    $data['amount_sold'] = (float) $data['amount_sold'];
    $data['price_value'] = (float) $data['price_value'];
    $data['discount'] = isset($data['discount']) ? (float) $data['discount'] : 0.0;
    $data['full_total'] = isset($data['full_total']) ? (float) $data['full_total'] : $data['amount_sold'];

    // 2b. Validate credit-specific rules
    $isCredit = ($data['status'] ?? 'paid') === 'credit';
    if ($isCredit) {
        if (empty($data['client_name'])) {
            return back()->withInput()->with('error', __('Client name is required for credit sales.'));
        }
        if ($data['amount_sold'] > $data['full_total']) {
            return back()->withInput()->with('error', __('Deposit cannot exceed the total price.'));
        }
    }

    // 3. Sanitize display amount
    $amountDisplay = $request->input('amount_display');
    $amountDisplay = $amountDisplay ? (float) str_replace(',', '', $amountDisplay) : 0.0;

    // 4. Compare display vs actual
    if (abs($data['amount_sold'] - $amountDisplay) > 0.01) {
        return back()->withInput()->with('error', 'Displayed amount does not match actual sold amount.');
    }

    DB::beginTransaction();

    try {
        // 5. Fetch product inventory
        $product = Inventory::findOrFail($data['product_id']);

        // 6. Check stock availability
        if ($data['total_pieces_value'] > $product->quantity) {
            return back()->withInput()->with('error', 'Not enough stock. Only ' . $product->quantity . ' pieces available.');
        }

        // 7. Get user/admin info
        $user = auth()->user();
        $adminId = $user->isAdmin() ? $user->id : $user->admin_id;
        $previousStock = $product->quantity;

        // 8. Update inventory
        $product->quantity -= $data['total_pieces_value'];
        $product->save();

        $isCredit = ($data['status'] ?? 'paid') === 'credit';

        // 9. Create sale record
        $sale = Sale::create([
            'employee_id' => $user->isAdmin() ? null : $user->id,
            'user_id' => $user->id,
            'admin_id' => $adminId,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'pieces_sold' => $data['total_pieces_value'],
            'price_per_piece' => $data['price_value'],
            'discount' => $data['discount'],
            'total_amount' => $data['full_total'],
            'amount_paid' => $data['amount_sold'],
            'status' => $isCredit ? 'credit' : 'paid',
            'client_name' => $data['client_name'] ?? null,
        ]);

        // 10. Create inventory history log (with current prices)
        $history = InventoryHistory::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'decrease',
            'quantity' => $data['total_pieces_value'],
            'previous_quantity' => $previousStock,
            'new_quantity' => $product->quantity,
            'purchase_price' => $product->purchase_price,
            'purchase_price_bulk' => $product->purchase_price_bulk,
            'selling_price' => $product->price,
            'selling_price_bulk' => $product->selling_price_bulk,
            'note' => "Sold {$data['quantity']} {$data['unit']}(s) by {$user->name} ({$data['total_pieces_value']} pieces)",
        ]);
        DB::commit();

        $businessName = $user->business_name;
        $balanceLeft = max(0, $data['full_total'] - $data['amount_sold']);
        session([
            'receipt' => [
                'business_name' => $businessName,
                'product' => $product->name,
                'unit' => $data['unit'],
                'price' => $data['price_value'],
                'total_pieces' => $data['total_pieces_value'],
                'amount' => $data['amount_sold'],
                'date' => now()->toDateString(),
                'status' => $isCredit ? 'credit' : 'paid',
                'client_name' => $data['client_name'] ?? null,
                'balance' => $balanceLeft,
            ]
        ]);
        $successMsg = $isCredit
            ? __('Credit sale recorded successfully for :name.', ['name' => $data['client_name'] ?? 'client'])
            : __('Sale recorded successfully.');
        return redirect()->back()->with('success', $successMsg);

    } catch (\Exception $e) {
    DB::rollBack();

    Log::error('Sale transaction failed', [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
        'trace'   => $e->getTraceAsString(),
    ]);

    return back()->withInput()->with(
        'error',
        'Error: ' . $e->getMessage()
    );
}
}

public function creditSales()
{
    $user = Auth::user();
    abort_unless($user->isAdmin(), 403);

    if (!$user->planHasFeature('credit_sales')) {
        return redirect()->route('admin.subscription.my')
            ->with('error', __('Credit sales are not available on your current plan.'));
    }

    $creditSales = Sale::with(['product', 'repayments'])
        ->where('admin_id', $user->id)
        ->where('status', 'credit')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('credit', compact('creditSales'));
}

public function recordRepayment(Request $request)
{
    // Strip commas for safe numeric parsing
    $amount = (float) str_replace(',', '', $request->input('amount', '0'));

    $request->validate([
        'sale_id' => 'required|exists:sales,id',
        'amount' => 'required|numeric|min:1',
        'next_installment_date' => 'required|date',
        'note' => 'nullable|string|max:1000',
    ]);

    $user = Auth::user();

    DB::beginTransaction();
    try {
        $sale = Sale::where('id', $request->sale_id)
            ->where('admin_id', $user->id)
            ->lockForUpdate()
            ->firstOrFail();

        $remaining = $sale->total_amount - $sale->amount_paid;
        if ($amount > $remaining) {
            return back()->with('error', currency_label('Repayment amount exceeds remaining balance of UGX :balance.', ['balance' => number_format($remaining)]));
        }

        Repayment::create([
            'sale_id' => $sale->id,
            'amount' => $amount,
            'next_installment_date' => $request->next_installment_date,
            'note' => $request->note,
        ]);

        $sale->amount_paid += $amount;
        $sale->save();

        if ($sale->amount_paid >= $sale->total_amount) {
            $sale->update(['status' => 'paid']);
        }

        DB::commit();
        return redirect()->back()->with('success', currency_label('Repayment of UGX :amount recorded successfully.', ['amount' => number_format($amount)]));
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Repayment failed: ' . $e->getMessage());
    }
}

public function updateNextInstallment(Request $request, $id)
{
    $request->validate([
        'next_installment_date' => 'required|date',
    ]);

    $sale = Sale::findOrFail($id);
    $user = Auth::user();
    abort_unless($user->isAdmin() && $sale->admin_id === $user->id, 403);

    $latestRepayment = $sale->repayments()->latest()->first();
    if ($latestRepayment) {
        $latestRepayment->update(['next_installment_date' => $request->next_installment_date]);
    }

    return redirect()->back()->with('success', __('Next installment date updated.'));
}

public function markAsReturned($id)
{
    $user = Auth::user();

    DB::beginTransaction();
    try {
        $sale = Sale::where('id', $id)
            ->where('admin_id', $user->id)
            ->lockForUpdate()
            ->firstOrFail();

        $sale->update(['status' => 'returned']);

        $product = Inventory::findOrFail($sale->product_id);
        $product->quantity += $sale->pieces_sold;
        $product->save();

        DB::commit();
        return redirect()->back()->with('success', __('Sale marked as returned. Stock restored.'));
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', __('Failed to mark as returned: :error', ['error' => $e->getMessage()]));
    }
}

}




