<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TODO: Remove in production
        DB::listen(function ($query) {
            Log::info($query->sql);
            // $query->sql
            // $query->bindings
            // $query->time
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // On long queue wait times notify us
        Horizon::routeMailNotificationsTo('davies.kirk@icloud.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
        // Horizon::routeSmsNotificationsTo('15556667777');
    }
}
