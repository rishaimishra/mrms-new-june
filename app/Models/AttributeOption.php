<?php

namespace App\Models;

class AttributeOption extends \Eav\AttributeOption
{
    protected $fillable = [
        'attribute_id', 'label', 'value', 'is_featured'
    ];

    protected $casts = [
        'is_featured' => 'boolean'
    ];
}
