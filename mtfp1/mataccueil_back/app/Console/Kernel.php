<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;


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
       // $schedule->command('inspire')->everyMinute();
        $req= new Request();
       // $schedule->job(ServiceController::afficheRapport($req, 0))->dailyAt('16:10')->timezone('Africa/Porto-Novo');
        $schedule->command("notification:srm")->weeklyOn('4','09:00')->timezone('Africa/Porto-Novo');
        // $schedule->job(ServiceController::afficheRapport($req,6))->everyMinute()->timezone('Africa/Porto-Novo');
        // $schedule->job(ServiceController::afficheRapport($req,11))->everyMinute()->timezone('Africa/Porto-Novo');
        // $schedule->job(ServiceController::afficheRapport($req,16))->everyMinute()->timezone('Africa/Porto-Novo');
        // $schedule->job(ServiceController::afficheRapport($req,21))->everyMinute()->timezone('Africa/Porto-Novo');
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
