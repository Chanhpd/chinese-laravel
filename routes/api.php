<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\VocabularyController;
use App\Http\Controllers\Api\UserProgressController;
use App\Http\Controllers\Api\SavedVocabularyController;
use App\Http\Controllers\Api\WordController;
use App\Http\Controllers\Api\RadicalController;
use App\Http\Controllers\Api\StreakController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ping route - Kiểm tra API hoạt động
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'API đang hoạt động',
        'timestamp' => now()->toDateTimeString(),
        'version' => '1.0.0',
    ], 200);
});

// Auth routes - Không cần authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Public routes - Vocabulary và Topics (không cần authentication)
Route::prefix('topics')->group(function () {
    Route::get('/', [TopicController::class, 'index']);
    Route::get('/{id}', [TopicController::class, 'show']);
    Route::get('/{id}/vocabularies', [TopicController::class, 'vocabularies']);
});

Route::prefix('vocabularies')->group(function () {
    Route::get('/', [VocabularyController::class, 'index']);
    Route::get('/random', [VocabularyController::class, 'random']);
    Route::get('/{id}', [VocabularyController::class, 'show']);
    Route::get('/{id}/translation/{languageCode}', [VocabularyController::class, 'translation']);
});

// Word routes - Public (New word system from level table)
Route::prefix('words')->group(function () {
    Route::get('/levels', [WordController::class, 'getLevels']); // Get all levels with word count
    Route::get('/search', [WordController::class, 'searchWords']); // Search words
    Route::get('/{testType}/{levelNumber}', [WordController::class, 'getWordsByLevel']); // Get words by level (e.g., hsk/1, tocfl/2)
    Route::get('/{testType}', [WordController::class, 'getWordsByTestType']); // Get all words by test type
});

// Radical routes - Public (HSK radicals/characters system)
Route::prefix('radicals')->group(function () {
    Route::get('/levels', [RadicalController::class, 'getLevels']); // Get all HSK levels with radical count
    Route::get('/statistics', [RadicalController::class, 'getStatistics']); // Get radical statistics
    Route::get('/search', [RadicalController::class, 'searchRadicals']); // Search radicals
    Route::get('/hsk/{levelNumber}', [RadicalController::class, 'getRadicalsByLevel']); // Get radicals by HSK level
    Route::get('/hsk', [RadicalController::class, 'getAllHSKRadicals']); // Get all HSK radicals grouped by level
    Route::post('/{id}/favorite', [RadicalController::class, 'toggleFavorite']); // Toggle favorite status
});

// Writing Practice - Score writing
Route::post('/score-writing', [RadicalController::class, 'scoreWriting'])->middleware('optional.auth');

// Chat bot AI - Public (nhưng tự động lưu lịch sử nếu có token)
Route::post('/chat', [ChatController::class, 'chat'])->middleware('optional.auth');

// Protected routes - Cần authentication (support both session and API token)
Route::middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Chat History routes (Yêu cầu authentication)
    Route::prefix('chat')->group(function () {
        Route::get('/history', [ChatController::class, 'history']); // Lấy lịch sử chat
        Route::delete('/history/{id}', [ChatController::class, 'deleteHistory']); // Xóa 1 chat
        Route::delete('/history', [ChatController::class, 'clearHistory']); // Xóa toàn bộ lịch sử
    });

    // User Progress routes
    Route::prefix('progress')->group(function () {
        Route::get('/', [UserProgressController::class, 'index']); // Get all progress
        Route::get('/statistics', [UserProgressController::class, 'statistics']); // Get user statistics
        Route::get('/level/{level}', [UserProgressController::class, 'byLevel']); // Get progress by HSK level
        Route::get('/topic/{topicId}', [UserProgressController::class, 'show']); // Get progress for specific topic
        Route::put('/topic/{topicId}', [UserProgressController::class, 'update']); // Update progress for topic
    });

    // Saved Vocabulary routes
    Route::prefix('saved-vocabularies')->group(function () {
        Route::get('/', [SavedVocabularyController::class, 'index']); // Get all saved vocabularies
        Route::post('/', [SavedVocabularyController::class, 'store']); // Save a vocabulary
        Route::post('/bulk', [SavedVocabularyController::class, 'bulkStore']); // Bulk save vocabularies
        Route::get('/statistics', [SavedVocabularyController::class, 'statistics']); // Get statistics
        Route::put('/{id}', [SavedVocabularyController::class, 'update']); // Update notes
        Route::post('/{id}/review', [SavedVocabularyController::class, 'markReviewed']); // Mark as reviewed
        Route::delete('/{id}', [SavedVocabularyController::class, 'destroy']); // Remove saved vocabulary
    });

    // Streak routes - Daily check-in and streak tracking
    Route::prefix('user/streak')->group(function () {
        Route::get('/', [StreakController::class, 'getStreakData']); // Get current streak data
        Route::post('/check-in', [StreakController::class, 'checkIn']); // Perform daily check-in
        Route::put('/sync', [StreakController::class, 'syncData']); // Sync offline data
        Route::get('/statistics', [StreakController::class, 'getStatistics']); // Get detailed statistics
        Route::delete('/reset', [StreakController::class, 'resetStreak']); // Reset streak (for testing)
    });

    // Include progress tracking routes
    require __DIR__.'/progress-api.php';
});
