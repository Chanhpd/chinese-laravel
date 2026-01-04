<?php

use App\Http\Controllers\ProgressDashboardController;
use App\Http\Controllers\UserLevelProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Progress Tracking API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // Learning Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [ProgressDashboardController::class, 'index']);
        Route::get('/statistics', [ProgressDashboardController::class, 'statistics']);
    });

    // User Level Progress
    Route::prefix('level-progress')->group(function () {
        Route::get('/', [UserLevelProgressController::class, 'index']);
        Route::get('/statistics', [UserLevelProgressController::class, 'statistics']);
        Route::get('/{levelId}', [UserLevelProgressController::class, 'show']);
        Route::post('/{levelId}/initialize', [UserLevelProgressController::class, 'initializeProgress']);
        Route::post('/{levelId}/word-completed', [UserLevelProgressController::class, 'markWordCompleted']);
        Route::post('/{levelId}/radical-completed', [UserLevelProgressController::class, 'markRadicalCompleted']);
        Route::post('/{levelId}/reset', [UserLevelProgressController::class, 'resetProgress']);
    });
});
