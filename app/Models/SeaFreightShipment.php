<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class SeaFreightShipment extends Model
{
    protected $fillable = [
        'region',
        'freight_number',
        'consignor_name',
        'consignor_address',
        'consignor_mobile_no',
        'consignee_name',
        'consignee_address',
        'consignee_mobile_no',
        'transport_type',
        'vessel_name',
        'port_of_loading',
        'port_of_discharge',
        'container_batch_no',
        'bill_of_lading_no',
        'description_of_goods',
        'freight_charges',
        'seller_id',
        'seller_name',
        'estimated_date_departure',
        'estimated_date_arrival',
        'booking_reference_id',
        'Barrel',
        '5_CM_box',
        '10_CM_box',
        '250_CM_box',
        'car',
        'weight',
        'other_items',
        'other_items_quantity',
        'account_id'
    ];   

    public function deliveryBook()
    {
        return $this->hasOne(DeliveryBookShipment::class, 'delivery_booking_reference');
    }
    public function get_user_detail(){
        return $this->belongsTo(SellerDetail::class, 'seller_id','user_id');
    }
    public function get_user(){
        return $this->belongsTo(User::class, 'seller_id','id');
    }
}