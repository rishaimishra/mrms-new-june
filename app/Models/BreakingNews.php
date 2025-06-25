<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakingNews extends Model
{
    protected $table = 'breaking_news';
    protected $fillable = [
        'seller_id',
        'breaking_news',
        'created_at',
        'updated_at',
    ];
}
