<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderTransfer extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];

  public function purchase_order(){
    return $this->belongsTo('App\Models\PurchaseOrder','purchase_order_id','id');
  }
}
