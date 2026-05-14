<?php

// use Modules\Management\BlogManagement\Blog\Controller\Controller as BlogController;
// use Modules\Management\ProjectManagement\Project\Controller\Controller as ProjectController;
// use Modules\Management\ProductManagement\DigitalProduct\Controller\Controller as DigitalProductController;
use Illuminate\Support\Facades\Route;
use Modules\Management\Public\Actions\GetPublicSummary;
use Modules\Management\Public\Actions\GetMemberDeposits;
use Modules\Management\Public\Actions\GetMemberDetail;
use Modules\Management\Public\Actions\GetTransactionLog;
use Modules\Management\Public\Actions\GetIncomeEntries;
use Modules\Management\Public\Actions\GetExpenseEntries;
use Modules\Management\Public\Actions\GetBalanceSheet;

Route::get('public/summary', fn() => GetPublicSummary::execute());
Route::get('public/member/{id}/deposits', fn($id) => GetMemberDeposits::execute($id));
Route::get('public/member/{id}/detail', fn($id) => GetMemberDetail::execute($id));
Route::get('public/transaction-log', fn() => GetTransactionLog::execute());
Route::get('public/income', fn() => GetIncomeEntries::execute());
Route::get('public/expense', fn() => GetExpenseEntries::execute());
Route::get('public/balance-sheet', fn() => GetBalanceSheet::execute());

// Project routes — module removed
// Route::get('get-all-projects', [ProjectController::class,'index']);
// Route::get('get-single-projects/{slug}', [ProjectController::class,'getSingleProject']);
// Route::get('get-projects-comments/{project_id}', [ProjectController::class,'getProjectComments']);
// Route::post('submit-project-comment/{project_id}', [ProjectController::class,'submitProjectComment']);
// Route::post('submit-project-like/{project_id}', [ProjectController::class,'submitProjectLike']);


// DigitalProduct routes — module removed
// Route::get('get-all-digital-products', [DigitalProductController::class, 'index']);
// Route::get('get-single-digital-product/{slug}', [DigitalProductController::class, 'getSingleDigitalProduct']);

// Blog routes — module removed
// Route::get('get-all-blogs', [BlogController::class,'index']);
// Route::get('get-single-blog/{slug}', [BlogController::class,'getSingleBlog']);
