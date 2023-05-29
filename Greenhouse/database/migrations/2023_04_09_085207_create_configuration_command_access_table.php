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
        Schema::create('configuration_command_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('greenhouse_id');
            $table->unsignedBigInteger('admin_id');
            $table->foreign('greenhouse_id')->references('id')->on('greenhouses')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('min_air_temp')->default(false);
            $table->boolean('min_wind_speed')->default(false);
            $table->boolean('max_soil_temp')->default(false);
            $table->boolean('max_soil_humidity')->default(false);
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
        Schema::dropIfExists('configuration_command_access');
    }
};
