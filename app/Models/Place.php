<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    const TYPE_PLACE = 'place';

    protected $fillable = ['user_id','name','about', 'address_id','area_id', 'chiefdom_id',
                            'section_id', 'type', 'digital_addresses', 'map_addresses',
                            'latitude', 'longitude','availability_times','meta_tag1','meta_tag2','meta_tag3'];

    const DOCUMENT_DIR  = 'knowledgebase/category';

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'availability_times' => 'array'
    ];

    public $timestamps = true;

    public function address()
    {
        return $this->belongsTo(Address::class,'address_id')->withDefault();
    }

    public function addressArea()
    {
        return $this->belongsTo(AddressArea::class,'area_id')->withDefault();
    }

    public function addressChiefdom()
    {
        return $this->belongsTo(AddressChiefdom::class,'chiefdom_id')->withDefault();
    }

    public function addressSection()
    {
        return $this->belongsTo(AddressSection::class,'section_id')->withDefault();
    }

    public function images()
    {
        return $this->hasMany(PlaceImage::class);
    }

    public function categories()
    {
        //return $this->belongsTo(BusinessCategory::class);
        return $this->belongsToMany(PlaceCategory::class,
            'place_place_categories',
            'place_id',
            'place_category_id');
    }
}
