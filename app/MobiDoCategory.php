<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobiDoCategory extends Model
{
    protected $table = 'mobi_doc_category';

    protected $fillable = [
        'name', 
        'image', 
        'is_active', 
        'sequence', 
        'sponsor_text',
        'background_image',
        'created_at',
        'updated_at'
    ];
}
