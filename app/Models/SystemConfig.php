<?php

namespace App\Models;

use App\Trails\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SystemConfig extends Model
{
    //use HasUuid;

   // public $incrementing = false;

    protected $fillable = ['option_name', 'option_value'];

    public static function boot()
    {
        parent::boot();

        /*self::creating(function ($model){
            $admin = Auth::user('admin');
            $model->created_by = $admin->id;
        });

        self::updating(function ($model){
            $admin = Auth::user('admin');
            $model->updated_by = $admin->id;
        });*/

    }
}
