<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;


class StarTransaction extends Model
{
    use Notifiable;

    protected $fillable = ['user_id','transaction_id','transaction_status','amount','subscription_type'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }


}
