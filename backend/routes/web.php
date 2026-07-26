<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ConflictLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FanclubController;
use App\Http\Controllers\Admin\KanbanCardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ReleaseController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\ScheduleEventController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\VideoController;
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

        Route::get('social-media', [SocialMediaController::class, 'index'])->name('social-media.index');
        Route::post('social-media/sync', [SocialMediaController::class, 'sync'])->name('social-media.sync');
        Route::get('social-media/{platform}', [SocialMediaController::class, 'show'])->name('social-media.show');

        // Calendar & Schedule Events
        Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
        Route::resource('schedule-events', ScheduleEventController::class)->except(['show']);

        // Kanban Board
        Route::get('kanban', [KanbanCardController::class, 'index'])->name('kanban.index');
        Route::post('kanban', [KanbanCardController::class, 'store'])->name('kanban.store');
        Route::put('kanban/{kanbanCard}', [KanbanCardController::class, 'update'])->name('kanban.update');
        Route::patch('kanban/{kanbanCard}/move', [KanbanCardController::class, 'move'])->name('kanban.move');
        Route::post('kanban/{kanbanCard}/confirm', [KanbanCardController::class, 'confirm'])->name('kanban.confirm');
        Route::delete('kanban/{kanbanCard}', [KanbanCardController::class, 'destroy'])->name('kanban.destroy');

        // Resources
        Route::resource('resources', AdminResourceController::class)->except(['show']);

        // Conflict Logs
        Route::get('conflict-logs', [ConflictLogController::class, 'index'])->name('conflict-logs.index');
        Route::post('conflict-logs/{conflictLog}/resolve', [ConflictLogController::class, 'resolve'])->name('conflict-logs.resolve');
    });
});

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
