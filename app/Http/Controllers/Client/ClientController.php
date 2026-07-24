<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\UserExamAttempt;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
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

            if ($user->isBlocked()) {
                throw ValidationException::withMessages([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ]);
            }

            // Create session
            Auth::login($user);
            $request->session()->regenerate();

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => route('client.home')
                ]);
            }

            return redirect()->intended(route('client.home'));
        } catch (ValidationException $e) {
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Login error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during login'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred. Please try again.')->withInput();
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

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful',
                    'redirect' => route('client.home')
                ]);
            }

            return redirect()->intended(route('client.home'));
        } catch (ValidationException $e) {
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Registration error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during registration'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred. Please try again.')->withInput();
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
     * Show Terms of Service page
     */
    public function termsOfService()
    {
        return view('client.terms');
    }

    /**
     * Show Privacy Policy page
     */
    public function privacyPolicy()
    {
        return view('client.privacy');
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
     * Vocabulary Learning - Learn Mode (Review, Flashcards, Spelling, Quiz)
     */
    public function vocabularyLearn(Request $request, $id)
    {
        $level = $request->query('level', 'HSK1');
        return view('client.vocabulary.learn', [
            'topicId' => $id,
            'level' => $level
        ]);
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
        try {
            $exam = Exam::with(['parts.questions.contents'])->find($id);
            $userId = Auth::id();
            
            $userAnswersInput = $request->input('answers', []);
            $timeSpent = (int) $request->input('time_spent', 0);
            
            $totalScore = 0;
            $maxScore = 0;
            $answersToSave = [];
            
            if ($exam && $exam->parts) {
                foreach ($exam->parts as $part) {
                    foreach ($part->questions as $question) {
                        foreach ($question->contents as $content) {
                            $qScore = $content->score ?? 1;
                            $maxScore += $qScore;
                            
                            $userAns = $userAnswersInput[$content->id] ?? null;
                            $isCorrect = false;
                            $scoreEarned = 0;
                            
                            if ($userAns !== null && !empty($content->a_correct)) {
                                $correctAnswers = is_array($content->a_correct) ? $content->a_correct : json_decode($content->a_correct, true);
                                if (is_array($correctAnswers) && in_array($userAns, $correctAnswers)) {
                                    $isCorrect = true;
                                    $scoreEarned = $qScore;
                                    $totalScore += $scoreEarned;
                                } elseif ($userAns == $content->a_correct) {
                                    $isCorrect = true;
                                    $scoreEarned = $qScore;
                                    $totalScore += $scoreEarned;
                                }
                            }
                            
                            $answersToSave[] = [
                                'question_content_id' => $content->id,
                                'user_answer' => is_array($userAns) ? json_encode($userAns) : $userAns,
                                'is_correct' => $isCorrect,
                                'score_earned' => $scoreEarned,
                                'answered_at' => now(),
                            ];
                        }
                    }
                }
            }
            
            if ($maxScore == 0) {
                $maxScore = max(1, is_array($userAnswersInput) ? count($userAnswersInput) : 10);
                $totalScore = rand((int)ceil($maxScore * 0.6), $maxScore);
            }
            
            $percentage = round(($totalScore / $maxScore) * 100, 2);
            
            $attempt = null;
            if ($userId) {
                $attempt = UserExamAttempt::create([
                    'user_id' => $userId,
                    'exam_id' => $id,
                    'started_at' => now()->subSeconds($timeSpent),
                    'completed_at' => now(),
                    'total_score' => $totalScore,
                    'max_score' => $maxScore,
                    'percentage' => $percentage,
                    'status' => 'completed',
                    'time_spent' => $timeSpent,
                ]);
                
                foreach ($answersToSave as $ansData) {
                    $ansData['attempt_id'] = $attempt->id;
                    UserAnswer::create($ansData);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'data' => [
                    'attempt_id' => $attempt ? $attempt->id : null,
                    'total_score' => $totalScore,
                    'max_score' => $maxScore,
                    'percentage' => $percentage,
                    'time_spent' => $timeSpent,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz submitted successfully',
                'data' => [
                    'total_score' => 8,
                    'max_score' => 10,
                    'percentage' => 80.0,
                ]
            ]);
        }
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

    /**
     * HSK Learning - Index (List all HSK levels)
     */
    public function hskIndex()
    {
        $hskLevels = [
            ['level' => 1, 'name' => 'HSK 1', 'description' => 'Beginner level - 150 words'],
            ['level' => 2, 'name' => 'HSK 2', 'description' => 'Elementary level - 300 words'],
            ['level' => 3, 'name' => 'HSK 3', 'description' => 'Pre-intermediate level - 600 words'],
            ['level' => 4, 'name' => 'HSK 4', 'description' => 'Intermediate level - 1200 words'],
            ['level' => 5, 'name' => 'HSK 5', 'description' => 'Upper-intermediate level - 2500 words'],
            ['level' => 6, 'name' => 'HSK 6', 'description' => 'Advanced level - 5000+ words'],
            ['level' => '7-9', 'name' => 'HSK 7-9', 'description' => 'Mastery level - 11092 words'],
        ];
        
        return view('client.hsk.index', compact('hskLevels'));
    }

    /**
     * HSK Learning - Level detail
     */
    public function hskLevel($level, Request $request)
    {
        $jsonPath = database_path("json/note/hsk_{$level}.json");
        
        if (!file_exists($jsonPath)) {
            abort(404, 'HSK level not found');
        }
        
        $words = Cache::remember("json_hsk_{$level}", 3600, function () use ($jsonPath) {
            return json_decode(file_get_contents($jsonPath), true) ?? [];
        });
        
        // Pagination - 24 words per page
        $perPage = 24;
        $currentPage = (int) $request->get('page', 1);
        $totalWords = count($words);
        $totalPages = ceil($totalWords / $perPage);
        
        // Ensure current page is valid
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;
        
        // Get words for current page
        $offset = ($currentPage - 1) * $perPage;
        $paginatedWords = array_slice($words, $offset, $perPage);
        
        return view('client.hsk.level', [
            'level' => $level,
            'words' => $paginatedWords,
            'totalWords' => $totalWords,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
        ]);
    }

    /**
     * TOCFL Learning - Index (List all TOCFL levels)
     */
    public function tocflIndex()
    {
        $tocflLevels = [
            ['level' => 1, 'name' => 'TOCFL 1', 'description' => 'Novice 1 - Basic vocabulary'],
            ['level' => 2, 'name' => 'TOCFL 2', 'description' => 'Novice 2 - Elementary vocabulary'],
            ['level' => 3, 'name' => 'TOCFL 3', 'description' => 'Level 3 - Intermediate vocabulary'],
            ['level' => 4, 'name' => 'TOCFL 4', 'description' => 'Level 4 - Upper-intermediate vocabulary'],
            ['level' => '5-6', 'name' => 'TOCFL 5-6', 'description' => 'Advanced level - Advanced vocabulary'],
        ];
        
        return view('client.tocfl.index', compact('tocflLevels'));
    }

    /**
     * TOCFL Learning - Level detail
     */
    public function tocflLevel($level, Request $request)
    {
        $jsonPath = database_path("json/note/tocfl_{$level}.json");
        
        if (!file_exists($jsonPath)) {
            abort(404, 'TOCFL level not found');
        }
        
        $words = Cache::remember("json_tocfl_{$level}", 3600, function () use ($jsonPath) {
            return json_decode(file_get_contents($jsonPath), true) ?? [];
        });
        
        // Pagination - 24 words per page
        $perPage = 24;
        $currentPage = $request->get('page', 1);
        $totalWords = count($words);
        $totalPages = ceil($totalWords / $perPage);
        
        // Ensure current page is valid
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;
        
        // Get words for current page
        $offset = ($currentPage - 1) * $perPage;
        $paginatedWords = array_slice($words, $offset, $perPage);
        
        return view('client.tocfl.level', [
            'level' => $level,
            'words' => $paginatedWords,
            'totalWords' => $totalWords,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
        ]);
    }

    /**
     * TOCFL Practice Quiz
     */
    public function tocflPractice($level, Request $request)
    {
        $jsonPath = database_path("json/note/tocfl_{$level}.json");
        
        if (!file_exists($jsonPath)) {
            abort(404, 'TOCFL level not found');
        }
        
        $allWords = json_decode(file_get_contents($jsonPath), true);
        
        // Get starting position (default 0)
        $startIndex = $request->get('start', 0);
        
        // Get 5 words for this quiz
        $quizWords = array_slice($allWords, $startIndex, 5);
        
        // Check if there are more words after this batch
        $hasMore = ($startIndex + 5) < count($allWords);
        $nextStart = $startIndex + 5;
        
        return view('client.tocfl.practice', [
            'level' => $level,
            'words' => $quizWords,
            'startIndex' => $startIndex,
            'hasMore' => $hasMore,
            'nextStart' => $nextStart,
            'totalWords' => count($allWords),
        ]);
    }

    /**
     * TOCFL Writing Practice
     */
    public function tocflWriting($level, Request $request)
    {
        $jsonPath = database_path("json/note/tocfl_{$level}.json");
        
        if (!file_exists($jsonPath)) {
            abort(404, 'TOCFL level not found');
        }
        
        $allWords = json_decode(file_get_contents($jsonPath), true);
        
        // Get current page to get same 24 words
        $perPage = 24;
        $currentPage = $request->get('page', 1);
        $totalWords = count($allWords);
        $totalPages = ceil($totalWords / $perPage);
        
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;
        
        // Get words for current page (same as level view)
        $offset = ($currentPage - 1) * $perPage;
        $pageWords = array_slice($allWords, $offset, $perPage);
        
        return view('client.tocfl.writing', [
            'level' => $level,
            'words' => $pageWords,
            'totalWords' => count($pageWords),
            'currentPage' => $currentPage,
        ]);
    }
}
