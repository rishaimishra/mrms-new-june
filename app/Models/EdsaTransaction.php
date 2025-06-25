<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;


class EdsaTransaction extends Model
{
    use Notifiable;

    protected $fillable = ['user_id','transaction_id','transaction_status','meter_number','meter_reading','amount','edsa_tariff_category','gst','service_charge','edsa_token'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }


}
