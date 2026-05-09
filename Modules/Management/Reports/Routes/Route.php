<?php

use Modules\Management\Reports\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/reports')->group(function () {
    Route::get('ledger',        [Controller::class, 'ledger']);
    Route::get('trial-balance', [Controller::class, 'trialBalance']);
    Route::get('profit-loss',   [Controller::class, 'profitLoss']);
    Route::get('balance-sheet', [Controller::class, 'balanceSheet']);
});
