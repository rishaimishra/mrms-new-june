<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessLicense extends Model
{
    protected $fillable = [

    ];

    protected $table = 'business_license';

    public function category(){
        return $this->belongsTo(BusinessType::class,'BusinessCategory');
    }
}
