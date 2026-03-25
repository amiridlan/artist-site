<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ReleaseController;
use App\Http\Controllers\Api\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes
Route::get('/members', [MemberController::class, 'index'])->name('api.members.index');
Route::get('/members/{slug}', [MemberController::class, 'show'])->name('api.members.show');

Route::get('/news', [NewsController::class, 'index'])->name('api.news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('api.news.show');

Route::get('/releases', [ReleaseController::class, 'index'])->name('api.releases.index');

Route::get('/videos', [VideoController::class, 'index'])->name('api.videos.index');

Route::get('/events', [EventController::class, 'index'])->name('api.events.index');
