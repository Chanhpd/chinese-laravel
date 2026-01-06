<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuth
{
    /**
     * Handle an incoming request.
     * Authenticate user nếu có token hoặc session, nhưng không yêu cầu bắt buộc
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // First, check if user is authenticated via session (web guard)
        if (Auth::guard('web')->check()) {
            Auth::setUser(Auth::guard('web')->user());
            return $next($request);
        }
        
        // Then, try authenticate with Sanctum token
        if ($request->bearerToken()) {
            try {
                $user = Auth::guard('sanctum')->user();
                if ($user) {
                    Auth::setUser($user);
                }
            } catch (\Exception $e) {
                // Ignore authentication errors, cho phép tiếp tục như guest
            }
        }
        
        return $next($request);
    }
}
