<?php

use Illuminate\Support\Facades\Route;
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
    
    // Authenticated routes (đã đăng nhập)
    Route::get('/home', [ClientController::class, 'home'])->middleware('auth')->name('home');
    Route::post('/logout', [ClientController::class, 'logout'])->middleware('auth')->name('logout');
});

// Admin Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Include admin routes
require __DIR__.'/admin.php';
