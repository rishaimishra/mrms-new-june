<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question', 'image'];
    const DOCUMENT_DIR  = 'public/question';
    protected $appends = ['full_image'];

    function getFullImageAttribute() {

        if($this->image){
            return  asset('storage/' . $this->image);
        }
        return  '';

    }

    public function options()
    {
        return $this->hasMany(Answer::class);
    }

    public function categories()
    {
        //return $this->belongsTo(BusinessCategory::class);
        return $this->belongsToMany(KnowledgebaseCategory::class,
            'knowledgebase_categories_questions',
            'questions_id',
            'knowledge_cat_id');
    }
}
