<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('hr_working_places', 'bn_name')) {
            Schema::table('hr_working_places', function (Blueprint $table) {
                $table->string('bn_name')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hr_working_places', 'bn_name')) {
            Schema::table('hr_working_places', function (Blueprint $table) {
                $table->dropColumn('bn_name');
            });
        }
    }
};
