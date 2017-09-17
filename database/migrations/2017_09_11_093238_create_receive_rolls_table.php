<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiveRollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receive_rolls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->integer('po_id')->unsigned();
            $table->string('po_num');
            $table->datetime('receive_date');
            $table->time('receive_time');
            $table->integer('supplier_id')->unsigned();
            $table->string('paper_key');
            $table->string('paper_width');
            $table->float('paper_price');
            $table->integer('rss_store')->default(1);
            $table->string('rss_loc')->default('MAIN');
            $table->integer('yyyy');
            $table->string('rtype');
            $table->integer('counter');
            $table->string('supplier_roll_id')->nullable();
            $table->string('unique_roll_id');
            $table->float('roll_weight');
            $table->float('roll_diameter');
            $table->string('doc_ref')->nullable();
            $table->string('wagon')->nullable();
            $table->string('remarks')->nullable();
            $table->datetime('rate_date')->nullable();
            $table->float('selling_rate')->nullable();
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
        Schema::dropIfExists('receive_rolls');
    }
}
