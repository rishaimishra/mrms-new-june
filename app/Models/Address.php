<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $fillable = [
        'ward_number', 'constituency', 'district','province'
    ];
    protected $hidden = ["created_at", "updated_at"];

    public function addressArea()
    {
        return $this->hasMany(AddressArea::class);
    }

    public function addressChiefdom()
    {
        return $this->hasMany(AddressChiefdom::class);
    }

    public function addressSection()
    {
        return $this->hasMany(AddressSection::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
