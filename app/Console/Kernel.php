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
        $schedule->command('send-push:cron')->everyMinute();
        $schedule->command('notification-clear:cron')->daily();
        $schedule->command('income-recurring:cron')->daily();
        $schedule->command('expense-recurring:cron')->daily();
        $schedule->command('expense-remainder:cron')->daily();
        $schedule->command('goal-remainder:cron')->daily();
        $schedule->command('task-remainder:cron')->daily();
        $schedule->command('songs:publish-scheduled')->everyMinute();
        $schedule->command('albums:publish-scheduled')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

