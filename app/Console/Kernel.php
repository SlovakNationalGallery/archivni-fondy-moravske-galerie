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
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('import', [
            '/home/mg/digitalni-archiv.moravska-galerie.cz/import/AMG_Fotografie.csv',
            '/home/mg/digitalni-archiv.moravska-galerie.cz/import/AMG_Negativy.csv',
            '/home/mg/digitalni-archiv.moravska-galerie.cz/import/AMG_OMZM I.csv',
            '/home/mg/digitalni-archiv.moravska-galerie.cz/import/AMZM_OMZM II.csv',
            '/home/mg/digitalni-archiv.moravska-galerie.cz/import/AMG02.csv',
        ])->daily();
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
