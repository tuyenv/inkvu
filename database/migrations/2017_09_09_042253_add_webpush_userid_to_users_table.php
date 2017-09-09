<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebpushUseridToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notify_settings', function (Blueprint $table) {
            $table->string('web_push_userid')->nullable();
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
            $table->dropColumn('web_push_userid');
        });
    }
}
