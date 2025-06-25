<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'app_type',
        'action',
        'message',
        'notification',
        'created_at',
        'updated_at',
    ];
}
