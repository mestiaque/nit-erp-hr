<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hr_factories', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_factories', 'allow_ot_hour')) {
                $table->decimal('allow_ot_hour', 8, 2)->nullable()->after('roster_day');
            }
        });
    }

    public function down()
    {
        Schema::table('hr_factories', function (Blueprint $table) {
            if (Schema::hasColumn('hr_factories', 'allow_ot_hour')) {
                $table->dropColumn('allow_ot_hour');
            }
        });
    }
};
