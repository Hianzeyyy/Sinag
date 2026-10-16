<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:repair-migrations-table', function () {
    if (DB::connection()->getDriverName() !== 'mysql') {
        return;
    }

    if (!Schema::hasTable('migrations')) {
        if (Schema::hasTable('migrations_repair')) {
            DB::statement('RENAME TABLE `migrations_repair` TO `migrations`');
            $this->info('Restored the repaired migrations table.');
        }

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

    if (!Schema::hasTable('migrations_repair')) {
        DB::statement(
            'CREATE TABLE `migrations_repair` (' .
            '`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, ' .
            '`migration` VARCHAR(255) NOT NULL, ' .
            '`batch` INT NOT NULL' .
            ')'
        );
    }

    DB::statement(
        'INSERT IGNORE INTO `migrations_repair` (`id`, `migration`, `batch`) ' .
        'SELECT `id`, `migration`, `batch` FROM `migrations`'
    );

    DB::statement('DROP TABLE `migrations`');
    DB::statement('RENAME TABLE `migrations_repair` TO `migrations`');
    $this->info('Rebuilt the legacy migrations table with an auto-increment id.');
})->purpose('Repair a legacy MySQL migrations table before running migrations');
