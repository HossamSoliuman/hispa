<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('db:backup')
    ->everyMinute()
    // ->at('02:00')
    ->withoutOverlapping();
