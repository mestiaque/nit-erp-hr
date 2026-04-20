<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('production_rate_processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_rate_id');
            $table->string('process');
            $table->decimal('rate', 10, 2);
            $table->string('pro_process')->nullable();
            $table->timestamps();

            $table->foreign('production_rate_id')->references('id')->on('production_rates')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_rate_processes');
    }
};
