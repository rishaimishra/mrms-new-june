<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{

    use  Notifiable;

    //public $incrementing = false;

    protected $fillable = ['user_id','order_type','transaction_id', 'order_status','address_id','address_area_id', 'address_chiefdom_id',
        'address_section_id', 'digital_addresses', 'latitude','longitude','digital_administration','transport','fuel','gst','sub_total','tip','grand_total'];

    public function address()
    {
        return $this->belongsTo(Address::class,'address_id')->withDefault();
    }

    public function addressArea()
    {
        return $this->belongsTo(AddressArea::class,'address_area_id')->withDefault();
    }

    public function addressChiefdom()
    {
        return $this->belongsTo(AddressChiefdom::class,'address_chiefdom_id')->withDefault();
    }

    public function addressSection()
    {
        return $this->belongsTo(AddressSection::class,'address_section_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class,'order_id');
    }
}
