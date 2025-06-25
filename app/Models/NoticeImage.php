<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeImage extends Model
{
    protected $table = 'notice_images';
    protected $fillable = [
        'notice_id',
        'image',
        'created_at',
        'updated_at',
    ];
   
}
