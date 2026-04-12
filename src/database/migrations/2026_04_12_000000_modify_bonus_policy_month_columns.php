<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('hr_bonus_policies')) {
            Schema::table('hr_bonus_policies', function (Blueprint $table) {
                // Change month_from and month_to from date to integer (for month numbers)
                if (Schema::hasColumn('hr_bonus_policies', 'month_from')) {
                    $table->integer('month_from')->change()->nullable()->comment('Month number from (e.g., 12 for 12 months)');
                }
                if (Schema::hasColumn('hr_bonus_policies', 'month_to')) {
                    $table->integer('month_to')->change()->nullable()->comment('Month number to (e.g., 60 for 60 months)');
                }
            });
        }

        if (Schema::hasTable('hr_bonus_policies')) {
            Schema::table('hr_bonus_policies', function (Blueprint $table) {
                if (!Schema::hasColumn('hr_bonus_policies', 'designation_id')) {
                    $table->unsignedBigInteger('designation_id')->nullable()->after('sub_section_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hr_bonus_policies')) {
            Schema::table('hr_bonus_policies', function (Blueprint $table) {
                // Revert back to date type if rolling back
                if (Schema::hasColumn('hr_bonus_policies', 'month_from')) {
                    $table->date('month_from')->change()->nullable();
                }
                if (Schema::hasColumn('hr_bonus_policies', 'month_to')) {
                    $table->date('month_to')->change()->nullable();
                }
            });
        }
    }
};
