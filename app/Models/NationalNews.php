<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NationalNews extends Model
{
    protected $fillable = [
        'headline',
        'story_board',
        'headline_image',
        'editor_name',
        'created_at',
        'updated_at',
    ];
}
