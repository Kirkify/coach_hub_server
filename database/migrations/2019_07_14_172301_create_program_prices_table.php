<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('program_id');
            $table->foreign('program_id')->references('id')->on('programs');
            $table->string('guid', 10);
            $table->string('name', 180);
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('capacity');
            $table->boolean('has_wait_list');
            $table->json('sub_options')->nullable();
            $table->unsignedTinyInteger('sub_options_preset')->nullable();
            $table->unsignedTinyInteger('multi_sub_options_required')->nullable();
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
        Schema::dropIfExists('program_prices');
    }
}
