<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ParcelServiceController;
use App\Http\Controllers\StickerServiceController;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    if (Auth::check()) {
        switch (Auth::user()->type) {
            case 'parcel':
                return redirect('/parcel/dashboard');
            case 'sticker':
                return redirect('/sticker/dashboard');
        }
    }

    // If not logged in, redirect to login
    return redirect('/login');
});

// routes/web.php
Auth::routes(['register' => false]);

// Parcel Services
Route::prefix('eparcel')->middleware(['auth', 'user-access:parcel'])->group(function() {
    Route::get('/dashboard', [App\http\Controllers\ParcelServiceController::class, 'dashboard'])->name('parcel.dashboard');
    Route::get('/kurier', [App\http\Controllers\ParcelServiceController::class, 'courier'])->name('parcel.courier');
    Route::post('/kurier', [App\http\Controllers\ParcelServiceController::class, 'addCourier'])->name('parcel.courier.register');
    Route::get('/daftar/penerima', [App\http\Controllers\ParcelServiceController::class, 'formWithRecipient'])->name('parcel.form.with.recipient');
    Route::get('/recipient/search', [App\Http\Controllers\ParcelServiceController::class, 'searchRecipient'])->name('parcel.recipient.search');
    Route::post('/recipient/detail', [App\Http\Controllers\ParcelServiceController::class, 'recipientDetails'])->name('parcel.recipient.detail');
    Route::post('/daftar/penerima', [App\http\Controllers\ParcelServiceController::class, 'registerParcelWithRecipient'])->name('parcel.register.with.recipient');
    Route::get('/daftar/tanpa_penerima', [App\http\Controllers\ParcelServiceController::class, 'formWithoutRecipient'])->name('parcel.form.without.recipient');
    Route::post('/daftar/tanpa_penerima', [App\http\Controllers\ParcelServiceController::class, 'registerParcelWithoutRecipient'])->name('parcel.register.without.recipient');
    Route::get('/tuntut/penerima', [App\http\Controllers\ParcelServiceController::class, 'claimWithRecipient'])->name('parcel.claim.with.recipient');
    Route::get('/tuntut/parcel/{ic}', [App\http\Controllers\ParcelServiceController::class, 'recipientParcelDetails'])->name('recipient.parcel.details');
    Route::match(['get', 'post'], '/tuntut/tanpa_penerima', [App\Http\Controllers\ParcelServiceController::class, 'claimWithoutRecipient'])->name('parcel.claim.without.recipient');
    Route::post('/tuntut/tanpa_penerima/kemaskini/{id}', [App\http\Controllers\ParcelServiceController::class, 'claimWithoutRecipientUpdate'])->name('parcel.claim.without.recipient.update');
    Route::post('/tuntut/parcel/kemaskini/{id}', [App\http\Controllers\ParcelServiceController::class, 'claimWithRecipientUpdate'])->name('parcel.claim.with.recipient.update');

    // Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('parcel.logout');
});

// Vehicle Services
Route::prefix('sticker')->middleware(['auth', 'user-access:sticker'])->group(function() {
    Route::get('/dashboard', [App\Http\Controllers\StickerServiceController::class, 'dashboard'])->name('sticker.dashboard');

    // Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('sticker.logout');
});