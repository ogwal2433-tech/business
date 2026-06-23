<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\BusinessSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($request->query('logout') === 'other_device') {
            session()->flash('error', __('You were logged out because someone logged into your account from another device.'));
        }
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // Attempt login without status check
    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Check user status
        if ($user->status === 'suspended') {
            Auth::logout();
            return redirect()->route('suspension.page')
                ->with('error', 'Your account has been suspended.');
        }

        if ($user->status !== 'active') {
            Auth::logout();
            return back()->with('error', 'Your account is not active.');
        }

        // Check subscription for admins and employees
        if (!$user->isSuperAdmin()) {
            // Employees: check parent admin's subscription first
            if ($user->isEmployee()) {
                $admin = $user->admin_id ? User::find($user->admin_id) : null;
                if (!$admin || !$admin->hasActiveSubscription()) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', __('Your business subscription has expired. Please contact your administrator.'));
                }
            }

            if ($user->hasPendingSubscription()) {
                $request->session()->regenerate();
                return redirect()->route('admin.subscription.required')
                    ->with('info', __('Your subscription request is pending approval from the system administrator.'));
            }

            if (!$user->hasActiveSubscription()) {
                $request->session()->regenerate();
                return redirect()->route('admin.subscription.required')
                    ->with('error', __('Your subscription has expired. Please select a plan to renew.'));
            }
        }

        // If active, regenerate session and redirect as usual
        $request->session()->regenerate();

        $sessionToken = (string) Str::uuid();
        $user->session_token = $sessionToken;
        $user->save();
        session(['session_token' => $sessionToken]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'employee') {
            return redirect()->route('employee.dashboard');
        } elseif ($user->role === 'super_admin') {
            return redirect()->route('system-admin.dashboard');
        }

        Auth::logout();
        return back()->with('error', 'Unauthorized user role.');
    }

    return back()->with('error', 'Invalid credentials.');
}



   public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Flash logout success message
    return redirect()->route('login')->with('message', 'Logout successful.');
}


    public function showRegistrationForm()
    {


        return view('auth.register');
    }

    // Store new user (admin creates new user)
    public function register(Request $request)
{
    $request->validate([
        'business_name' => 'required|string|max:256',
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:6',
    ]);

    $user = User::create([
        'business_name' => $request->business_name,
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin',
        'status' => 'active',
    ]);

    // Auto-assign free trial subscription
    $trialPlan = SubscriptionPlan::where('slug', 'free-trial')->first();
    if ($trialPlan) {
        BusinessSubscription::create([
            'business_admin_id' => $user->id,
            'plan_id' => $trialPlan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($trialPlan->duration_days),
            'trial_ends_at' => now()->addDays($trialPlan->duration_days),
            'status' => 'trial',
        ]);
    }

    // Check if it's an AJAX request
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'redirect' => route('login')
        ], 200);
    }

    // Regular form submission
    return redirect()->route('login')->with('success', __('User registered successfully.'));
}
}