<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'purchase_order_id', 'paper_quality', 'paper_gramatures',
    'paper_width', 'paper_qty', 'um',
    'paper_price', 'tax', 'remarks',
    'transfered_id',
    'rstatus',
    'created_by', 'updated_by', 'deleted_by',
    'created_at', 'updated_at', 'deleted_at'
  ];

  public function purchase_order(){
    return $this->belongsTo('App\Models\PurchaseOrder');
  }
}
