<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PlaceImage extends Model
{
    protected $fillable = ['image','place_id'];

	protected $appends = ['full_image'];

    function getFullImageAttribute() {

        if($this->image){
            return  asset('storage/' . $this->image);
        }
        return  asset('storage/images.png');
    }
}