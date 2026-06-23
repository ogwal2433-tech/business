<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
public function dashboard()
{
    $user = Auth::user();

    $todaySales = Sale::where('employee_id', $user->id)
        ->whereDate('created_at', today())
        ->sum('total_amount');

    $monthlySales = Sale::where('employee_id', $user->id)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');

    $totalProductsSold = Sale::where('employee_id', $user->id)
        ->sum('quantity');

    $recentSales = Sale::with('product')
        ->where('employee_id', $user->id)
        ->latest()
        ->take(5)
        ->get();

    return view('employee.dashboard', compact(
        'todaySales',
        'monthlySales',
        'totalProductsSold',
        'recentSales'
    ));
}

public function index()
{
    $employees = User::where('role', 'employee')->get();

    $todaySales = 10;
    $monthlySales = 0;
    $totalProductsSold = 0;

    // Dummy empty collection for now:
    $recentSales = collect();

    return view('Admin.employees', [
        'employees' => $employees,
        'todaySales' => $todaySales,
        'monthlySales' => $monthlySales,
        'totalProductsSold' => $totalProductsSold,
        'recentSales' => $recentSales
    ]);
}

    public function create()
    {
            $employees = User::where('role', 'employee')->latest()->limit(10)->get();

        return view('admin.employees' ,compact('employees'));
    }
public function suspend($id)
{
    $employee = User::findOrFail($id);
    $employee->status = User::STATUS_SUSPENDED;
    $employee->save();

    return back()->with('success', __('Employee suspended successfully.'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'employee',
            'password' => bcrypt($request->password),
            'status' => true,
        ]);

        return redirect()->route('admin.employees.index')
                         ->with('success', __('Employee created successfully.'));
    }
public function reactivate($id)
{
    $employee = User::findOrFail($id);
    $employee->status = 'active';
    $employee->save();

    return back()->with('success', __('Employee reactivated successfully.'));
}
    public function edit($id)
    {
        $employee = User::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $employee->id,
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.employees.index')
                         ->with('success', __('Employee updated successfully.'));
    }

    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees.index')
                         ->with('success', __('Employee deleted.'));
    }

    public function storemployee(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'nullable|email|max:255|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = auth()->user();

    if (!$user->canAddMoreEmployees()) {
        return redirect()->route('admin.employees.create')
            ->with('error', __('Employee limit reached. Upgrade your plan to add more employees.'));
    }

    $employee = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'employee',
        'status' => 'active',
        'admin_id' => $user->id,
    ]);

    return redirect()->route('admin.employees.create')
        ->with('success', __('Employee created successfully.'));
}
}
