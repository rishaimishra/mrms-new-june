<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }
}
