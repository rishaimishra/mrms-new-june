<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineBookingCategory extends Model
{
    protected $table = 'online_booking_category';

    protected $fillable = [
        'name', 
        'image', 
        'is_active', 
        'sequence', 
        'sponsor_text',
        'background_image',
        'created_at',
        'updated_at'
    ];
}
