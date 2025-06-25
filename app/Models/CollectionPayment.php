<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionPayment extends Model
{
    protected $fillable = [
        'customer_id',
        'seller_id',
        'customer_name',
        'customer_address',
        'telephone',
        'email',
        'nin',
        'loan_type',
        'item_loaned',
        'cost_of_loaned_item',
        'payback',
        'loan_term',
        'monthly_spread',
        'no_of_months',
        'payment',
        'balance',
        'interest',
        'loan_officer',
        'payee_name',
        'payment_mode',
        'collection_agent_name',
        'digital_payment_type',
        'payment_date_timestamp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }
}
