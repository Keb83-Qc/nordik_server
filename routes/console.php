<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─────────────────────────────────────────────────────────────────────────────
// TÂCHES PLANIFIÉES
// Ajouter au crontab serveur (une seule entrée suffit) :
//   * * * * * php /chemin/vers/artisan schedule:run >> /dev/null 2>&1
// ─────────────────────────────────────────────────────────────────────────────

// Sync Google Reviews : toutes les 6 heures
Schedule::command('google:fetch-reviews')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// Sync employés Zoho : chaque nuit à 2h
Schedule::command('zoho:sync')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// Backup DB : chaque nuit à 3h
Schedule::command('backup:clean')->dailyAt('03:00')->withoutOverlapping();
Schedule::command('backup:run')->dailyAt('03:15')->withoutOverlapping();
