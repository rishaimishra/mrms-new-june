<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;
use App\SellerThemeRelation;


class SellerDetail extends Model
{
    use Notifiable;

    protected $fillable = ['user_id','store_icon','business_name','tin','street_number','area','ward','section','chiefdon','province','business_coordinates',
    'street_name','business_registration_image','store_name','store_location','store_document_1','store_document_2','mobile1','mobile2','mobile3','business_email','opening_time','closing_time',
    'store_document_3','store_document_4','store_category','store_category_name','is_verified','created_at','updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function themeRelations()
    {
        return $this->hasMany(SellerThemeRelation::class, 'seller_id', 'user_id');
    }

    
}