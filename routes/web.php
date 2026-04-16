<?php

use App\Http\Controllers\AdminController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:customer')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
        Route::get('/dashboard/restaurants', [DashboardController::class, 'restaurants'])->name('dashboard.restaurants');
        Route::get('/dashboard/restaurants/{restaurant}', [DashboardController::class, 'restaurantMenus'])->name('dashboard.restaurants.show');
    });

    Route::middleware('role:seller')->prefix('seller')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
        Route::get('/restaurants/create', [SellerDashboardController::class, 'createRestaurant'])->name('seller.restaurants.create');
        Route::get('/restaurants/{restaurant}/edit', [SellerDashboardController::class, 'editRestaurant'])->name('seller.restaurants.edit');
        Route::get('/restaurants/{restaurant}/unlock-menu', [SellerDashboardController::class, 'showUnlockMenu'])->name('seller.restaurants.unlock-menu');
        Route::post('/restaurants/{restaurant}/unlock-menu', [SellerDashboardController::class, 'unlockMenu'])->name('seller.restaurants.unlock-menu.post');
        Route::get('/restaurants/{restaurant}/menus', [SellerDashboardController::class, 'restaurantMenus'])->name('seller.restaurants.menus');
        Route::get('/restaurants', [SellerDashboardController::class, 'restaurants'])->name('seller.restaurants');
        Route::get('/mystery-boxes/create', [SellerDashboardController::class, 'createMysteryBox'])->name('seller.mystery-boxes.create');
        Route::get('/mystery-boxes/{mystery_box}/edit', [SellerDashboardController::class, 'editMysteryBox'])->name('seller.mystery-boxes.edit');
        Route::get('/mystery-boxes', [SellerDashboardController::class, 'mysteryBoxes'])->name('seller.mystery-boxes');
        Route::get('/orders', [SellerDashboardController::class, 'orders'])->name('seller.orders');
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('admin.restaurants');
        Route::delete('/restaurants/{restaurant}', [AdminController::class, 'destroyRestaurant'])->name('admin.restaurants.destroy');
        Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
    });

    Route::middleware('role:customer,seller,admin')->group(function () {
        Route::get('/restaurants', [RestaurantController::class, 'index']);
        Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');

        Route::get('/mystery-boxes', [MysteryBoxController::class, 'index'])->name('mystery-boxes.index');
        Route::get('/mystery-boxes/{mystery_box}', [MysteryBoxController::class, 'show'])->name('mystery-boxes.show');

        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::match(['put', 'patch'], '/orders/{order}', [OrderController::class, 'update']);
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    });

    Route::middleware('role:customer')->group(function () {
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    Route::middleware('role:seller')->group(function () {
        Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
        Route::match(['put', 'patch'], '/restaurants/{restaurant}', [RestaurantController::class, 'update'])->name('restaurants.update');
        Route::delete('/restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');

        Route::post('/mystery-boxes', [MysteryBoxController::class, 'store'])->name('mystery-boxes.store');
        Route::match(['put', 'patch'], '/mystery-boxes/{mystery_box}', [MysteryBoxController::class, 'update'])->name('mystery-boxes.update');
        Route::delete('/mystery-boxes/{mystery_box}', [MysteryBoxController::class, 'destroy'])->name('mystery-boxes.destroy');
    });

    Route::middleware('role:customer,admin')->group(function () {
        Route::resource('reviews', ReviewController::class)->except(['create', 'edit']);
    });
});

require __DIR__.'/auth.php';
