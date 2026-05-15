<?php

use Modules\Management\ShareAdjustment\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('share-adjustments')->group(function () {
        Route::get('',            [Controller::class, 'index']);
        Route::get('preview',     [Controller::class, 'preview']);
        Route::post('store',      [Controller::class, 'store']);
    });
});
