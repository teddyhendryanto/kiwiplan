<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdiExportDetail extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function edi_export(){
    return $this->belongsTo('App\Models\EdiExport','edi_export_id','id');
  }

  public function verify_roll(){
    return $this->belongsTo('App\Models\VerifyRoll','verify_roll_id','id');
  }
}
