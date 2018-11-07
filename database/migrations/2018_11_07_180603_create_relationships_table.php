<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema based upon https://www.codedodle.com/2014/12/social-network-friends-database.html
        Schema::create('relationships', function (Blueprint $table) {
            $table->unsignedInteger('user_one_id');
            $table->unsignedInteger('user_two_id');
            $table->tinyInteger('status');
            $table->unsignedInteger('action_user_id');
            $table->timestamps();

            $table->unique(['user_one_id', 'user_two_id']);
            $table->foreign('user_one_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relationships');
    }
}
