<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ReleaseController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FanclubController;
use Illuminate\Support\Facades\Route;

// Admin auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('members', MemberController::class)->except(['show']);
        Route::resource('news', NewsController::class)->except(['show']);
        Route::resource('releases', ReleaseController::class)->except(['show']);
        Route::resource('videos', VideoController::class)->except(['show']);
        Route::resource('events', EventController::class)->except(['show']);
        Route::resource('fanclub', FanclubController::class)->except(['show']);
    });
});

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
