<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\VocabularyController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Topics
    Route::resource('topics', TopicController::class);
    Route::post('topics/{topic}/translations', [TopicController::class, 'updateTranslations'])
        ->name('topics.translations.update');
    
    // Vocabularies
    Route::resource('vocabularies', VocabularyController::class);
    Route::post('vocabularies/{vocabulary}/translations', [VocabularyController::class, 'updateTranslations'])
        ->name('vocabularies.translations.update');
});
