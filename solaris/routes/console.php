<?php

use App\Models\SolarFarm;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('solaris:seed-if-empty', function () {
    if (SolarFarm::exists()) {
        $this->info('La base ya tiene granjas; los datos existentes se conservan.');

        return;
    }
    $this->call('db:seed', ['--force' => true]);
})->purpose('Inicializa un despliegue nuevo sin sobrescribir datos de operación');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
