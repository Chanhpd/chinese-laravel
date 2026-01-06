<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    /**
     * Show the client home page
     */
    public function index()
    {
        // Nếu đã đăng nhập, redirect về home
        if (Auth::check()) {
            return redirect()->route('client.home');
        }
        
        return view('client.index');
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập, redirect về home
        if (Auth::check()) {
            return redirect()->route('client.home');
        }
        
        return view('client.login');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        // Nếu đã đăng nhập, redirect về home
        if (Auth::check()) {
            return redirect()->route('client.home');
        }
        
        return view('client.register');
    }

    /**
     * Handle login submit (web-based)
     */
    public function loginSubmit(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }

            // Create session
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('client.home'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Handle register submit (web-based)
     */
    public function registerSubmit(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create session
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('client.home'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Show the home page (authenticated users only)
     */
    public function home()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('client.login');
        }
        
        return view('client.home');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('client.index')->with('success', 'Đăng xuất thành công!');
    }
}
