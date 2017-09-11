<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperKey extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];

  public function supplier(){
    return $this->belongsTo('App\Models\PaperSupplier');
  }
}
