<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateImage extends Model
{
    protected $fillable = ['image','real_estate_id'];

    protected $appends = ['full_image'];

    function getFullImageAttribute() {

        if($this->image){
            return  asset('storage/' . $this->image);
        }
        return  asset('storage/images.png');
    }
}
