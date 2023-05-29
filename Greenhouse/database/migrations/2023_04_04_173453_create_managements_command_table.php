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
        Schema::create('managements_command', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('greenhouse_id');
            $table->foreign('greenhouse_id')->references('id')->on('greenhouses');
            $table->integer('opening_command')->nullable();;
            $table->integer('closing_command')->nullable();;
            $table->integer('filling_command')->nullable();;
            $table->integer('emptying_command')->nullable();;
            $table->integer('remote_mode')->nullable();;
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
        Schema::dropIfExists('managements_command');
    }
};
