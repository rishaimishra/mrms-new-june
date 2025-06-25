<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'go_plan_monthly',
        'go_plan_referal_fee',
        'individual_plan_monthly',
        'individual_plan_referal_fee',
        'business_plan_monthly',
        'business_plan_referal_fee',
        'created_at',
        'updated_at',
    ];
}
