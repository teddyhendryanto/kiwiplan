<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdiExport extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function edi_export_details(){
    return $this->hasMany('App\Models\EdiExportDetail','edi_export_id','id');
  }

  public function edi_export_histories(){
    return $this->hasMany('App\Models\EdiExportHistory','edi_export_id','id');
  }
}
