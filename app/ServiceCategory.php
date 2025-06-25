<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $table = 'service_category';

    protected $fillable = [
        'name', 
        'image', 
        'is_active', 
        'sequence', 
        'sponsor_text',
        'background_image',
        'created_at',
        'updated_at',
        'parent_id'
    ];

    // Define the parent category relationship
    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    // Define the child categories relationship
    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }
}
