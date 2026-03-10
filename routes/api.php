<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SnippetController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1/auth')->middleware('throttle:auth')->group(function () {
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('v1')->middleware('throttle:api')->group(function () {
  Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
  Route::apiResource('snippets', SnippetController::class)->only(['index', 'show']);
  Route::apiResource('tags', TagController::class)->only(['index', 'show']);
});

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
  Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
  Route::apiResource('snippets', SnippetController::class)->except(['index', 'show']);
});
