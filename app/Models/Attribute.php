<?php

namespace App\Models;

class Attribute extends Model
{
    protected $fillable = [
        'user_id','attribute_id','entity_id','attribute_code','backend_class','backend_type','backend_table','frontend_class','frontend_type','frontend_label','source_class','default_value','is_filterable','is_searchable','is_required','required_validate_class','sequence'
    ];

}
