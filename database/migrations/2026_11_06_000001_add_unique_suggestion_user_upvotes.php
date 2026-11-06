<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicatePairs = DB::table('suggestion_user_upvotes')
            ->select('suggestion_id', 'user_id')
            ->groupBy('suggestion_id', 'user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicatePairs as $pair) {
            $duplicateIds = DB::table('suggestion_user_upvotes')
                ->where('suggestion_id', $pair->suggestion_id)
                ->where('user_id', $pair->user_id)
                ->orderBy('id')
                ->skip(1)
                ->pluck('id');

            DB::table('suggestion_user_upvotes')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('suggestion_user_upvotes', function (Blueprint $table): void {
            $table->unique(['suggestion_id', 'user_id'], 'suggestion_user_upvotes_unique');
        });
    }

    public function down(): void
    {
        Schema::table('suggestion_user_upvotes', function (Blueprint $table): void {
            $table->dropUnique('suggestion_user_upvotes_unique');
        });
    }
};
