<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatorToNotifySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notify_settings', function (Blueprint $table) {
            $table->integer('creator')->unsigned()->nullable();
            $table->index('creator');
            $table->foreign('creator')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notify_settings', function (Blueprint $table) {
            $table->dropColumn('creator');
        });
    }
}
