<?php

use App\Http\Controllers\Admin\LandingPageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\ServiceFrequencyController;

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

Route::as('admin.')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::match(['get', 'post'], 'login', 'login')->name('login');
        Route::match(['get', 'post'], 'logout', 'logout')->name('logout');
        Route::match(['get', 'post'], 'forgot/password', 'forgotPassword')->name('forgot.password');
        Route::match(['get', 'post'], 'reset/password/{token?}', 'resetPassword')->name('reset.password');
        Route::any('verify-email/{uuid}', 'verifyEmail')->name('verify.email');
        Route::any('/delete/account', 'deleteAccount')->name('delete.account');
    });

    Route::middleware(['auth'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
            Route::post('profile/update', 'profileUpdate')->name('profile.update');
            Route::post('password/update', 'passwordUpdate')->name('password.update');
            Route::post('notification/read', 'readNotification')->name('read.notification');
        });
        Route::controller(RolePermissionController::class)->as('role.')->prefix('role')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-role-permission');
            Route::post('add', 'roleAdd')->name('add')->can('add-role-permission');
            Route::any('permission/{uuid}', 'rolePermission')->name('permission')->can('add-role-permission');
            Route::any('user-permission/{uuid}', 'userRolePermission')->name('user.permission')->can('add-role-permission');

            Route::any('user/list', 'userList')->name('user.list')->can('view-admin-user');
            Route::any('user/add', 'userAdd')->name('user.add')->can('add-admin-user');
            Route::any('user/edit/{uuid}', 'userAdd')->name('user.edit')->can('edit-admin-user');
        });
        Route::controller(UserController::class)->as('user.')->prefix('user')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-user');
            Route::get('view/{uuid}', 'view')->name('view')->can('view-user');
            Route::any('add', 'add')->name('add')->can('add-user');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-user');
            Route::any('income/{uuid?}', 'income')->name('income')->can('user-income');
            Route::any('expense/{uuid?}', 'expense')->name('expense')->can('user-expense');
            Route::any('item/{uuid?}', 'item')->name('item')->can('user-item');
        });
        Route::controller(ArtistController::class)->as('artist.')->prefix('artist')->group(function () {
            Route::get('list', 'index')->name('list');
            Route::get('view/{uuid}', 'view')->name('view');
            Route::any('add', 'add')->name('add');
            Route::any('edit/{uuid}', 'add')->name('edit');
            Route::post('approve/{uuid}', 'approve')->name('approve');
            Route::post('reject/{uuid}', 'reject')->name('reject');
        });
        Route::controller(CmsController::class)->as('cms.')->prefix('cms')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-cms');
            Route::any('add', 'add')->name('add')->can('add-cms');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-cms');
        });
        Route::controller(CategoryController::class)->as('category.')->prefix('category')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-category');
            Route::any('add', 'add')->name('add')->can('add-category');
            Route::any('add/{uuid}', 'add')->name('edit')->can('edit-category');
        });
        Route::controller(SettingController::class)->as('setting.')->prefix('setting')->group(function () {
            Route::any('update', 'index')->name('update')->can('view-setting');
        });
        Route::controller(SubscriptionController::class)->as('subscription.')->prefix('subscription')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-subscription');
            Route::any('add', 'add')->name('add')->can('add-subscription');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-subscription');
        });
        Route::controller(NotificationController::class)->as('notification.')->prefix('notification')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-notification');
        });
        Route::controller(ContactController::class)->as('contact.')->prefix('contact')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-help-support');
            Route::post('reply', 'contactReply')->name('reply')->can('reply-help-support');
        });
        Route::controller(BroadcastController::class)->as('broadcast.')->prefix('broadcast')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-broadcast');
            Route::any('add', 'add')->name('add')->can('add-broadcast');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-broadcast');
            Route::any('send/{uuid?}', 'send')->name('send')->can('send-broadcast');
            Route::any('log/{uuid?}', 'log')->name('log')->can('view-broadcast');
        });
        Route::controller(ServiceFrequencyController::class)->as('frequency.')->prefix('frequency')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-service-frequency');
            Route::any('add', 'add')->name('add')->can('add-service-frequency');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-service-frequency');
        });
        Route::controller(ItemController::class)->as('item.')->prefix('item')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-item');
            Route::any('add', 'add')->name('add')->can('add-item');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-item');
            Route::any('view/{uuid?}', 'view')->name('view')->can('view-item');
        });
        Route::controller(TransactionController::class)->as('transaction.')->prefix('transaction')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-transaction');
            Route::get('details/{uuid}', 'details')->name('details')->can('view-transaction');
            Route::get('invoice/{uuid}', 'invoice')->name('invoice')->withoutMiddleware(['auth'])->can('view-transaction');
        });
        Route::controller(FaqController::class)->as('faq.')->prefix('faq')->group(function () {
            Route::get('list', 'index')->name('list')->can('view-faq');
            Route::any('add', 'add')->name('add')->can('add-faq');
            Route::any('edit/{uuid}', 'add')->name('edit')->can('edit-faq');
        });
        Route::controller(BannerController::class)->as('banner.')->prefix('banner')->group(function () {
            Route::get('list', 'index')->name('list');
            Route::any('add/{uuid?}', 'add')->name('add');
        });
        Route::controller(CouponController::class)->as('coupon.')->prefix('coupon')->group(function () {
            Route::get('list', 'index')->name('list');
            Route::any('add/{uuid?}', 'add')->name('add');
        });
        Route::controller(LandingPageController::class)->as('landing.page.')->prefix('landing-page')->group(function () {
            Route::any('update', 'index')->name('update');
            Route::any('notify', 'notify')->name('notify');
            Route::any('notify/send', 'notifySend')->name('notify.send');
        });
    });
});
