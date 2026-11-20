<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/dashboard', fn () => redirect()->route('home'))->middleware('auth')->name('dashboard');
Route::get('/pos', [PosController::class, 'index'])->name('pos');
Route::post('/orders', [PosController::class, 'store'])->name('orders.store');
Route::get('/receipts/{order}', [PosController::class, 'receipt'])->name('receipts.show');

Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
