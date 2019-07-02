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
            $table->string('program_title', 180);
            $table->string('program_description');
            $table->integer('category')->nullable()->unsigned();
            $table->foreign('category')->references('id')->on('sports');
            $table->date('registration_start');
            $table->date('registration_end');
            $table->date('program_start');
            $table->date('program_end');
            $table->integer('max_participants');
            $table->boolean('has_wait_list');
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations');
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
