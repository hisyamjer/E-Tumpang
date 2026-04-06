<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\destinationController;
use App\Http\Controllers\auth\authController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\carController;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return redirect('/login');
});

// Protect the dashboard so only logged-in students can see it

Route::post('/logout', [authController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Keep the login routes for guests only
Route::middleware('guest')->group(function () {
    Route::get('/login', [authController::class, 'login'])->name('login');
    Route::post('/login', [authController::class, 'store'])->name('store');
});

Route::middleware('auth')->group(function () {
    // The selection page
    Route::get('/choose-role', [RoleController::class, 'index'])->name('role.index');
    // The selection logic
    Route::post('/set-role', [RoleController::class, 'store'])->name('role.store');
});

Route::middleware(['auth', 'check.role'])->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');
    Route::get('/dbdriver', [dashboardController::class, 'index']);
    Route::get('/dbpassenger', [dashboardController::class, 'index']);

    Route::get('/destination', [destinationController::class, 'index'])->name('destination');
    Route::post('/destination', [destinationController::class, 'store'])->name('destination.store');
    Route::put('/destination/{trip}', [destinationController::class, 'update'])->name('destination.update');
    Route::get('/destination/create', [destinationController::class, 'create'])->name('destination.create');
    Route::post('/destination/{trip}/delete', [destinationController::class, 'destroy']);

    Route::get('/car', [carController::class, 'index']);
    Route::post('/car', [carController::class, 'store']);

    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::post('/booking/{trip}', [BookingController::class, 'join']);
    Route::delete('/booking/{id}', [BookingController::class, 'destroy']);

});
