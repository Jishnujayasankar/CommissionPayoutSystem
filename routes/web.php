<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard - shows all users with total commissions
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// User management
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Sale management
Route::put('/sales/{id}', [UserController::class, 'updateSale'])->name('sales.update');

// Commission level management
Route::get('/commission-levels', [App\Http\Controllers\CommissionLevelController::class, 'index'])->name('commission-levels.index');
Route::post('/commission-levels', [App\Http\Controllers\CommissionLevelController::class, 'store'])->name('commission-levels.store');
Route::put('/commission-levels/{id}', [App\Http\Controllers\CommissionLevelController::class, 'update'])->name('commission-levels.update');
