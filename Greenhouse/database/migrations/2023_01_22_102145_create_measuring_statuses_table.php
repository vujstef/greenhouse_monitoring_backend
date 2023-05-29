<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measuring_statuses', function (Blueprint $table) {
            $table->id();
            $table->float('air_temperature')->nullable();
            $table->float('relative_air_humidity')->nullable();
            $table->float('soil_temperature')->nullable();
            $table->float('relative_humidity_of_the_soil')->nullable();
            $table->float('lighting_intensity')->nullable();
            $table->float('outside_air_temperature')->nullable();
            $table->float('wind_speed')->nullable();
            $table->float('water_level')->nullable();
            $table->integer('opening')->nullable();
            $table->integer('closing')->nullable();
            $table->integer('opened')->nullable();
            $table->integer('closed')->nullable();
            $table->integer('filling')->nullable();
            $table->integer('emptying')->nullable();
            $table->integer('full')->nullable();
            $table->integer('empty')->nullable();
            $table->integer('remote_mode')->nullable();
            $table->dateTime('time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measuring_statuses');
    }
};
