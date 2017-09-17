<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiveRoll extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function site(){
    return $this->belongsTo('App\Models\Site');
  }

  public function purchase_order(){
    return $this->belongsTo('App\Models\PurchaseOrder','po_id','id');
  }

  public function supplier(){
    return $this->belongsTo('App\Models\PaperSupplier');
  }

  public function verify_roll(){
    return $this->hasOne('App\Models\VerifyRoll');
  }
}
