<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_locations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('bn_name')->nullable();
            $table->string('code', 50)->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_locations');
    }
};
