<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'site_id', 'yyyymm', 'counter', 'supplier_id',
    'po_num', 'po_num_ex', 'po_date', 'po_qty',
    'due_date', 'contact_person', 'term', 'remarks1',
    'transfered_id', 'transfered', 'transfered_count', 'rstatus',
    'created_by', 'updated_by', 'deleted_by',
    'created_at', 'updated_at', 'deleted_at'
  ];

  public function site(){
    return $this->belongsTo('App\Models\Site');
  }

  public function supplier(){
    return $this->belongsTo('App\Models\PaperSupplier');
  }

  public function purchase_order_details(){
    return $this->hasMany('App\Models\PurchaseOrderDetail');
  }

  public function purchase_order_transfers(){
    return $this->hasOne('App\Models\PurchaseOrderTransfer');
  }
}
