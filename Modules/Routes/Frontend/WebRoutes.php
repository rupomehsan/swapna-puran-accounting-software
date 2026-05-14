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
Route::get('/transaction-log', [FrontendController::class, 'TransactionLogPage'])->name('TransactionLogPage');
Route::get('/income', [FrontendController::class, 'IncomePage'])->name('IncomePage');
Route::get('/expense', [FrontendController::class, 'ExpensePage'])->name('ExpensePage');
Route::get('/balance-sheet', [FrontendController::class, 'BalanceSheetPage'])->name('BalanceSheetPage');
Route::get('/member/{id}', [FrontendController::class, 'MemberDetailPage'])->name('MemberDetailPage');

Route::get('/login', [AuthController::class, 'LoginPage'])->name('LoginPage');
Route::get('/forgot-password', [AuthController::class, 'ForgotPassword'])->name('ForgotPassword');



