<?php
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;



Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

