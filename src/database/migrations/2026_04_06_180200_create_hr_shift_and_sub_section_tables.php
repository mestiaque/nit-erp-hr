<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_shift');
            $table->string('name_of_shift_bn')->nullable();
            $table->time('shift_starting_time')->nullable();
            $table->time('red_marking_on')->nullable();
            $table->time('shift_closing_time')->nullable();
            $table->boolean('shift_closing_time_next_day')->default(false);
            $table->time('over_time_allowed_up_to')->nullable();
            $table->boolean('over_time_allowed_up_to_next_day')->default(false);
            $table->time('over_time_1_allowed_up_to')->nullable();
            $table->boolean('over_time_1_allowed_up_to_next_day')->default(false);
            $table->time('card_accept_from')->nullable();
            $table->time('card_accept_to')->nullable();
            $table->boolean('card_accept_to_next_day')->default(false);
            $table->string('meal_option')->nullable();
            $table->decimal('tiffin_allowance', 12, 2)->nullable();
            $table->boolean('no_lunch_hour_holiday')->default(false);
            $table->boolean('dinner_allowance')->default(false);
            $table->string('dinner_count_option')->nullable();
            $table->boolean('double_shift')->default(false);
            $table->text('weekly_overtime_allowed')->nullable();
            $table->text('weekly_ot_sat')->nullable();
            $table->text('weekly_ot_sun')->nullable();
            $table->text('weekly_ot_mon')->nullable();
            $table->text('weekly_ot_tue')->nullable();
            $table->text('weekly_ot_wed')->nullable();
            $table->text('weekly_ot_thu')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_sub_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->string('salary_type', 30)->default('fixed_rate');
            $table->integer('approve_man_power')->nullable();
            $table->unsignedBigInteger('roster_shift_id')->nullable();
            $table->boolean('is_individual_roster')->default(false);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_weeks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('day_number')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_working_places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_working_places');
        Schema::dropIfExists('hr_weeks');
        Schema::dropIfExists('hr_sub_sections');
        Schema::dropIfExists('hr_shifts');
    }
};
