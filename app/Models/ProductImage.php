<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['image','place_id','product_id'];
    protected $appends = ['full_image'];
	function getFullImageAttribute() {

        if($this->image){
            return  asset('storage/' . $this->image);
        }
        return  asset('storage/images.png');
    }

}