<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderFrequent extends Model
{
  public function supplier(){
    return $this->belongsTo('App\Models\PaperSupplier');
  }
}
