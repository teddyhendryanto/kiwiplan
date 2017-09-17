<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExportFlagToVerifyRolls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verify_rolls', function (Blueprint $table) {
          $table->boolean('exported')->default(false)->after('verify_date');
          $table->integer('exported_count')->default(0)->after('exported');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verify_rolls', function (Blueprint $table) {
          $table->dropColumn('exported');
          $table->dropColumn('exported_count');
        });
    }
}
