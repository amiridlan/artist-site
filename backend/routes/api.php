<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FanAuthController;
use App\Http\Controllers\Api\FanPaymentController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ReleaseController;
use App\Http\Controllers\Api\VideoController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::get('/members', [MemberController::class, 'index'])->name('api.members.index');
Route::get('/members/{slug}', [MemberController::class, 'show'])->name('api.members.show');

Route::get('/news', [NewsController::class, 'index'])->name('api.news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('api.news.show');

Route::get('/releases', [ReleaseController::class, 'index'])->name('api.releases.index');

Route::get('/videos', [VideoController::class, 'index'])->name('api.videos.index');

Route::get('/events', [EventController::class, 'index'])->name('api.events.index');

// Fan auth & payment
Route::prefix('fan')->group(function () {

    // Public — no auth needed
    Route::post('/login',          [FanAuthController::class,  'login']);
    Route::post('/pre-register',   [FanPaymentController::class, 'preRegister']);  // step 1 of registration
    Route::post('/payment/callback', [FanPaymentController::class, 'callback']);   // ToyyibPay server callback
    Route::get('/payment/status',    [FanPaymentController::class, 'status']);     // return-page polling

    // Authenticated fans
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',    [FanAuthController::class,    'logout']);
        Route::get('/me',         [FanAuthController::class,    'me']);
        Route::post('/cancel',    [FanAuthController::class,    'cancel']);
        Route::post('/subscribe', [FanPaymentController::class, 'createBill']);   // renewal
    });
});
