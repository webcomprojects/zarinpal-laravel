<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Here is where you can register payment related routes for your application.
|
*/

// Payment process routes
Route::middleware(['auth'])->group(function () {
    // Initialize payment and redirect to ZarinPal
    Route::get('/payment/pay/{orderId}', [PaymentController::class, 'pay'])
        ->name('payment.pay');
});

// Payment callback route (doesn't require auth as the user comes back from ZarinPal)
Route::get('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

// Success and failure routes
Route::get('/checkout/success', function () {
    return view('checkout.success');
})->name('checkout.success');

Route::get('/checkout/failed', function () {
    return view('checkout.failed');
})->name('checkout.failed');