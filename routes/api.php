<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\VocabularyController;
use App\Http\Controllers\Api\UserProgressController;
use App\Http\Controllers\Api\SavedVocabularyController;
use App\Http\Controllers\StoryController;

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

// Story routes - Public
Route::prefix('stories')->group(function () {
    Route::get('/', [StoryController::class, 'index']);
    Route::get('/hsk-levels', [StoryController::class, 'hskLevels']);
    Route::get('/{slug}', [StoryController::class, 'show']);
});

// Protected routes - Cần authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
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
});
