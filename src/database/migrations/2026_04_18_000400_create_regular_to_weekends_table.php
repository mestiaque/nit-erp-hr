<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('regular_to_weekends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->date('date');
            $table->enum('type', ['regular', 'weekend']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regular_to_weekends');
    }
};
