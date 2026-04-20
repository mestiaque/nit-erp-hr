<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('production_rates', function (Blueprint $table) {
            $table->id();
            $table->string('local_agent')->nullable();
            $table->string('buyer')->nullable();
            $table->string('style_name')->nullable();
            $table->string('style_number')->nullable();
            $table->string('gauge')->nullable();
            $table->integer('order_qty')->nullable();
            $table->string('merchandiser')->nullable();
            $table->string('process')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->string('pro_process')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_rates');
    }
};
