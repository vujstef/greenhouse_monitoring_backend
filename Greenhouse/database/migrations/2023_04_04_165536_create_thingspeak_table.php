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
        Schema::create('thingspeaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('greenhouse_id');
            $table->string('channel_id');
            $table->string('read_key');
            $table->string('write_key');
            $table->foreign('greenhouse_id')->references('id')->on('greenhouses')->onDelete('cascade');
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
        Schema::dropIfExists('thingspeaks');
    }
};
