<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showAdminLoginForm()
    {
        if (Auth::guard('admin')->check() && in_array(Auth::guard('admin')->user()->role, ['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle admin login request
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::guard('admin')->user();

            // Check if user is admin or staff
            if (!in_array($user->role, ['admin', 'staff'])) {
                Auth::guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => 'You do not have admin access.',
                ]);
            }

            // Check if user is blocked
            if ($user->status === 'blocked') {
                Auth::guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been blocked.',
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
