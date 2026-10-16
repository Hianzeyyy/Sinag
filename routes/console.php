<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:repair-migrations-table', function () {
    if (DB::connection()->getDriverName() !== 'mysql' || !Schema::hasTable('migrations')) {
        return;
    }

    $idColumn = DB::selectOne("SHOW COLUMNS FROM `migrations` WHERE `Field` = 'id'");

    if (!$idColumn) {
        throw new RuntimeException('The migrations table is missing its id column.');
    }

    if (str_contains(strtolower($idColumn->Extra), 'auto_increment')) {
        return;
    }

    if (!preg_match('/^int(?:\(\d+\))? unsigned$/i', $idColumn->Type)) {
        throw new RuntimeException('The migrations table id column has an unsupported type.');
    }

    DB::statement('ALTER TABLE `migrations` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT');
    $this->info('Repaired the migrations table auto-increment column.');
})->purpose('Repair a legacy MySQL migrations table before running migrations');
