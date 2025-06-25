<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreetNameApplicationForm extends Model
{
    public function street_name_application()
    {
        return $this->belongsTo(StreetNameApplication::class);
    }

    public function constituency_detail()
    {
        return $this->belongsTo(Constituency::class,'constituency','id');
    }

    public function ward_detail()
    {
        return $this->belongsTo(Ward::class,'ward','id');
    }

    public function community_detail()
    {
        return $this->belongsTo(Community::class,'name_of_community','id');
    }
}
