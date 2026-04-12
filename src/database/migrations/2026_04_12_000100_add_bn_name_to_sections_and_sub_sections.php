<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add bn_name to attributes table for sections (type 29)
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table) {
                if (!Schema::hasColumn('attributes', 'bn_name')) {
                    $table->string('bn_name', 191)->nullable()->after('name');
                }
            });
        }

        // Add bn_name to hr_sub_sections table
        if (Schema::hasTable('hr_sub_sections')) {
            Schema::table('hr_sub_sections', function (Blueprint $table) {
                if (!Schema::hasColumn('hr_sub_sections', 'bn_name')) {
                    $table->string('bn_name', 191)->nullable()->after('name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table) {
                if (Schema::hasColumn('attributes', 'bn_name')) {
                    $table->dropColumn('bn_name');
                }
            });
        }

        if (Schema::hasTable('hr_sub_sections')) {
            Schema::table('hr_sub_sections', function (Blueprint $table) {
                if (Schema::hasColumn('hr_sub_sections', 'bn_name')) {
                    $table->dropColumn('bn_name');
                }
            });
        }
    }
};
