<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressChiefdom extends Model
{
    //
    protected $fillable= ['name'];
    protected $hidden = ["created_at", "updated_at"];
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
