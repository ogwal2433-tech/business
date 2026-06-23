<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\sale;
use App\Models\InventoryHistory;
class ProductController extends Controller
{
    /**
     * Display a paginated list of products owned by the authenticated admin.
     */
    public function index()
    {
        $products = Product::where('admin_id', auth()->id())
                           ->orderBy('name')
                           ->paginate(10);

        return view('inventory.list', compact('products'));
    }

    /**
     * Show the form for editing the specified product if owned by the authenticated admin.
     */
    public function edit($id)
    {
        $product = Product::where('id', $id)
                          ->where('admin_id', auth()->id())
                          ->firstOrFail();

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product after validating and ensuring ownership.
     */
    // public function update(Request $request, $id)
    // {
    //     $product = Product::where('id', $id)
    //                       ->where('admin_id', auth()->id())
    //                       ->firstOrFail();

    //     $validated = $request->validate([
    //         'sku' => 'required|string|max:255',
    //         'name' => 'required|string|max:255',
    //         'quantity' => 'required|integer|min:0',
    //         'purchase_price' => 'required|numeric|min:0',
    //         'price' => 'required|numeric|min:0',
    //     ]);

    //     $product->update($validated);

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    // }
public function update(Request $request, $id)
{
    $product = Product::where('id', $id)
                      ->where('admin_id', auth()->id())
                      ->firstOrFail();

    $validated = $request->validate([
        'purchase_price' => 'nullable|numeric|min:0',
        'price' => 'nullable|numeric|min:0',
        'purchase_price_per_dozen' => 'nullable|numeric|min:0',
        'selling_price_per_dozen' => 'nullable|numeric|min:0',
        'purchase_price_per_carton' => 'nullable|numeric|min:0',
        'selling_price_per_carton' => 'nullable|numeric|min:0',
    ]);

    $product->update($validated);

    return redirect()->route('products.index')->with('success', __('Product updated successfully.'));
}

    /**
     * Delete the specified product if it belongs to the authenticated admin.
     */
    public function destroy($id)
    {
        $product = Product::where('id', $id)
                          ->where('admin_id', auth()->id())
                          ->firstOrFail();

        $product->delete();

        return redirect()->route('products.index')->with('success', __('Product deleted successfully.'));
    }

    /**
     * (Optional) Show the form for creating a new product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * (Optional) Store a newly created product and assign it to the authenticated admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product = new Product($validated);
        $product->admin_id = auth()->id(); // Assign ownership
        $product->save();

        return redirect()->route('products.index')->with('success', __('Product created successfully.'));
    }
public function salesReport(Request $request){
    $user = Auth::user();
    if (! $user->isAdmin()) {
        abort(403, 'Unauthorized access.');
    }

    $request->validate([
        'date' => 'nullable|date',
    ]);

    $date = $request->input('date');
    $filters = ['date' => $date];

    $baseQuery = Sale::where('admin_id', $user->id);

    if ($date) {
        $baseQuery->whereDate('created_at', $date);
    }

    $sales = (clone $baseQuery)->with(['product', 'user'])->orderBy('created_at', 'desc')->paginate(15);
    $totalAmount = (clone $baseQuery)->sum('total_amount');
    $totalQuantity = (clone $baseQuery)->sum('quantity');

    $todayQuery = Sale::where('admin_id', $user->id)->whereDate('created_at', today());
    $adminSalesToday = $todayQuery->sum('total_amount');

    $monthQuery = Sale::where('admin_id', $user->id)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year);
    $adminMonthlySales = $monthQuery->sum('total_amount');

    return view('adminR', compact('sales', 'totalAmount', 'totalQuantity', 'filters', 'adminSalesToday', 'adminMonthlySales'));
}

public function salesEmp(Request $request)
{
    $user = Auth::user();

    // Validate inputs
    $request->validate([
        'date' => 'nullable|date',
        'employee_id' => 'nullable|exists:users,id',
    ]);

    $date = $request->input('date');
    $employeeId = $request->input('employee_id');

    if (! $user->isAdmin()) {
        abort(403, 'Unauthorized access.');
    }

    // Base query - sales related to this admin
    $query = Sale::where('admin_id', $user->id);

    // Filter by employee if provided (and ensure employee belongs to this admin)
    if ($employeeId) {
        // Optional: Verify the employee belongs to this admin
        // Assuming users have admin_id column:
        $employee = \App\Models\User::where('id', $employeeId)->where('admin_id', $user->id)->first();

        if (! $employee) {
            return redirect()->route('reports.sales')->with('error', 'Invalid employee selected.');
        }

        $query->where('employee_id', $employeeId);
    }

    // Filter by date if provided
    if ($date) {
        $query->whereDate('created_at', $date);
    }

    // Paginate results
    $sales = $query->with(['product', 'user'])->orderBy('created_at', 'desc')->paginate(15);

    // Calculate totals on filtered data
    $totalAmount = $query->sum('total_amount');
    $totalQuantity = $query->sum('quantity');

    // Pass filters for UI repopulation, and a list of employees to choose from
    $filters = ['date' => $date, 'employee_id' => $employeeId];

    // Get employees for this admin (to populate a dropdown filter in the blade)
    $employees = \App\Models\User::where('admin_id', $user->id)->get();

    return view('emp', compact('sales', 'totalAmount', 'totalQuantity', 'filters', 'employees'));
}
}
