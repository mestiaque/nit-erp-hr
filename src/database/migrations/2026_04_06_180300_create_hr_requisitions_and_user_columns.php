<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_no', 50);
            $table->string('title');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('requisition_date')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'hr_designation_id')) {
                $table->unsignedBigInteger('hr_designation_id')->nullable()->after('designation_id');
            }
            if (!Schema::hasColumn('users', 'hr_factory_id')) {
                $table->unsignedBigInteger('hr_factory_id')->nullable()->after('hr_designation_id');
            }
            if (!Schema::hasColumn('users', 'hr_sub_section_id')) {
                $table->unsignedBigInteger('hr_sub_section_id')->nullable()->after('hr_factory_id');
            }
            if (!Schema::hasColumn('users', 'hr_working_place_id')) {
                $table->unsignedBigInteger('hr_working_place_id')->nullable()->after('hr_sub_section_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drops = [];
            foreach (['hr_designation_id', 'hr_factory_id', 'hr_sub_section_id', 'hr_working_place_id'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $drops[] = $column;
                }
            }
            if ($drops) {
                $table->dropColumn($drops);
            }
        });

        Schema::dropIfExists('hr_requisitions');
    }
};
