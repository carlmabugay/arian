<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:refresh-demo-data')->everyTwoHours()->withoutOverlapping()->runInBackground();
