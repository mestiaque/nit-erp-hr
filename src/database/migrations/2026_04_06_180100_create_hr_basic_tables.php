<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_bonus_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_bonus_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('bonus_title_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('sub_section_id')->nullable();
            $table->date('month_from')->nullable();
            $table->date('month_to')->nullable();
            $table->string('salary_basis', 30)->default('basic');
            $table->string('amount_type', 30)->default('percent');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->integer('approved_manpower')->nullable();
            $table->decimal('attendance_bonus', 12, 2)->nullable();
            $table->decimal('attendance_bonus_com', 12, 2)->nullable();
            $table->decimal('tiffin_allowance', 12, 2)->nullable();
            $table->decimal('minimum_tiffin_hour', 12, 2)->nullable();
            $table->decimal('night_allowance', 12, 2)->nullable();
            $table->decimal('minimum_night_hour', 12, 2)->nullable();
            $table->decimal('dinner_allowance', 12, 2)->nullable();
            $table->decimal('minimum_dinner_hour', 12, 2)->nullable();
            $table->string('meal_payment_way', 30)->nullable();
            $table->string('weekend_allowance_count', 50)->nullable();
            $table->decimal('holiday_allowance', 12, 2)->nullable();
            $table->boolean('is_ot_basis_wphp')->default(false);
            $table->boolean('is_ot_basis_main')->default(false);
            $table->boolean('is_ot_basis_others_1')->default(false);
            $table->boolean('is_ot_basis_others_2')->default(false);
            $table->text('responsibilities')->nullable();
            $table->unsignedBigInteger('report_to')->nullable();
            $table->text('follow_up_team')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_factories', function (Blueprint $table) {
            $table->id();
            $table->string('factory_no', 50)->nullable();
            $table->boolean('is_running')->default(false);
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('weekend', 50)->nullable();
            $table->string('roster_day', 50)->nullable();
            $table->decimal('stamp_amount', 12, 2)->nullable();
            $table->integer('attendance_bonus_late_days_more_than')->nullable();
            $table->decimal('ot_rate', 12, 2)->nullable();
            $table->string('absent_deduct_from')->nullable();
            $table->string('absent_deduct_special')->nullable();
            $table->decimal('production_subsidy', 12, 2)->nullable();
            $table->string('attendance_id_type')->nullable();
            $table->string('attendance_type')->nullable();
            $table->date('last_earn_leave_count_date')->nullable();
            $table->string('authority_sign')->nullable();
            $table->boolean('apply_special_office_time_in_main')->default(false);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_leave_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50);
            $table->integer('days')->default(0);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_production_bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('sub_section_id')->nullable();
            $table->decimal('percentage', 12, 2)->default(0);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_salary_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Salary Key');
            $table->decimal('medical', 12, 2)->nullable();
            $table->decimal('lunch', 12, 2)->nullable();
            $table->decimal('transport', 12, 2)->nullable();
            $table->unsignedBigInteger('salary_approved_person_1')->nullable();
            $table->unsignedBigInteger('salary_approved_person_2')->nullable();
            $table->unsignedBigInteger('salary_approved_person_3')->nullable();
            $table->unsignedBigInteger('salary_approved_person_4')->nullable();
            $table->unsignedBigInteger('salary_approved_person_5')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payment_methods');
        Schema::dropIfExists('hr_salary_keys');
        Schema::dropIfExists('hr_production_bonuses');
        Schema::dropIfExists('hr_leave_infos');
        Schema::dropIfExists('hr_factories');
        Schema::dropIfExists('hr_designations');
        Schema::dropIfExists('hr_bonus_policies');
        Schema::dropIfExists('hr_bonus_titles');
    }
};
