<?php
  namespace App\Models;

  use Zizaco\Entrust\EntrustRole;
  use Illuminate\Database\Eloquent\SoftDeletes;

  class Role extends EntrustRole
  {
    use SoftDeletes;

    protected $dates = ['deleted_at'];
  }
