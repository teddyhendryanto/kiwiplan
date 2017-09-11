<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperSupplier extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  public function keys(){
    return $this->hasMany('App\Models\PaperKey');
  }

  public function purchase_orders(){
    return $this->hasMany('App\Models\PurchaseOrder');
  }

  public function purchase_order_frequents(){
    return $this->hasMany('App\Models\PurchaseOrderFrequent','supplier_id','id');
  }

}
