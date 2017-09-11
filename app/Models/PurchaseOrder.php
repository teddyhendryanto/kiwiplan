<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];

  public function site(){
    return $this->belongsTo('App\Models\Site');
  }

  public function supplier(){
    return $this->belongsTo('App\Models\PaperSupplier');
  }

  public function purchase_order_details(){
    return $this->hasMany('App\Models\PurchaseOrderDetail');
  }
}
