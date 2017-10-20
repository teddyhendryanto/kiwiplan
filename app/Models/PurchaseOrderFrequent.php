<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderFrequent extends Model
{
  use SoftDeletes;
  
  public function supplier(){
    return $this->belongsTo('App\Models\PaperSupplier');
  }
}
