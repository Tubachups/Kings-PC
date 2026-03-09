<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/cron', function (Request $request) {
    abort_unless(
        hash_equals(config('app.cron_token'), (string) $request->header('X-CRON-TOKEN')),
        403
    );

    Artisan::call('queue:work', [
        '--once' => true,
        '--queue' => 'default',
        '--tries' => 3,
    ]);

    return response()->json(['ok' => true]);
});
