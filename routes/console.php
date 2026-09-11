<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('surgeries:mark-no-show')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('surgeries:send-reminders')
    ->everyMinute()
    ->withoutOverlapping();
