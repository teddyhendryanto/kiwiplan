<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',5);
            $table->string('short_name');
            $table->string('full_name');
            $table->string('address')->nullable();
            $table->integer('lead_time')->default(0);
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('currency');
            $table->integer('term');
            $table->string('ex_code',5)->nullable();
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
        Schema::dropIfExists('paper_suppliers');
    }
}
