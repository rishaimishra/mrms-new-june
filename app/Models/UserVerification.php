<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    //
    protected $fillable = ['identity', 'code', 'expired_at', 'created_at', 'user_id'];
    public $timestamps = false;

    protected $dates = [
        'expired_at'
    ];
}
