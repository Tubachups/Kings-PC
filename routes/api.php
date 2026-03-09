<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/cron', function () {
    Artisan::call('queue:restart');
    return response()->json(['message' => 'Restart signal sent to all workers.']);
});