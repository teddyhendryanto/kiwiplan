<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];
  protected $fillable = ['rstatus','deleted_by','deleted_at'];

  public function purchase_order(){
    return $this->belongsTo('App\Models\PurchaseOrder');
  }
}
