<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Show the home page (authenticated users only)
     */
    public function home()
    {
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
