<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatARideCategory extends Model
{
    protected $appends = ['image_url'];
    protected $table = 'chat_a_ride_category'; // Set your table name here
    protected $fillable = [
        'name',
        'image',
        'is_active',
        'sequence',
        'sponser_text',
        'background_image',
        'created_at',
        'updated_at',
    ];
    public function getImageUrlAttribute()
    {
        return url('storage/' . $this->image); // Adjust based on your storage path
    }
}
