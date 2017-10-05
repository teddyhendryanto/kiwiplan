<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransferedToPurchaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
          $table->integer('transfered_id')->nullable()->after('remarks3');
          $table->boolean('transfered')->after('transfered_id')->default(false);
          $table->integer('transfered_count')->after('transfered')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
          $table->dropColumn('transfered_id');
          $table->dropColumn('transfered');
          $table->dropColumn('transfered_count');
        });
    }
}
