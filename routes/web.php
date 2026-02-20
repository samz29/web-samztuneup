<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\PromoSliderController;
use App\Http\Controllers\Admin\ServiceRateController;
use App\Http\Controllers\Admin\WebMenuController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

// Installer routes (only if not installed)
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::post('/', [InstallController::class, 'install'])->name('install');
    Route::get('/success', [InstallController::class, 'success'])->name('success');
});

Route::get('/', [BookingController::class, 'index'])->name('home');

Route::prefix('booking')->name('booking.')->group(function () {
    // Public webhook for Tripay callbacks
    Route::post('/webhook/tripay', [BookingController::class, 'webhook'])->name('webhook');

    // Routes that require authentication (users must login/register first)
    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/calculate-fee', [BookingController::class, 'calculateFee'])->name('calculate.fee');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/payment/{bookingCode}', [BookingController::class, 'payment'])->name('payment');
        Route::get('/success/{bookingCode}', [BookingController::class, 'success'])->name('success');
        Route::get('/track', [BookingController::class, 'track'])->name('track');
    });
});

// Parts routes
Route::prefix('parts')->name('part.')->group(function () {
    Route::get('/{part}', [PartController::class, 'show'])->name('show');
    Route::get('/{part}/checkout', [PartController::class, 'checkout'])->name('checkout');
    Route::post('/calculate-shipping', [PartController::class, 'calculateShipping'])->name('calculate.shipping');
    Route::post('/{part}/order', [PartController::class, 'processOrder'])->name('order');
    Route::get('/payment/success/{orderCode}', [PartController::class, 'paymentSuccess'])->name('payment.success');
    Route::post('/payment/callback', [PartController::class, 'paymentCallback'])->name('payment.callback');
});

// Test route for storage files
Route::get('/storage-test/{filename}', function ($filename) {
    $path = storage_path('app/public/logos/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    return response('File not found', 404);
});
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/dashboard/update-workshop-location', [AdminController::class, 'updateWorkshopLocation'])->name('dashboard.update-workshop-location');
    Route::post('/dashboard/update-logo', [AdminController::class, 'updateLogo'])->name('dashboard.update-logo');

    // Resource routes for CRUD operations
    Route::post('bookings/calculate-fee', [\App\Http\Controllers\Admin\BookingController::class, 'calculateFee'])->name('bookings.calculate-fee');
    Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('/users/{user}/verify-email', [\App\Http\Controllers\Admin\UserController::class, 'verifyEmail'])->name('users.verify-email');
    Route::resource('call-fees', \App\Http\Controllers\Admin\CallFeeController::class);
    Route::resource('promo-sliders', \App\Http\Controllers\Admin\PromoSliderController::class);
    Route::resource('service-rates', \App\Http\Controllers\Admin\ServiceRateController::class);
    Route::resource('app-settings', \App\Http\Controllers\Admin\AppSettingController::class);
    Route::resource('web-menus', \App\Http\Controllers\Admin\WebMenuController::class);
    Route::resource('galleries', \App\Http\Controllers\Admin\GalleryController::class);
    Route::resource('sponsors', \App\Http\Controllers\Admin\SponsorController::class);
    Route::resource('parts', \App\Http\Controllers\Admin\PartController::class);

    // Additional routes
    Route::post('/app-settings/bulk-update', [\App\Http\Controllers\Admin\AppSettingController::class, 'bulkUpdate'])->name('app-settings.bulk-update');
    Route::post('/web-menus/update-order', [\App\Http\Controllers\Admin\WebMenuController::class, 'updateOrder'])->name('web-menus.update-order');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
