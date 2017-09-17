<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('site_id')->unsigned();
          $table->integer('yyyymm')->unsigned();
          $table->integer('counter');
          $table->integer('supplier_id')->unsigned();
          $table->string('po_num');
          $table->string('po_num_ex')->nullable();
          $table->datetime('po_date');
          $table->float('po_qty');
          $table->string('due_date');
          $table->string('contact_person')->nullable();
          $table->integer('term');
          $table->string('remarks1')->nullable();
          $table->string('remarks2')->nullable();
          $table->string('remarks3')->nullable();
          $table->string('rstatus',2)->default('NW');
          $table->string('created_by');
          $table->string('updated_by')->nullable();
          $table->string('deleted_by')->nullable();
          $table->timestamps();
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
