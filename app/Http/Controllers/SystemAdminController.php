<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\BusinessSubscription;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemAdminController extends Controller
{
    // ──────────────────────────────────────────────
    //  Dashboard
    // ──────────────────────────────────────────────
    public function dashboard()
    {
        $totalBusinesses = User::where('role', 'admin')->count();
        $totalEmployees = User::where('role', 'employee')->count();
        $activeSubscriptions = BusinessSubscription::whereIn('status', ['active', 'trial'])->count();
        $pendingSubscriptions = BusinessSubscription::where('status', 'pending')->count();
        $expiredSubscriptions = BusinessSubscription::where('status', 'expired')->count();
        $totalRevenue = PaymentTransaction::where('status', 'paid')->sum('amount');
        $recentBusinesses = User::where('role', 'admin')->latest()->take(5)->get();
        $recentPayments = PaymentTransaction::with('subscription.businessAdmin')
            ->where('status', 'paid')
            ->latest()
            ->take(10)
            ->get();
        $plans = SubscriptionPlan::all();

        return view('system-admin.dashboard', compact(
            'totalBusinesses', 'totalEmployees', 'activeSubscriptions',
            'pendingSubscriptions', 'expiredSubscriptions', 'totalRevenue', 'recentBusinesses',
            'recentPayments', 'plans'
        ));
    }

    public function runSubscriptionCheck()
    {
        $expired = BusinessSubscription::whereIn('status', ['active', 'trial'])
            ->where('end_date', '<', now())
            ->get();

        $count = $expired->count();

        foreach ($expired as $sub) {
            $sub->update(['status' => 'expired']);
        }

        if ($count > 0) {
            return redirect()->route('system-admin.dashboard')
                ->with('success', __('Subscription check complete. :count subscription(s) marked as expired.', ['count' => $count]));
        }

        return redirect()->route('system-admin.dashboard')
            ->with('success', __('No expired subscriptions found.'));
    }

    // ──────────────────────────────────────────────
    //  Business (Admin) Management
    // ──────────────────────────────────────────────
    public function businesses(Request $request)
    {
        $query = User::where('role', 'admin');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $businesses = $query->withCount('employees')
            ->with('subscription.plan')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('system-admin.businesses', compact('businesses'));
    }

    public function businessDetail($id)
    {
        $business = User::where('role', 'admin')
            ->withCount('employees')
            ->with(['subscription.plan', 'allSubscriptions' => function ($q) {
                $q->with('plan')->latest();
            }])
            ->findOrFail($id);

        $payments = PaymentTransaction::whereHas('subscription', function ($q) use ($id) {
            $q->where('business_admin_id', $id);
        })->with('recorder')->latest()->get();

        return view('system-admin.business-detail', compact('business', 'payments'));
    }

    public function toggleBusinessStatus($id)
    {
        $business = User::where('role', 'admin')->findOrFail($id);
        $business->status = $business->status === 'active' ? 'inactive' : 'active';
        $business->save();

        return redirect()->back()->with('success', __("Business :name status updated to :status", [
            'name' => $business->business_name,
            'status' => $business->status,
        ]));
    }

    // ──────────────────────────────────────────────
    //  Subscription Management
    // ──────────────────────────────────────────────
    public function subscriptions(Request $request)
    {
        $query = BusinessSubscription::with(['businessAdmin', 'plan']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $subscriptions = $query->latest()->paginate(20)->withQueryString();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('system-admin.subscriptions', compact('subscriptions', 'plans'));
    }

    public function assignSubscription(Request $request)
    {
        $validated = $request->validate([
            'business_admin_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,trial,expired,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Check user is an admin
        $user = User::findOrFail($validated['business_admin_id']);
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', __('Selected user is not a business admin.'));
        }

        // Deactivate any existing active subscriptions
        BusinessSubscription::where('business_admin_id', $validated['business_admin_id'])
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled']);

        BusinessSubscription::create([
            'business_admin_id' => $validated['business_admin_id'],
            'plan_id' => $validated['plan_id'],
            'start_date' => $validated['start_date'] ?? now(),
            'end_date' => $validated['end_date'] ?? now()->addDays(30),
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', __('Subscription assigned successfully.'));
    }

    public function updateSubscriptionStatus(Request $request, $id)
    {
        $subscription = BusinessSubscription::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:active,expired,trial,cancelled',
            'end_date' => 'nullable|date',
        ]);

        $subscription->update($validated);

        return redirect()->back()->with('success', __('Subscription status updated.'));
    }

    public function approveSubscription(Request $request, $id)
    {
        $subscription = BusinessSubscription::with('plan')->findOrFail($id);

        if ($subscription->status !== 'pending') {
            return redirect()->back()->with('error', __('This subscription is not pending approval.'));
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($subscription, $validated) {
            $subscription->update([
                'status' => 'active',
            ]);

            PaymentTransaction::create([
                'subscription_id' => $subscription->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'] ?? now(),
                'status' => 'paid',
                'payment_method' => $validated['payment_method'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);
        });

        $businessName = $subscription->businessAdmin?->business_name ?? 'N/A';
        return redirect()->back()->with('success', __("Subscription approved for {$businessName}. Payment recorded."));
    }

    // ──────────────────────────────────────────────
    //  Payment Recording
    // ──────────────────────────────────────────────
    public function recordPayment(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:business_subscriptions,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $subscription = BusinessSubscription::findOrFail($validated['subscription_id']);

        DB::transaction(function () use ($validated, $subscription) {
            PaymentTransaction::create([
                'subscription_id' => $validated['subscription_id'],
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'] ?? now(),
                'status' => 'paid',
                'payment_method' => $validated['payment_method'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);

            // Extend subscription
            $plan = $subscription->plan;
            $oldEnd = $subscription->end_date ?: now();
            $newEnd = max($oldEnd, now())->addDays($plan->duration_days);
            $subscription->update([
                'status' => 'active',
                'end_date' => $newEnd,
            ]);
        });

        return redirect()->back()->with('success', __('Payment recorded and subscription extended.'));
    }

    // ──────────────────────────────────────────────
    //  Plan Management
    // ──────────────────────────────────────────────
    public function plans()
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        return view('system-admin.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['features'] = $validated['features']
            ? array_map('trim', explode("\n", $validated['features']))
            : [];
        $validated['is_active'] = $request->has('is_active');

        SubscriptionPlan::create($validated);

        return redirect()->route('system-admin.plans')->with('success', __('Plan created successfully.'));
    }

    public function updatePlan(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $id,
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
        ]);

        $validated['features'] = $validated['features']
            ? array_map('trim', explode("\n", $validated['features']))
            : [];
        $validated['is_active'] = $request->has('is_active');

        $plan->update($validated);

        return redirect()->route('system-admin.plans')->with('success', __('Plan updated successfully.'));
    }

    // ──────────────────────────────────────────────
    //  Business Admin's Subscription Page
    // ──────────────────────────────────────────────
    public function mySubscription()
    {
        $user = auth()->user();
        $subscription = $user->subscription;
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $payments = PaymentTransaction::whereHas('subscription', function ($q) use ($user) {
            $q->where('business_admin_id', $user->id);
        })->with('recorder')->latest()->get();

        $paymentsJson = $payments->map(function ($p) {
            return [
                'id' => $p->id,
                'amount' => number_format($p->amount),
                'payment_date' => $p->payment_date ? $p->payment_date->format('d M Y H:i') : $p->created_at->format('d M Y H:i'),
                'payment_method' => $p->payment_method ?? '-',
                'reference' => $p->reference ?? '-',
                'status' => ucfirst($p->status),
                'notes' => $p->notes ?? '-',
                'recorded_by' => $p->recorder?->name ?? 'System',
                'created_at' => $p->created_at->format('d M Y H:i'),
            ];
        });

        return view('system-admin.my-subscription', compact('subscription', 'plans', 'payments', 'paymentsJson'));
    }

    public function subscriptionRequired()
    {
        $user = auth()->user();
        $pendingSub = BusinessSubscription::where('business_admin_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('system-admin.subscription-required', compact('plans', 'pendingSub'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Cancel any existing active/trial subscriptions
        BusinessSubscription::where('business_admin_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled']);

        // Create new subscription
        BusinessSubscription::create([
            'business_admin_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.subscription.required')
            ->with('success', __('You have chosen the :plan plan. Awaiting system admin approval.', ['plan' => $plan->name]));
    }

    public function subscriptionStatus()
    {
        $user = auth()->user();
        $sub = BusinessSubscription::where('business_admin_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return response()->json([
            'pending' => $sub ? true : false,
        ]);
    }

    // ──────────────────────────────────────────────
    //  AJAX: Get user for subscription assignment
    // ──────────────────────────────────────────────
    public function searchBusinesses(Request $request)
    {
        $search = $request->get('q');
        $users = User::where('role', 'admin')
            ->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            })
            ->select('id', 'business_name', 'name', 'username')
            ->take(20)
            ->get();

        return response()->json($users);
    }
}
