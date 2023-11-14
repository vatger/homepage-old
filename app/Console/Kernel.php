<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Stringable;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Update the datafeed every minute
        $schedule->command('network:datafeed')->everyMinute()->withoutOverlapping();
        // Update the statistics hourly
        $schedule->command('stats:update')->hourlyAt(5)->withoutOverlapping();
        // Update the completed event routes
        $schedule->command('eventroute:update')->hourlyAt(10)->withoutOverlapping();

        // ONLY in production env
//        $schedule->command('forum:sync')->hourly()->withoutOverlapping()->environments(['production']);

        // ONLY in production env
//        $schedule->command('ts:webquery')->hourlyAt(30)->withoutOverlapping()->environments(['production']);

        // Remove unfinished registrations prior to account updates
//        $schedule->command('account:clear-unfinished-registrations')->dailyAt('02:00')->withoutOverlapping();

//        $schedule->command('account:connectupdater')->hourlyAt(45)->withoutOverlapping(60);

        // ONLY in production or staging env
//        $schedule->command('account:apiupdater')->hourlyAt(20)->withoutOverlapping()->environments(['staging', 'production']);
//        $schedule->command('account:apiupdater')->hourlyAt(40)->withoutOverlapping()->environments(['staging', 'production']);
        //
        $schedule->command('activitylog:clean')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
