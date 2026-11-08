<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPostgresSequences extends Command
{
    protected $signature = 'app:sync-postgres-sequences';

    protected $description = 'Advance PostgreSQL ID sequences after importing database records';

    public function handle(): int
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->error('This command requires a PostgreSQL connection.');

            return self::FAILURE;
        }

        $columns = DB::select(
            "SELECT table_name, pg_get_serial_sequence(format('%I.%I', table_schema, table_name), 'id') AS sequence " .
            'FROM information_schema.columns ' .
            'WHERE table_schema = current_schema() AND column_name = ?',
            ['id']
        );
        $synchronized = 0;

        foreach ($columns as $column) {
            if (!$column->sequence) {
                continue;
            }

            $maxId = DB::table($column->table_name)->max('id');
            DB::select('SELECT setval(?::regclass, ?, ?)', [
                $column->sequence,
                $maxId === null ? 1 : (int) $maxId,
                $maxId !== null,
            ]);
            $synchronized++;
        }

        $this->info("Synchronized {$synchronized} PostgreSQL ID sequences.");

        return self::SUCCESS;
    }
}
