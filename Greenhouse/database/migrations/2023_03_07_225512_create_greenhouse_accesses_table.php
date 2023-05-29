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
        Schema::create('greenhouse_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('greenhouse_id');
            $table->unsignedBigInteger('admin_id');
            $table->boolean('air_temperature')->default(false);
            $table->boolean('relative_air_humidity')->default(false);
            $table->boolean('soil_temperature')->default(false);
            $table->boolean('relative_humidity_of_the_soil')->default(false);
            $table->boolean('lighting_intensity')->default(false);
            $table->boolean('outside_air_temperature')->default(false);
            $table->boolean('wind_speed')->default(false);
            $table->boolean('water_level')->default(false);
            $table->boolean('opening')->default(false);
            $table->boolean('closing')->default(false);
            $table->boolean('opened')->default(false);
            $table->boolean('closed')->default(false);
            $table->boolean('filling')->default(false);
            $table->boolean('emptying')->default(false);
            $table->boolean('full')->default(false);
            $table->boolean('empty')->default(false);
            $table->boolean('remote_mode')->default(false);
            $table->foreign('greenhouse_id')->references('id')->on('greenhouses')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('greenhouse_accesses');
    }
};
