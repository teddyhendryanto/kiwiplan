<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public function purchase_orders(){
      return $this->hasMany('App\Models\PurchaseOrder');
    }
}
