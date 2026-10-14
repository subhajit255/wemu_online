<?php

use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

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


if (config('app.env') === 'production') {
    Route::domain('admin.wemu.online')->get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::domain('artist.wemu.online')->get('/', function () {
        return redirect()->route('artist.login');
    });
} else {
    Route::get('/', function () {
        return redirect()->route('artist.login');
    });

    Route::get('/login', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/admin', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/artist', function () {
        return redirect()->route('artist.login');
    });
}

Route::controller(HomeController::class)->group(function () {
    Route::get('/landing', 'landingPage')->name('landing.page');
    Route::get('coming-soon', 'comingSoon')->name('coming.soon');
    Route::get('/term-and-conditions', 'termAndConditions')->name('term.and.conditions');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy.policy');
    Route::post('/notify-me', 'notifyMe')->name('notify.me');
});

Route::post('stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Routes for previewing email templates
Route::get('/mail/signup', function () {
    return view('emails.user-signup');
});

// Public account / user deletion routes (for web & store compliance)
Route::match(['get', 'post'], '/delete-user', [\App\Http\Controllers\Admin\AuthController::class, 'deleteAccount'])->name('web.delete.user');
Route::match(['get', 'post'], '/delete-account', [\App\Http\Controllers\Admin\AuthController::class, 'deleteAccount'])->name('web.delete.account');
Route::match(['get', 'post'], '/delete/account', [\App\Http\Controllers\Admin\AuthController::class, 'deleteAccount']);
