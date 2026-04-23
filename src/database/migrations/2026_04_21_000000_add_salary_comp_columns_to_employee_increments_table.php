<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_increments', function (Blueprint $table) {
            $table->decimal('previous_salary_comp_1', 15, 2)->nullable()->after('new_salary');
            $table->decimal('new_salary_comp_1', 15, 2)->nullable()->after('previous_salary_comp_1');
            $table->decimal('previous_salary_comp_2', 15, 2)->nullable()->after('new_salary_comp_1');
            $table->decimal('new_salary_comp_2', 15, 2)->nullable()->after('previous_salary_comp_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_increments', function (Blueprint $table) {
            $table->dropColumn([
                'previous_salary_comp_1',
                'new_salary_comp_1',
                'previous_salary_comp_2',
                'new_salary_comp_2',
            ]);
        });
    }
};
