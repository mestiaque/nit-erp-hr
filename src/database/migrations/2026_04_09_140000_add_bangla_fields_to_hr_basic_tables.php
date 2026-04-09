<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('hr_bonus_titles', 'bn_title')) {
            Schema::table('hr_bonus_titles', function (Blueprint $table) {
                $table->string('bn_title')->nullable()->after('title');
            });
        }

        if (!Schema::hasColumn('hr_designations', 'bn_name')) {
            Schema::table('hr_designations', function (Blueprint $table) {
                $table->string('bn_name')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('hr_factories', 'bn_name')) {
            Schema::table('hr_factories', function (Blueprint $table) {
                $table->string('bn_name')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('hr_factories', 'bn_address')) {
            Schema::table('hr_factories', function (Blueprint $table) {
                $table->text('bn_address')->nullable()->after('address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hr_bonus_titles', 'bn_title')) {
            Schema::table('hr_bonus_titles', function (Blueprint $table) {
                $table->dropColumn('bn_title');
            });
        }

        if (Schema::hasColumn('hr_designations', 'bn_name')) {
            Schema::table('hr_designations', function (Blueprint $table) {
                $table->dropColumn('bn_name');
            });
        }

        if (Schema::hasColumn('hr_factories', 'bn_name')) {
            Schema::table('hr_factories', function (Blueprint $table) {
                $table->dropColumn('bn_name');
            });
        }

        if (Schema::hasColumn('hr_factories', 'bn_address')) {
            Schema::table('hr_factories', function (Blueprint $table) {
                $table->dropColumn('bn_address');
            });
        }
    }
};
