<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\VocabularyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RadicalController;
use App\Http\Controllers\AdminExamController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Dashboard & Statistics
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logs', [DashboardController::class, 'logs'])->name('logs');
    Route::get('/statistics/user-growth', [DashboardController::class, 'userGrowth'])->name('statistics.user-growth');
    Route::get('/statistics/learning-activity', [DashboardController::class, 'learningActivity'])->name('statistics.learning-activity');
    Route::get('/statistics/top-learners', [DashboardController::class, 'topLearners'])->name('statistics.top-learners');
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/role', [UserController::class, 'changeRole'])->name('change-role');
        Route::post('/{id}/block', [UserController::class, 'block'])->name('block');
        Route::post('/{id}/unblock', [UserController::class, 'unblock'])->name('unblock');
        Route::get('/{id}/progress', [UserController::class, 'progress'])->name('progress');
        Route::get('/{id}/saved-vocabularies', [UserController::class, 'savedVocabularies'])->name('saved-vocabularies');
    });
    
    // Topics
    Route::resource('topics', TopicController::class);
    Route::post('topics/{topic}/translations', [TopicController::class, 'updateTranslations'])
        ->name('topics.translations.update');
    
    // Vocabularies
    Route::resource('vocabularies', VocabularyController::class);
    Route::post('vocabularies/{vocabulary}/translations', [VocabularyController::class, 'updateTranslations'])
        ->name('vocabularies.translations.update');

    // Radicals
    Route::prefix('radicals')->name('radicals.')->group(function () {
        Route::get('/', [RadicalController::class, 'index'])->name('index');
        Route::post('/', [RadicalController::class, 'store'])->name('store');
        Route::get('/statistics', [RadicalController::class, 'statistics'])->name('statistics');
        Route::get('/levels', [RadicalController::class, 'getLevels'])->name('levels');
        Route::post('/bulk-import', [RadicalController::class, 'bulkImport'])->name('bulk-import');
        Route::put('/bulk-update', [RadicalController::class, 'bulkUpdate'])->name('bulk-update');
        Route::delete('/bulk-delete', [RadicalController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{id}', [RadicalController::class, 'show'])->name('show');
        Route::put('/{id}', [RadicalController::class, 'update'])->name('update');
        Route::delete('/{id}', [RadicalController::class, 'destroy'])->name('destroy');
    });

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [AdminExamController::class, 'index'])->name('index');
        Route::post('/', [AdminExamController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminExamController::class, 'show'])->name('show');
        Route::put('/{id}', [AdminExamController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminExamController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/statistics', [AdminExamController::class, 'statistics'])->name('statistics');
        Route::get('/{id}/attempts', [AdminExamController::class, 'attempts'])->name('attempts');
        Route::post('/{id}/toggle-active', [AdminExamController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{id}/duplicate', [AdminExamController::class, 'duplicate'])->name('duplicate');
    });
});
