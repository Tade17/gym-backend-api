<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ejecutar la limpieza de fotos cada hora (para asegurar que se borren cumplidas las 24h)
Schedule::command('app:clean-old-meal-photos')->hourly();


// RF-21: Revisa retención a las 8:00 PM todos los días
Schedule::command('app:notify-retention')->dailyAt('20:00');

// RF-22: Revisa recordatorios de comida cada hora
Schedule::command('app:send-daily-reminders')->hourly();