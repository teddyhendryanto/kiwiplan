<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEdiExportIdToEdiExportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('edi_export_details', function (Blueprint $table) {
          $table->integer('edi_export_id')->unsigned()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edi_export_details', function (Blueprint $table) {
          $table->dropColumn('edi_export_id');
        });
    }
}
