<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessReg extends Model
{
    protected $fillable = [

    ];

    protected $table = 'business_registration';

    public function payments()
    {
        return $this->belongsTo(PaymentHistory::class,'id','business_id');
    }

    public function businessLic()
    {
        return $this->belongsTo(BusinessLicense::class,'id','BusinessRegId');
    }

    public function licenseAmount()
    {
        return $this->belongsTo(LicenseAmountHistory::class,'id','business_id')->OrderBy('id','desc');
    }
}
