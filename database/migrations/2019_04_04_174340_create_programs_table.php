<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_base_profile_id')->unsigned();
            $table->foreign('coach_base_profile_id')->references('id')->on('coach_base_profiles')->onDelete('cascade');
            $table->string('program_title', 180)->nullable();
            $table->string('program_description', 500)->nullable();
            $table->integer('category')->unsigned()->nullable();
            $table->foreign('category')->references('id')->on('sports');
            $table->date('registration_start')->nullable();
            $table->date('registration_end')->nullable();
            $table->date('program_start')->nullable();
            $table->date('program_end')->nullable();
            $table->boolean('is_live')->default(false);
            $table->integer('location_id')->unsigned()->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->uuid('form_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('programs');
    }
}
