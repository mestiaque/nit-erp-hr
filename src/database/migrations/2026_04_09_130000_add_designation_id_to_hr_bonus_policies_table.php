<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('hr_bonus_policies', 'designation_id')) {
            Schema::table('hr_bonus_policies', function (Blueprint $table) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('sub_section_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hr_bonus_policies', 'designation_id')) {
            Schema::table('hr_bonus_policies', function (Blueprint $table) {
                $table->dropColumn('designation_id');
            });
        }
    }
};
