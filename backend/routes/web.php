<?php

use Illuminate\Support\Facades\Route;
use HiEvents\Http\Actions\Orders\StripeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/return', function () {
    dd('success');
});

Route::get('/checkout/stripe', [StripeController::class, 'checkoutStripe'])->name('jazzcash.checkout');
Route::post('/process-payment', [StripeController::class, 'processPayment'])->name('process.payment');

// Route::post('/jazzcash/callback/{event_id}/{order_id}', [HandleJazzCashPayment::class, 'handle'])->name('jazzcash.callback');

// Route::post('events/{event_id}/orders/{order_id}/jazzcash/initiate', InitiateJazzCashPayment::class)
//     ->name('events.orders.jazzcash.initiate');
