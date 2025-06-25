<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;


class SellerBusinessDetail extends Model
{
    use Notifiable;

    protected $fillable = ['firstname', 'middlename', 
    'surname', 'streetnumber', 'streetname', 
    'area_id', 'ward_id', 'section_id', 'address_id',
    'chiefdom_id', 'province_id', 'mobile_1', 'email',
    'business_logo', 'business_corods', 'com_email', 
    'opening_time', 'closing_time', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }


    public function address()
    {
        return $this->belongsTo(Address::class,'address_id')->withDefault();
    }

    public function addressArea()
    {
        return $this->belongsTo(AddressArea::class,'area_id')->withDefault();
    }

    public function addressChiefdom()
    {
        return $this->belongsTo(AddressChiefdom::class,'chiefdom_id')->withDefault();
    }

    public function addressSection()
    {
        return $this->belongsTo(AddressSection::class,'section_id')->withDefault();
    }

}
