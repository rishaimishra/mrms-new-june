<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatArideCategory extends Model
{
    protected $table = 'chat_a_ride_category';

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
