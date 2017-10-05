<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifyToReceiveRollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receive_rolls', function (Blueprint $table) {
          $table->boolean('verify')->default(false)->after('selling_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receive_rolls', function (Blueprint $table) {
          $table->dropColumn('verify');
        });
    }
}
