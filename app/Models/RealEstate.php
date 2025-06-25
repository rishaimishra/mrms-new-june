<?php

namespace App\Models;

use Eav\Model;

class RealEstate extends Model
{
    const ENTITY  = 'real_estate';

    protected $fillable = [
        'user_id', 'name', 'title', 'about', 'address_id', 'area_id', 'chiefdom_id',
        'section_id', 'type', 'digital_addresses', 'map_addresses', 'is_available',
        'latitude', 'longitude', 'availability_times', 'meta_tag1', 'meta_tag2', 'meta_tag3'
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'availability_times' => 'array'
    ];

    const TYPE_PLACE = 'place';


    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id')->withDefault();
    }

    public function addressArea()
    {
        return $this->belongsTo(AddressArea::class, 'area_id')->withDefault();
    }

    public function addressChiefdom()
    {
        return $this->belongsTo(AddressChiefdom::class, 'chiefdom_id')->withDefault();
    }

    public function addressSection()
    {
        return $this->belongsTo(AddressSection::class, 'section_id')->withDefault();
    }

    public function images()
    {
        return $this->hasMany(RealEstateImage::class);
    }

    public function categories()
    {

        return $this->belongsToMany(
            RealEstateCategory::class,
            'real_estate_real_estate_categories',
            'real_id',
            'real_category_id'
        );
    }

    public function interestedUsers()
    {
        return $this->belongsToMany(User::class, 'real_estate_interested_user');
    }

    public function getMainTableAttribute($loadedAttributes)
    {
        $mainTableAttributeCollection = $loadedAttributes->filter(function ($attribute) {
            return $attribute->isStatic();
        });

        $mainTableAttribute = $mainTableAttributeCollection->code()->toArray();

        $mainTableAttribute = array_merge($mainTableAttribute, $this->getFillable());

        $mainTableAttribute[] = 'entity_id';
        $mainTableAttribute[] = 'attribute_set_id';

        $mainTableAttribute[] = 'created_at';
        $mainTableAttribute[] = 'updated_at';

        return $mainTableAttribute;
    }
}
