<?php
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;


Route::controller(SocialiteController::class)->group(function () {
    Route::get('/auth/google/redirect', 'redirect')
        ->defaults('provider', 'google')
        ->name('google.redirect');
    Route::get('/auth/google/callback', 'callback')
        ->defaults('provider', 'google')
        ->name('google.callback');
    Route::get('/auth/facebook/redirect', 'redirect')
        ->defaults('provider', 'facebook')
        ->name('facebook.redirect');
    Route::get('/auth/facebook/callback', 'callback')
        ->defaults('provider', 'facebook')
        ->name('facebook.callback');
});
