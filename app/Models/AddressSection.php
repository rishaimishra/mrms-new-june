<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressSection extends Model
{
    //
    protected $fillable= ['name'];

    protected $hidden = ["created_at", "updated_at"];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function addressArea()
    {
        return $this->hasMany(AddressArea::class);
    }
}
