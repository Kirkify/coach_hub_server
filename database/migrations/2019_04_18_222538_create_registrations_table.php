<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_base_profile_id')->unsigned();
            $table->foreign('coach_base_profile_id')->references('id')->on('coach_base_profiles')->onDelete('cascade');
            $table->string('first_name', 180);
            $table->string('last_name');
            $table->string('email');
            $table->date('date_of_birth');
            $table->string('notes')->nullable();
            $table->integer('program_id')->unsigned();
            $table->foreign('program_id')->references('id')->on('programs');
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
        Schema::dropIfExists('registrations');
    }
}
