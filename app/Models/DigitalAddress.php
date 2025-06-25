<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use TeamTNT\TNTSearch\Indexer\TNTIndexer;

/**
 * @property mixed area_name
 */
class DigitalAddress extends Model
{


    protected $fillable = ['type', 'latitude', 'longitude', 'digital_addresses', 'open_location_code'];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function address()
    {
        return $this->belongsTo(Address::class)->withDefault();
    }

    public function addressArea()
    {
        return $this->belongsTo(AddressArea::class)->withDefault();
    }

    public function addressChiefdom()
    {
        return $this->belongsTo(AddressChiefdom::class)->withDefault();
    }

    public function addressSection()
    {
        return $this->belongsTo(AddressSection::class)->withDefault();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray('digital_addresses');
        $array['nameNgrams'] = utf8_encode((new TNTIndexer)->buildTrigrams($this->name));
        /*$array =  [
            'id' => $this->id,
            'digital_addresses' => $this->digital_addresses

        ];*/
        // Customize array...

        return $array;
    }

    public function searchableAs()
    {
        //$array = $this->toArray('area_name');

        return 'digitalAddresses';
    }
}
