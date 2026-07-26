<?php

namespace App\Providers;

use App\Models\KanbanCard;
use App\Models\ScheduleEvent;
use App\Policies\KanbanCardPolicy;
use App\Policies\ScheduleEventPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        ScheduleEvent::class => ScheduleEventPolicy::class,
        KanbanCard::class => KanbanCardPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
