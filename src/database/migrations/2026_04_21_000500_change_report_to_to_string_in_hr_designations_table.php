<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('hr_designations', 'report_to')) {
            return;
        }

        DB::statement("ALTER TABLE `hr_designations` MODIFY `report_to` VARCHAR(191) NULL");
    }

    public function down(): void
    {
        if (!Schema::hasColumn('hr_designations', 'report_to')) {
            return;
        }

        DB::statement("UPDATE `hr_designations` SET `report_to` = NULL WHERE `report_to` IS NOT NULL AND `report_to` REGEXP '[^0-9]'");
        DB::statement("ALTER TABLE `hr_designations` MODIFY `report_to` BIGINT UNSIGNED NULL");
    }
};

