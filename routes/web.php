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

Route::get('/', function () {
    return redirect(route('artist.login'));
});
Route::get('/login', function () {
    return redirect(route('admin.login'));
});
Route::get('/admin', function () {
    return redirect(route('admin.login'));
});
Route::get('/artist', function () {
    return redirect(route('artist.login'));
});
Route::controller(HomeController::class)->group(function () {
    Route::get('/landing', 'landingPage')->name('landing.page');
    Route::get('coming-soon', 'comingSoon')->name('coming.soon');
    Route::get('/term-and-conditions', 'termAndConditions')->name('term.and.conditions');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy.policy');
    Route::post('/notify-me', 'notifyMe')->name('notify.me');
});
