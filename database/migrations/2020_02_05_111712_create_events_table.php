<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('video_src')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('artist')->nullable();
            $table->integer('zoom')->nullable();
            $table->dateTime('starts_on');
            $table->dateTime('finishes_on');
            $table->integer('starting_tickets')->default(0);
            $table->float('starting_price', 8, 2)->nullable();
            $table->integer('mid_tickets')->default(0);
            $table->float('mid_price', 8, 2)->nullable();
            $table->integer('end_tickets')->default(0);
            $table->float('end_price', 8, 2)->nullable();
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
        Schema::dropIfExists('events');
    }
}
