<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Super admins bypass check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Allow access to subscription pages without check
        if ($request->routeIs('admin.subscription.required') || $request->routeIs('admin.subscription.my') || $request->routeIs('admin.subscription.subscribe') || $request->routeIs('admin.subscription.status')) {
            return $next($request);
        }

        // For admins
        if ($user->isAdmin()) {
            // Pending — redirect back to subscription page (keep session)
            if ($user->hasPendingSubscription()) {
                return redirect()->route('admin.subscription.required')
                    ->with('info', __('Your subscription request is pending approval from the system administrator.'));
            }
            // Expired / no subscription — log out completely
            if (!$user->hasActiveSubscription()) {
                auth()->logout();
                return redirect()->route('login')->with('error', __('Your subscription has expired. Please log in and choose a plan to renew.'));
            }
        }

        // For employees — log out if parent admin's subscription is expired
        if ($user->isEmployee()) {
            $admin = $user->admin_id ? \App\Models\User::find($user->admin_id) : null;
            if ($admin && !$admin->hasActiveSubscription()) {
                auth()->logout();
                return redirect()->route('login')->with('error', __('Your business subscription has expired. Please contact your admin.'));
            }
        }

        return $next($request);
    }
}
