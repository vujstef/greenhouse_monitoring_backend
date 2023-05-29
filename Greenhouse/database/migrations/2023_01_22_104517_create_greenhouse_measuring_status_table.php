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
        Schema::create('greenhouse_measuring_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('greenhouse_id')->unsigned();
            $table->unsignedBigInteger('measuring_status_id')->unsigned();

            $table->foreign('greenhouse_id')->references('id')->on('greenhouses')->onDelete('cascade');
            $table->foreign('measuring_status_id')->references('id')->on('measuring_statuses')->onDelete('cascade');
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
        Schema::dropIfExists('greenhouse_measuring_status');
    }
};
