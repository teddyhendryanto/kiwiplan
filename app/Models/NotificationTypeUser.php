<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTypeUser extends Model
{
    public $timestamps = false;
    protected $table = 'notification_type_user';
    protected $fillable = ['id', 'notification_type_id', 'user_id'];
}
