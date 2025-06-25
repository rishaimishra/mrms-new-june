<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;


class SavedStarRechargeCard extends Model
{
    use Notifiable;

    protected $fillable = ['user_id','recharge_card_number','recharge_card_name'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    
}