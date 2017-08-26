<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator')->unsigned();
            $table->integer('notify_user')->unsigned();
            $table->string('email', 255);
            $table->string('mobile', 20);
            $table->boolean('web_notify');
            $table->boolean('mobile_notify');
            $table->boolean('email_notify');
            $table->timestamps();

            $table->index('creator');
            $table->foreign('creator')->references('id')->on('users')->onDelete('cascade');
            $table->index('notify_user');
            $table->foreign('notify_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notify_settings');
    }
}
