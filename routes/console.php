<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('database:repair-auto-increment-ids', function () {
    $connection = DB::connection();

    if ($connection->getDriverName() !== 'mysql') {
        $this->info('Database driver does not require MySQL ID repair.');

        return 0;
    }

    $database = $connection->getDatabaseName();
    $columns = $connection->select(
        <<<'SQL'
            SELECT TABLE_NAME, COLUMN_TYPE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND COLUMN_NAME = 'id'
              AND COLUMN_KEY = 'PRI'
              AND DATA_TYPE IN ('tinyint', 'smallint', 'mediumint', 'int', 'bigint')
              AND EXTRA NOT LIKE '%auto_increment%'
            SQL,
        [$database]
    );

    foreach ($columns as $column) {
        $table = str_replace('`', '``', $column->TABLE_NAME);
        $columnType = strtolower($column->COLUMN_TYPE);

        if (!preg_match('/^(tinyint|smallint|mediumint|int|bigint)(\\([0-9]+\\))?( unsigned)?( zerofill)?$/', $columnType)) {
            $this->error("Refusing to repair an unexpected ID type on {$column->TABLE_NAME}.");

            return 1;
        }

        $connection->statement(
            "ALTER TABLE `{$table}` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT"
        );
        $this->line("Enabled AUTO_INCREMENT on {$column->TABLE_NAME}.id");
    }

    $this->info('Primary-key ID repair completed.');

    return 0;
})->purpose('Repair integer primary-key IDs missing AUTO_INCREMENT');
