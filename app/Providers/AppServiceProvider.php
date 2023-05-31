<?php

namespace App\Providers;

//use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    //public function boot(Charts $charts)
    public function boot()
    {
        if(config('app.forcehttps')==true) {
            \URL::forceScheme('https');
        }

        //$charts->register([
        //    \App\Charts\Statistics\Aerodrome\TrafficChart::class,
        //    \App\Charts\Statistics\Flightdata\ActivityChart::class,
        //]);

        Queue::failing(function (JobFailed $event) {
            \Log::channel('joberror')->error($event->connectionName);
            \Log::channel('joberror')->error($event->job->getName());
            \Log::channel('joberror')->error($event->exception->getMessage());
        });
    }
}
