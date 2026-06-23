<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionToken = $user->session_token;

            if ($sessionToken !== null) {
                $currentToken = session('session_token');

                if ($currentToken === null || $currentToken !== $sessionToken) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'message' => 'Logged out due to login from another device.',
                            'logout' => true,
                        ], 401);
                    }

                    return redirect()->route('login')
                        ->with('error', 'You were logged out because someone logged into your account from another device.');
                }
            }
        }

        return $next($request);
    }
}
