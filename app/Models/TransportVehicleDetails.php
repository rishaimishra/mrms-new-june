<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportVehicleDetails extends Model
{
    // Specify the table name if it's different from the plural of the model name
    protected $table = 'transport_vehicle_details';

    // Specify any fillable fields for mass assignment
    protected $fillable = [
        'user_id', 
        'transport_type', 
        'transport_make',
        'transport_model',
        'manufacture_year',
        'air_conditioning',
        'audio_radio',
        'vehicle_images',
        'vehicle_license',
        'vehicle_insurance',
        'driver_name',
        'driver_address',
        'driver_mobile',
        'driver_email',
        'driver_license',
        'bank_name',
        'account_name',
        'swift_code',
        'efsc_code'
    ];

    /**
     * Define the relationship with the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
