<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // Add this to the $commands array in the Kernel class
    /**
     * The Artisan commands provided by your application.
     * php artisan stock:fetch-historical-data --symbol=all --start-date=2025-04-01 --end-date=2025-04-04 --chunk=5 --from-id=80 --to-id=100
     * @var array
     */
    protected $commands = [
        // Add this line
        \App\Console\Commands\FetchHistoricalDataCommand::class,

    ];

    // And optionally add it to the schedule
    protected function schedule(Schedule $schedule)
    {
        // Run every 15 minutes during market hours (9:00 AM to 4:00 PM on weekdays)
        $schedule->command('market:fetch-data')
            ->weekdays()
            ->between('9:00', '16:00')
            ->everyFifteenMinutes();

        // Also run once at market open and once at market close
        $schedule->command('market:fetch-data')
            ->weekdays()
            ->at('9:15');

        $schedule->command('market:fetch-data')
            ->weekdays()
            ->at('15:30');

        $schedule->command('market:fetch-indices')->dailyAt('18:00');

        // Add this to your schedule method in Kernel.php
        $schedule->command('market:fetch-holidays')->weekly()->mondays()->at('01:00');
        // Add this to the schedule method
        $schedule->command('stocks:analyze-high-low')->dailyAt('20:00');

        // Update FII Strategy daily after market close
        $schedule->command('fii:update-strategy')
            ->weekdays()
            ->at('16:30')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
