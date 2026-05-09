<?php

// use Modules\Management\BlogManagement\Blog\Controller\Controller as BlogController;
// use Modules\Management\ProjectManagement\Project\Controller\Controller as ProjectController;
// use Modules\Management\ProductManagement\DigitalProduct\Controller\Controller as DigitalProductController;
use Illuminate\Support\Facades\Route;
use Modules\Management\Public\Actions\GetPublicSummary;
use Modules\Management\Public\Actions\GetMemberDeposits;

Route::get('public/summary', fn() => GetPublicSummary::execute());
Route::get('public/member/{id}/deposits', fn($id) => GetMemberDeposits::execute($id));

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
