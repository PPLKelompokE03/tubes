<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MysteryBoxController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:customer')
        ->name('dashboard');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])
        ->middleware('role:customer')
        ->name('dashboard.orders');

    Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])
        ->middleware('role:seller')
        ->name('seller.dashboard');
    Route::get('/seller/restaurants', [SellerDashboardController::class, 'restaurants'])
        ->middleware('role:seller')
        ->name('seller.restaurants');
    Route::get('/seller/mystery-boxes', [SellerDashboardController::class, 'mysteryBoxes'])
        ->middleware('role:seller')
        ->name('seller.mystery-boxes');
    Route::get('/seller/orders', [SellerDashboardController::class, 'orders'])
        ->middleware('role:seller')
        ->name('seller.orders');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('restaurants', RestaurantController::class);
    Route::resource('mystery-boxes', MysteryBoxController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('reviews', ReviewController::class);
});

require __DIR__.'/auth.php';
