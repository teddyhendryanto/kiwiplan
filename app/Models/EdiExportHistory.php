<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdiExportHistory extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function edi_export(){
    return $this->belongsTo('App\Models\EdiExport','id','edi_export_id');
  }
}
