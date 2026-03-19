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

Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');



Route::get('/login', [authController::class, 'login']);
Route::post('/login', [authController::class, 'store']);