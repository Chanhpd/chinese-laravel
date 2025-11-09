<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\VocabularyController;

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

// Protected routes - Cần authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});
