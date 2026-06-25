<?php

use App\Http\Controllers\Artist\AlbumController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Artist\AuthController;
use App\Http\Controllers\Artist\SongController;
use App\Http\Controllers\Artist\SubscriptionController;
use App\Http\Controllers\Artist\AnalyticsController;
use App\Http\Controllers\Artist\AudienceController;
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

Route::as('artist.')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::match(['get', 'post'], 'login', 'login')->name('login');
        Route::match(['get', 'post'], 'register', 'register')->name('register');
        Route::get('register/checkout-success', 'checkoutSuccess')->name('register.checkout.success');
        Route::match(['get', 'post'], 'otp-verify', 'otpVerify')->name('otp.verify');
        Route::match(['get', 'post'], 'logout', 'logout')->name('logout');
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::post('reverify', 'reverifySubmit')->name('reverify.submit');
    });
    Route::middleware(['auth', \App\Http\Middleware\CheckArtistApproval::class])->group(function () {
        Route::controller(SongController::class)->group(function () {
            Route::get('songs', 'index')->name('songs.index');
            Route::match(['get', 'post'], 'songs/add-or-update/{id?}', 'storeOrUpdate')->name('songs.storeOrUpdate');
            Route::get('songs/details/{id}', 'show')->name('songs.show');
            Route::get('songs/play/{id}', 'play')->name('songs.play');
        });
        Route::controller(AlbumController::class)->group(function () {
            Route::match(['get', 'post'], 'albums', 'index')->name('albums.index');
            Route::match(['get', 'post'], 'albums/add-or-update/{id?}', 'storeOrUpdate')->name('albums.storeOrUpdate');
            Route::get('albums/{id}', 'show')->name('albums.show');
        });
        Route::controller(AnalyticsController::class)->group(function () {
            Route::get('analytics', 'index')->name('analytics.index');
            Route::get('analytics/streams', 'streamsChart')->name('analytics.streams');
        });
        Route::controller(AudienceController::class)->group(function () {
            Route::get('audience', 'index')->name('audience.index');
        });
        Route::controller(\App\Http\Controllers\Artist\SettingsController::class)->group(function () {
            Route::get('settings', 'index')->name('settings.index');
            Route::post('settings', 'update')->name('settings.update');
        });
        Route::controller(\App\Http\Controllers\Artist\PromotionController::class)->group(function () {
            Route::get('promotion', 'index')->name('promotion.index');
        });
        Route::controller(\App\Http\Controllers\Artist\TeamController::class)->group(function () {
            Route::get('team', 'index')->name('team.index');
            Route::match(['get', 'post'], 'team/add-or-update/{id?}', 'storeOrUpdate')->name('team.storeOrUpdate');
            Route::post('team/update-permissions/{id}', 'updatePermissions')->name('team.updatePermissions');
            Route::post('team/delete/{id}', 'destroy')->name('team.destroy');
        });
        Route::controller(\App\Http\Controllers\Artist\ReleaseController::class)->group(function () {
            Route::get('releases', 'index')->name('releases.index');
        });
        Route::controller(SubscriptionController::class)->group(function () {
            Route::get('subscription', 'index')->name('subscription.index');
            Route::post('subscription/{id}/cancel', 'cancel')->name('subscription.cancel');
            Route::get('subscription/plans', 'plans')->name('subscription.plans');
            Route::post('subscription/checkout', 'checkout')->name('subscription.checkout');
            Route::get('subscription/checkout/success', 'checkoutSuccess')->name('subscription.checkout.success');
        });
        Route::controller(\App\Http\Controllers\Artist\FaqController::class)->group(function () {
            Route::get('faq', 'index')->name('faq.index');
            Route::post('faq/add', 'add')->name('faq.add');
            Route::post('faq/delete/{id}', 'destroy')->name('faq.destroy');
        });
    });
});
