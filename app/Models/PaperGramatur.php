<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperGramatur extends Model
{
  use SoftDeletes;
  protected $table = 'paper_gramatures';
  protected $dates = ['deleted_at'];
}
