<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Check if user has admin, super_admin, or staff role
        if (!in_array($user->role, ['admin', 'super_admin', 'staff'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }
            
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'You do not have admin access.'
            ]);
        }

        // Check if user is blocked
        if ($user->status === 'blocked') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been blocked.',
                ], 403);
            }
            
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been blocked.'
            ]);
        }

        return $next($request);
    }
}
