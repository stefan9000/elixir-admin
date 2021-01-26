<?php

use App\Ticket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id')
                ->foreign()
                ->references('id')
                ->on('events')
                ->onDelete('CASCADE');
            $table->unsignedBigInteger('user_id')
                ->foreign()
                ->references('id')
                ->on('users')
                ->onDelete('SET NULl')
                ->nullable();
            $table->tinyInteger('used')->default(Ticket::NOT_USED);
            $table->float('price', 8, 2);
            $table->string('code');
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
        Schema::dropIfExists('tickets');
    }
}
