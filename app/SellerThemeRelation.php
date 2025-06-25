<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerThemeRelation extends Model
{
    //
    protected $fillable = [
        'seller_id',
        'theme_id'
    ];

    public function theme()
    {
        return $this->belongsTo(SellerTheme::class, 'theme_id', 'id');
    }
}
