<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotifyDeliverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notify_deliver', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('email', 255)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->enum('push_type', ['web', 'email', 'mobile']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notify_deliver', function (Blueprint $table) {
            //
        });
    }
}
