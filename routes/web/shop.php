<?php

use App\Http\Controllers\EmailController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::controller(ShopController::class)->group(function () {
    Route::get('/', 'index')->name('shop');
    Route::get('/contacts', 'contacts')->name('contacts');
    Route::get('/about', 'about')->name('about');
    Route::get('/terms-and-condition', 'termsAndCondition')->name('termsAndCondition');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacyPolicy');
    Route::get('/accessibility-statement', 'accessibilityStatement')->name('accessibilityStatement');
    Route::get('/components', 'components')->name('components');
    Route::get('/builds', 'builds')->name('builds');
    Route::get('/builder', 'builder')->name('builder');
    Route::post('/builder/ai', 'generate')->name('builder.ai');
    // Route::get('/redis', 'redis');
    Route::get('/{category:slug}', 'showByCategory')->name('shop.category');
    Route::get('/builds/{buildPost}/image/{slot}', 'buildImage')->whereNumber('slot')->name('builds.image');
});

Route::post('/contacts', [EmailController::class, 'sendEmail'])->name('contacts.send');
