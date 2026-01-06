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
     * Radicals Learning - Index
     */
    public function radicalsIndex()
    {
        return view('client.radicals.index');
    }

    /**
     * Radicals Learning - Practice Page
     */
    public function radicalsPractice()
    {
        return view('client.radicals.practice');
    }

    /**
     * Radicals Learning - By Level
     */
    public function radicalsLevel($level)
    {
        return view('client.radicals.level', ['level' => $level]);
    }

    /**
     * Radicals Learning - Detail
     */
    public function radicalsDetail($id)
    {
        return view('client.radicals.detail', ['id' => $id]);
    }

    /**
     * Vocabulary Learning - Index
     */
    public function vocabularyIndex()
    {
        return view('client.vocabulary.index');
    }

    /**
     * Vocabulary Learning - By Topic
     */
    public function vocabularyTopic($id)
    {
        return view('client.vocabulary.topic', ['id' => $id]);
    }

    /**
     * Vocabulary Learning - Detail
     */
    public function vocabularyDetail($id)
    {
        return view('client.vocabulary.detail', ['id' => $id]);
    }

    /**
     * Quiz/Exam - Index
     */
    public function quizIndex()
    {
        return view('client.quiz.index');
    }

    /**
     * Quiz/Exam - Detail
     */
    public function quizDetail($id)
    {
        return view('client.quiz.detail', ['id' => $id]);
    }

    /**
     * Quiz/Exam - Submit
     */
    public function quizSubmit(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Quiz submitted successfully',
        ]);
    }

    /**
     * Chat page
     */
    public function chat()
    {
        return view('client.chat');
    }

    /**
     * User Profile
     */
    public function profile()
    {
        return view('client.profile');
    }

    /**
     * Update User Profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
            ]);

            $user = Auth::user();
            $user->update($validated);

            return redirect()->route('client.profile')->with('success', 'Profile updated successfully!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
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
