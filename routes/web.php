<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\auth\authController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', function () {
    return redirect('/dashboard');
});

// Protect the dashboard so only logged-in students can see it
Route::get('/dashboard', [dashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Keep the login routes for guests only
Route::middleware('guest')->group(function () {
    Route::get('/login', [authController::class, 'login'])->name('login');
    Route::post('/login', [authController::class, 'store'])->name('store');
});