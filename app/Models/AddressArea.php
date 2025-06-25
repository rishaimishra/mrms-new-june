<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressArea extends Model
{
    //
    protected $fillable= ['name'];
    protected $hidden = ["created_at", "updated_at"];
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function addressSection()
    {
        return $this->belongsTo(AddressSection::class)->withDefault();
    }
}
