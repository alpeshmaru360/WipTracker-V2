<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\File;
use App\Models\Project;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->call(function () {
        //     \Log::channel('schedule-test')->info('Schedule run at ' . now());

        //     $project_create = new Project;
        //     $project_create->project_name = "Cron Job";
        //     $project_create->wip_project_create_date  = now();
        //     $project_create->save();
        // })->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}