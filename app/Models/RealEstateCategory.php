<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateCategory extends Model
{
    protected $fillable = ['name', 'image','parent_id', 'is_active','sequence','sponsor_text','background_image'];
    const DOCUMENT_DIR  = 'places/category';

    protected $appends = ['full_image'];
    protected $casts = ['sequence' => 'double'];
    function getFullImageAttribute() {

        if($this->image){
            return  asset('storage/' . $this->image);
        }
        return  asset('storage/images.png');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id')->withDefault();
    }

    public function children() {
        return $this->hasMany(static::class, 'parent_id')->orderBy('name', 'asc')->with('Children');
    }

    public function haschildren() {
        return ["children" => count($this->hasMany(static::class, 'parent_id'))];
    }
    /*public function scopeChildren($query)
    {
        return $query->where('is_active', true);
    }*/

    public function scopeRoot($query)
    {
        return $query->where('parent_id', null);
    }

    public function getName()
    {
        return ucfirst($this->name);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeChild($query)
    {
        return $query->where('parent_id', $this->id);

    }
    public function categories()
    {
        return $this->belongsToMany(Place::class);
    }
}
