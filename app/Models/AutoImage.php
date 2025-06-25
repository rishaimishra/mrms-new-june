<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AutoImage extends Model
{
    protected $fillable = ['image','auto_id'];

    protected $appends = ['full_image'];

    function getFullImageAttribute() {

        if($this->image){
            return  asset('storage/' . $this->image);
        }
        return  asset('storage/images.png');
    }
}