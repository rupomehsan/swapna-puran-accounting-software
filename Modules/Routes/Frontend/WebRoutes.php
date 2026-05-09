<?php

use Illuminate\Support\Facades\Route;
use Modules\Controllers\Frontend\Auth\AuthController;

use Modules\Controllers\Frontend\FrontendController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontendController::class, 'HomePage'])->name('HomePage');

Route::get('/login', [AuthController::class, 'LoginPage'])->name('LoginPage');
Route::get('/forgot-password', [AuthController::class, 'ForgotPassword'])->name('ForgotPassword');



