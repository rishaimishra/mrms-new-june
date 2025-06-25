<?php

namespace App\Models;

use Eav\Model;

class Auto extends Model
{
    const ENTITY = 'auto';

    protected $fillable = [
        'user_id', 'name', 'title', 'about', 'address_id', 'area_id', 'chiefdom_id',
        'section_id', 'type', 'digital_addresses', 'map_addresses', 'title',
        'latitude', 'longitude', 'availability_times', 'meta_tag1', 'meta_tag2', 'meta_tag3', 'is_available',
        'background_image'
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'availability_times' => 'array'
    ];

    public $timestamps = true;

    const TYPE_PLACE = 'place';

    /*protected $appends = ['is_interested'];

    function getIsInterestedAttribute() {

        //$attachedIds = $message->Users()->whereIn('id', $request->get('users'))->pluck('id');
        $user = User::find(auth()->id());
        $hasInterested = $user->autos()->where('id', $this->id)->exists();

        return $hasInterested;
    }*/

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
        return $this->hasMany(AutoImage::class);
    }

    public function categories()
    {
        //return $this->belongsTo(BusinessCategory::class);
        return $this->belongsToMany(
            AutoCategory::class,
            'auto_auto_categories',
            'auto_id',
            'auto_category_id'
        );
    }

    public function interestedUsers()
    {
        return $this->belongsToMany(User::class, 'auto_interested_user');
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
