<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationType extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];

  protected $fillable = [
      'type', 'description',
      'rstatus',
      'created_by', 'updated_by', 'deleted_by',
      'created_at', 'updated_at', 'deleted_at'
  ];

  public function users()
  {
    return $this->belongsToMany('App\User','notification_type_user');
  }
}
