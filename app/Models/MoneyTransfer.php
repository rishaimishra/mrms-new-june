<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends Model
{
    protected $table = 'money_transfer';

    // Specify the primary key if it's not `id`
    protected $primaryKey = 'id';

    // Enable timestamps if you have `created_at` and `updated_at` columns
    public $timestamps = true;

    //
    protected $fillable = [
        'customer_id',
        'seller_id',
        'originating_country',
        'transaction_code_number',
        'sender_name',
        'sender_address',
        'sender_mobile',
        'receiver_name',
        'receiver_address',
        'receiver_mobile',
        'amount_sent',
        'amount_sent_destination_country'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }
}
