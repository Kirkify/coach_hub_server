<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_base_profile_id')->unsigned();
            $table->foreign('coach_base_profile_id')->references('id')->on('coach_base_profiles')->onDelete('cascade');
            $table->string('name', 180);
            $table->string('description')->nullable();
            $table->string('street_number');
            $table->string('street_name');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('locations');
    }
}
