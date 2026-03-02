<?php
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::controller(ProductController::class)
    ->prefix('products')
    ->name('products.')
    ->group(function () {

        // Custom Routes

        // Static URIs
        Route::get('archived', 'archived')->name('archived');
        Route::post('bulk-archive', 'bulkArchive')->name('bulkArchive');
        Route::post('bulk-update-status', 'bulkUpdateStatus')->name('bulkUpdateStatus');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete')->name('bulkForceDelete');

        // Dynamic URIs (with parameters)
        Route::patch('{product}/status', 'updateStatus')->name('updateStatus');
        Route::patch('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
    });

// CRUD Resource Route
Route::resource('products', ProductController::class);