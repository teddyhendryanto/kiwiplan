<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifyRoll extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function receive_roll(){
    return $this->belongsTo('App\Models\ReceiveRoll', 'receive_roll_id', 'id');
  }

  public function edi_export_details(){
    return $this->hasMany('App\Models\EdiExportDetail','verify_roll_id', 'id');
  }
}
