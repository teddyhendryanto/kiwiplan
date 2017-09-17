<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdiExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edi_exports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('yyyy')->unsigned();
            $table->integer('counter')->unsigned();
            $table->string('edi_counter');
            $table->string('order_file');
            $table->string('receiving_file');
            $table->string('exec_type')->nullable();
            $table->string('rstatus',2)->default('NW');
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('edi_exports');
    }
}
