<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_base_profile_id')->unsigned();
            $table->foreign('coach_base_profile_id')->references('id')->on('coach_base_profiles')->onDelete('cascade');
            $table->longText('coaching_experience');
            $table->longText('athletic_highlights');
            $table->longText('session_plan');
            $table->string('one_sentence_bio', 180);
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
        Schema::dropIfExists('coach_profiles');
    }
}
