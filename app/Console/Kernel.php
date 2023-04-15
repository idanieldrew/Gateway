<?php

namespace App\Console;

use App\Jobs\DestroyGateway;
use App\Jobs\DestroyOrder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // For test scenario we'll delete expired orders but in real we can transform to other engine
        $schedule->job(DestroyOrder::class)->everyMinute();
        $schedule->job(DestroyGateway::class)->everyMinute();
        // CartDestroy
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
