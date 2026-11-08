<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class CompatibleInsert
{
    public static function insert(string $table, array $values): int
    {
        $connection = DB::connection();

        if ($connection->getDriverName() === 'mysql') {
            $id = ((int) $connection->table($table)->max('id')) + 1;
            $connection->table($table)->insert(['id' => $id] + $values);

            return $id;
        }

        return (int) $connection->table($table)->insertGetId($values, 'id');
    }
}
