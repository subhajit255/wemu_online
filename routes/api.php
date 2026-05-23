<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/signup', 'signup')->name('signup');
    Route::post('/login', 'login')->name('login');
    Route::post('/login/verification', 'loginVerification')->name('login.verification');
    Route::post('/login-email', 'loginViaEmail')->name('login-email');
    Route::post('/logout', 'logout')->name('logout');
    Route::post('/forgot/password', 'forgotPassword')->name('forgot.password');
    Route::post('/change/password', 'changePassword')->name('change.password');

    Route::post('/forgot/pin', 'forgotPin')->name('forgot.pin');
    Route::post('/verify/pin', 'verifyPin')->name('verify.pin');
    Route::post('/change/pin', 'changePin')->name('change.pin');

    Route::post('/category/list', 'categoryList')->name('category.list');
    Route::get('/service/frequency/list', 'serviceFrequencyList')->name('service.frequency.list');
    Route::get('/setting', 'setting')->name('setting');
    Route::get('/feature', 'feature')->name('feature');
    Route::get('/cms', 'cms')->name('cms');
    Route::get('/faq', 'faq')->name('faq');
    Route::post('/contact-us', 'contactUs')->name('contact.us');


    Route::get('/todo/list', 'todoList')->name('todo.list');
    Route::post('/todo/add', 'todoAdd')->name('todo.add');
    Route::post('/todo/delete', 'todoDelete')->name('todo.delete');
});

Route::controller(MasterController::class)->group(function () {
    Route::get('/albums', 'albums')->name('albums');
    Route::get('/genres', 'genres')->name('genres');
    Route::post('/song/increase-play-count/{id}', 'songsCountIncrease')->name('song.increase-play-count');
    Route::get('/songs-by-album/{albumId}', 'songsByAlbum')->name('songs.by.album');
});
Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/subscriptions', 'subscriptions')->name('subscriptions');
});

Route::controller(ArtistController::class)->group(function () {
    Route::get('/artists', 'artists')->name('artists');
    Route::get('/artist/details/{id}', 'artistDetails')->name('artist.details');
});
Route::middleware('auth:api')->group(function () {
    // Route::controller(UserController::class)->group(function () {
    //     Route::post('/user/list', 'userList')->name('user.list');
    //     Route::get('/user/get/profile', 'profileDetails')->name('user.get.profile');
    //     Route::post('/user/update/profile', 'updateProfile')->name('user.update.profile');
    //     Route::post('/user/income/add', 'userIncomeAdd')->name('user.income.add')->middleware('CheckSubscription');
    //     Route::post('/user/income/recurring/stop', 'userIncomeRecurringStop')->name('user.income.recurring.stop');
    //     Route::post('/user/income/list', 'userIncomeList')->name('user.income.list');
    //     Route::post('/user/income/delete', 'userIncomeDelete')->name('user.income.delete');
    //     Route::post('/user/item/add', 'userItemAdd')->name('user.item.add')->middleware('CheckSubscription');
    //     Route::post('/user/item/list', 'userItemList')->name('user.item.list');
    //     Route::post('/user/item/delete', 'userItemDelete')->name('user.item.delete');
    //     Route::post('/user/item/image/delete', 'userItemImageDelete')->name('user.item.delete');
    //     Route::post('/user/expense/add', 'userExpenseAdd')->name('user.expense.add')->middleware('CheckSubscription');
    //     Route::post('/user/expense/recurring/stop', 'userExpenseRecurringStop')->name('user.expense.recurring.stop');
    //     Route::post('/user/expense/delete', 'userExpenseDelete')->name('user.expense.delete');
    //     Route::post('/user/expense/list', 'userExpenseList')->name('user.expense.list');
    //     Route::post('/user/budget/add', 'userBudgetAdd')->name('user.budget.add')->middleware('CheckSubscription');
    //     Route::post('/user/budget/recurring/stop', 'userBudgetRecurringStop')->name('user.budget.recurring.stop');
    //     Route::post('/user/budget/delete', 'userBudgetDelete')->name('user.budget.delete');
    //     Route::post('/user/budget/list', 'userBudgetList')->name('user.budget.list');
    //     Route::post('/user/goal/add', 'userGoalAdd')->name('user.goal.add')->middleware('CheckSubscription');
    //     Route::post('/user/goal/delete', 'userGoalDelete')->name('user.goal.delete');
    //     Route::post('/user/goal/list', 'userGoalList')->name('user.goal.add');
    //     Route::post('/user/statistics', 'userStatistics')->name('user.statistics');
    //     Route::post('/user/goal/mark', 'userGoalMark')->name('user.goal.mark');
    //     Route::post('/user/task/mark', 'userTaskMark')->name('user.task.mark');
    //     Route::post('/user/task/delete', 'userTaskDelete')->name('user.task.delete');
    //     Route::post('/user/notification/list', 'userNotificationList')->name('user.notification.list');
    //     Route::post('/user/notification/delete', 'userNotificationDelete')->name('user.notification.delete');
    //     Route::get('/user/subscription/list', 'userSubscriptionList')->name('user.subscription.list');

    //     Route::post('/user/subscription/purchase', 'userSubscriptionPurchase')->name('user.subscription.purchase');
    //     Route::post('/user/subscription/response', 'userSubscriptionResponse')->name('user.subscription.response');

    //     Route::get('/coupon/list', 'couponList')->name('coupon.list');
    //     Route::get('/banner/list', 'bannerList')->name('banner.list');

    //     Route::post('/account/delete', 'deleteAccount')->name('account.delete');
    // });
    Route::controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');
        Route::get('my-profile', 'myProfile')->name('my-profile');
        Route::post('update-profile', 'updateProfile')->name('update-profile');
    });
    Route::controller(MasterController::class)->group(function () {
        Route::post('/song/add-play-history/{id}', 'songsPlayed')->name('song.add-play-history');
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('/recently-played', 'recentlyPlayedSongs')->name('recently.played');
        Route::get('/toggle-song-like/{songId}', 'toggleSongLike')->name('toggle.song.like');
        Route::get('/liked-songs', 'likedSong')->name('liked.songs');
        Route::get('/made-for-you', 'madeForYouSongs')->name('made.for.you');
        Route::get('/toggle-artist-follow/{artistId}', 'toggleArtistFollow')->name('toggle.artist.follow');
    });
    Route::controller(SongController::class)->group(function () {
        Route::post('/playlist/create-or-update', 'createUpdatePlaylist')->name('playlist.create-or-update');
        Route::get('/playlist/my-playlists', 'myPlayLists')->name('playlist.my-playlists');
        Route::post('/playlist/add-remove-song', 'songAddRemovePlayList')->name('playlist.add-remove-song');
        Route::get('/playlist/details/{playlistId}', 'playListDetails')->name('playlist.details');
        Route::get('/playlist/delete/{playlistId}', 'deletePlaylist')->name('playlist.delete');
        Route::post('/playlist/bulk-add-remove-song', 'bulkSongAddRemovePlayList')->name('playlist.bulk-add-remove-song');
    });
    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/my-current-subscription', 'myCurrentSubscription')->name('my-current-subscription');
    });
});
