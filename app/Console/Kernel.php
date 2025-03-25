<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        if (app()->environment('local')) {
            // Run every minute in the local environment for testing
            $schedule->command('send:trip-reminders')->everyMinute();
        } else {
            // Define your actual production scheduling here
            $schedule->command('send:trip-reminders')->daily();
            $schedule->command('check:deadlines')->daily();
            $schedule->command('check:activity-reminder')->everyMinute();
            $schedule->command('check:plan-expired')->daily();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
        Commands\SendTripReminders::class,
    ];
}
