<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_satisfaction_surveys')
            && !Schema::hasColumn('customer_satisfaction_surveys', 'responses')) {
            Schema::table('customer_satisfaction_surveys', function (Blueprint $table) {
                $table->json('responses')->nullable()->after('feedback');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_satisfaction_surveys')
            && Schema::hasColumn('customer_satisfaction_surveys', 'responses')) {
            Schema::table('customer_satisfaction_surveys', function (Blueprint $table) {
                $table->dropColumn('responses');
            });
        }
    }
};
