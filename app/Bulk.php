<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Bulk extends Model
{
    protected $table = 'property_payments';
    protected $fillable = [
        'property_id',
'admin_user_id','assessment','amount','total','payment_type','payee_name','created_at','updated_at',
'payment_made_year',
    ];
}