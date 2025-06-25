<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['option_value', 'option_image', 'is_answer'];
    protected $appends = ['full_image'];

    function getFullImageAttribute()
    {

        if ($this->option_image) {
            return asset('storage/' . $this->option_image);
        }
        return '';

    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
