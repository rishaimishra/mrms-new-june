<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }
}
