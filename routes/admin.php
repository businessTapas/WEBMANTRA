<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminLoginController;

    Route::middleware('guest')->controller(AdminLoginController::class)->group(function() {
            Route::get('/', 'login');
            Route::get('login', 'login')->name('admin.login');
            Route::post('postlogin', 'post_login')->name('admin.postlogin');
        });

    Route::middleware(['auth'])->group(function() {
        Route::get('logout', [AdminLoginController::class, 'logout'] )->name('admin.logout');

        Route::middleware(['auth', 'check.user.type'])->group(function() {
            Route::get('dashboard', [AdminLoginController::class, 'dashboard'])->name('admin.dashboard');
        });

    });