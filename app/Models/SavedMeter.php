<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;


class SavedMeter extends Model
{
    use Notifiable;

    protected $fillable = ['user_id','meter_number','meter_name'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    
}