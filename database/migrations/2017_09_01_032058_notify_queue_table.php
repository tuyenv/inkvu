<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotifyQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_queue', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('link_id')->unsigned();
            $table->enum('status', ['init', 'process', 'failed', 'done']);
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('link_id');
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notify_queue');
    }
}
