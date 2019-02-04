<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
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
        // Apparently this is more moment.js friendly
        // https://github.com/laravel/framework/issues/24983
//        Carbon::serializeUsing(function (Carbon $timestamp) {
//            return $timestamp->toIso8601ZuluString();
//        });

        if ($this->app->environment() !== 'production') {
            DB::listen(function ($query) {
                Log::info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
                // Log::info($query->sql);
            });
        }
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

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
