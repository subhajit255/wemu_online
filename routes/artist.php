<?php

use App\Http\Controllers\Artist\AlbumController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Artist\AuthController;
use App\Http\Controllers\Artist\SongController;

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
        Route::match(['get', 'post'], 'otp-verify', 'otpVerify')->name('otp.verify');
        Route::match(['get', 'post'], 'logout', 'logout')->name('logout');
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });
    Route::middleware(['auth'])->group(function () {
        Route::controller(SongController::class)->group(function () {
            Route::get('songs', 'index')->name('songs.index');
            Route::match(['get', 'post'], 'songs/add-or-update/{id?}', 'storeOrUpdate')->name('songs.storeOrUpdate');
        });
        Route::controller(AlbumController::class)->group(function () {
            Route::match(['get', 'post'], 'albums', 'index')->name('albums.index');
            Route::match(['get', 'post'], 'albums/add-or-update/{id?}', 'storeOrUpdate')->name('albums.storeOrUpdate');
            Route::get('albums/{id}', 'show')->name('albums.show');
        });
    });
});
