<?php

namespace App\Http\Controllers;

use App\Models\AdminExpense;
use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', __('Profile updated successfully'));
    }

    public function clearData(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) abort(403);

        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'confirmation' => 'required|string|in:DELETE',
        ]);

        $from = $request->from_date;
        $to = $request->to_date;
        $adminId = $user->id;
        $dataTypes = $request->input('data_types', []);
        $deleted = [];

        DB::beginTransaction();
        try {
            if (in_array('sales', $dataTypes)) {
                $count = Sale::where('admin_id', $adminId)
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->delete();
                $deleted[] = __(':count sale(s)', ['count' => $count]);
            }

            if (in_array('expenses', $dataTypes)) {
                $empCount = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
                    ->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
                    ->delete();
                $admCount = AdminExpense::where('admin_id', $adminId)
                    ->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
                    ->delete();
                $deleted[] = __(':count expense(s)', ['count' => $empCount + $admCount]);
            }

            if (in_array('credit_sales', $dataTypes)) {
                $count = Sale::where('admin_id', $adminId)
                    ->where('status', 'credit')
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->delete();
                $deleted[] = __(':count credit sale(s)', ['count' => $count]);
            }

            if (in_array('inventory_history', $dataTypes)) {
                $count = \App\Models\InventoryHistory::whereHas('product', fn($q) => $q->where('admin_id', $adminId))
                    ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                    ->delete();
                $deleted[] = __(':count inventory history log(s)', ['count' => $count]);
            }

            if (empty($deleted)) {
                DB::rollBack();
                return back()->with('error', __('No data type selected.'));
            }

            DB::commit();

            return back()->with('success', __('Cleared: :items', ['items' => implode(', ', $deleted)]));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error clearing data: :msg', ['msg' => $e->getMessage()]));
        }
    }
}
