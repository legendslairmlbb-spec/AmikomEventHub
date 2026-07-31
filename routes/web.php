<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\AuthController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Checkout (Public & Auth)
Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/checkout/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');

// Reviews
Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Google SSO Routes
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
Route::post('/logout', [SocialiteController::class, 'logout'])->name('logout');

// Admin Auth
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', AdminEventController::class);
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
});
