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
        Schema::create('management_command_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('greenhouse_id');
            $table->unsignedBigInteger('admin_id');
            $table->foreign('greenhouse_id')->references('id')->on('greenhouses')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('opening_command')->default(false);
            $table->boolean('closing_command')->default(false);
            $table->boolean('filling_command')->default(false);
            $table->boolean('emptying_command')->default(false);
            $table->boolean('remote_mode')->default(false);
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
        Schema::dropIfExists('management_command_access');
    }
};
