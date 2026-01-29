<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Kernel
{
    protected function schedule(Schedule $schedule)
    {
        // Run demo refresh every two hour
        if (app()->environment('production')) {

            $schedule->command('app:refresh-demo-data')->everyTwoHours()->withoutOverlapping()->runInBackground();

        }

    }
}
