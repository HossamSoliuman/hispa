<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('db:backup')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping();
