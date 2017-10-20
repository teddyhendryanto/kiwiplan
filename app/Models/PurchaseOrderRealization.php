<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderRealization extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function purchase_order(){
    return $this->belongsTo('App\Models\PurchaseOrder');
  }

  public function receive_rolls(){
    return $this->hasMany('App\Models\ReceiveRoll','po_id','purchase_order_id');
  }

}
