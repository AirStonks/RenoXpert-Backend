<?php

namespace App\Providers;

use App\Events\SaleStatusUpdated;
use Illuminate\Support\ServiceProvider;
use App\Listeners\TriggerCreateRenoProgress;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        SaleStatusUpdated::class => [
            TriggerCreateRenoProgress::class,
        ],
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
        //
    }
}
