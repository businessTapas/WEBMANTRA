<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

Route::get('/', [ProductController::class, 'index'])->name('home');

    // Cart section
Route::get('/add-to-cart/{slug}', [CartController::class, 'addToCart'])->name('add-to-cart');
Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart')->middleware('user');
Route::get('cart-delete/{id}', [CartController::class, 'cartDelete'])->name('cart-delete');
Route::post('cart-update', [CartController::class, 'cartUpdate'])->name('cart.update');

Route::get('/cart', [CartController::class, 'cartView'])->name('cart');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/checkout/update', [CheckoutController::class, 'update'])->name('checkout-update');
Route::get('/checkout/delete/{id}', [CheckoutController::class, 'delete'])->name('checkout-delete');
Route::post('cart/order', [OrderController::class, 'store'])->name('cart.order');

Route::post('pay', [PayPalController::class, 'payWithPayPal'])->name('pay.with.paypal');
Route::get('paypal/status', [PayPalController::class, 'getPaymentStatus'])->name('paypal.status');


