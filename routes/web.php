<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Client Routes
Route::prefix('client')->name('client.')->group(function () {
    // Guest routes (chưa đăng nhập)
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/login', [ClientController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [ClientController::class, 'showRegisterForm'])->name('register');
    Route::post('/login-submit', [ClientController::class, 'loginSubmit'])->name('login-submit');
    Route::post('/register-submit', [ClientController::class, 'registerSubmit'])->name('register-submit');
    
    // Legal pages
    Route::get('/terms-of-service', [ClientController::class, 'termsOfService'])->name('terms');
    Route::get('/privacy-policy', [ClientController::class, 'privacyPolicy'])->name('privacy');
    
    // Authenticated routes (đã đăng nhập)
    Route::middleware('auth')->group(function () {
        Route::get('/home', [ClientController::class, 'home'])->name('home');
        Route::post('/logout', [ClientController::class, 'logout'])->name('logout');
        
        // Radicals Learning
        Route::prefix('radicals')->name('radicals.')->group(function () {
            Route::get('/', [ClientController::class, 'radicalsIndex'])->name('index');
            Route::get('/practice', [ClientController::class, 'radicalsPractice'])->name('practice');
            Route::get('/level/{level}', [ClientController::class, 'radicalsLevel'])->name('level');
            Route::get('/{id}', [ClientController::class, 'radicalsDetail'])->name('detail');
        });
        
        // Vocabulary Learning
        Route::prefix('vocabulary')->name('vocabulary.')->group(function () {
            Route::get('/', [ClientController::class, 'vocabularyIndex'])->name('index');
            Route::get('/topic/{id}', [ClientController::class, 'vocabularyTopic'])->name('topic');
            Route::get('/learn/{id}', [ClientController::class, 'vocabularyLearn'])->name('learn');
            Route::get('/{id}', [ClientController::class, 'vocabularyDetail'])->name('detail');
        });
        
        // HSK Learning
        Route::prefix('hsk')->name('hsk.')->group(function () {
            Route::get('/', [ClientController::class, 'hskIndex'])->name('index');
            Route::get('/level/{level}', [ClientController::class, 'hskLevel'])->name('level');
        });
        
        // TOCFL Learning
        Route::prefix('tocfl')->name('tocfl.')->group(function () {
            Route::get('/', [ClientController::class, 'tocflIndex'])->name('index');
            Route::get('/level/{level}', [ClientController::class, 'tocflLevel'])->name('level');
            Route::get('/level/{level}/practice', [ClientController::class, 'tocflPractice'])->name('practice');
            Route::get('/level/{level}/writing', [ClientController::class, 'tocflWriting'])->name('writing');
        });
        
        // Quiz/Exam
        Route::prefix('quiz')->name('quiz.')->group(function () {
            Route::get('/', [ClientController::class, 'quizIndex'])->name('index');
            Route::get('/{id}', [ClientController::class, 'quizDetail'])->name('detail');
            Route::post('/{id}/submit', [ClientController::class, 'quizSubmit'])->name('submit');
        });
        
        // Chat
        Route::get('/chat', [ClientController::class, 'chat'])->name('chat');
        
        // Profile
        Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [ClientController::class, 'updateProfile'])->name('profile.update');
    });
});

// Root redirect to client
Route::redirect('/', '/client', 301);

// Admin Authentication Routes (at /admin/login)
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login-submit');
});

Route::middleware('auth:admin')->group(function () {
    Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

// Include admin dashboard and management routes
require __DIR__.'/admin.php';
