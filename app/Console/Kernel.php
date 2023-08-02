<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('registered')->daily();
        //$schedule->command('\App\Console\Commands\RegisteredVisits')->everyMinute();
        //$schedule->command('\App\Console\Commands\RegisteredVisits')->everyTwoMinutes();
        //$schedule->command('\App\Console\Commands\RegisteredVisits')->dailyAt('20:23');
        //$schedule->command(App\Console\Commands\RegisteredVisits::class)->everyMinute();
        //$schedule->command(App\Console\Commands\generateEmailData::class)->everyMinute();
        $schedule->command('create:generate_emails')->everyMinute();
        //$schedule->command('save:emails')->everyMinute();
        /*$schedule->call(function () {
            
        })->daily();*/
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
