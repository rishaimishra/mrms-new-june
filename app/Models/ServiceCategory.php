<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceCategory extends Model
{
    protected $appends = ['image_url'];
    protected $table = 'service_category'; // Set your table name here
    protected $fillable = [
        'name',
        'image',
        'is_active',
        'sequence',
        'sponser_text',
        'created_at',
        'updated_at',
    ];
    public function getImageUrlAttribute()
    {
        return url('storage/' . $this->image); // Adjust based on your storage path
    }
}
