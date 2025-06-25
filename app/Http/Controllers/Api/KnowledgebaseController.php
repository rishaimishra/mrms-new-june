<?php


namespace App\Http\Controllers\Api;


use App\Logic\SystemConfig;
use App\Models\KnowledgebaseCategory;
use App\Models\Question;

class KnowledgebaseController extends ApiController
{
    /**
     * Display a listing of the Category.
     *
     */
    function getKnowledgebaseCategory($id = null)
    {

        $categories = KnowledgebaseCategory::active()->where('parent_id', $id)->withCount('children')->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);

        $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
        $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::QUIZ_SPONSOR}]);
        if ($id) {
            $knowledgebaseCategory = KnowledgebaseCategory::active()->where('id', $id)->first();
            $custom = $knowledgebaseCategory->sponsor_text ?
                collect(['sponsor' => $knowledgebaseCategory->sponsor_text]) :
                collect(['sponsor' => $sponsorAll->{SystemConfig::QUIZ_SPONSOR}]);
        }

        $data = $custom->merge($categories);
        return $this->genericSuccess($data);
    }

    /**
     *
     */
    function getQuestion($catid)
    {

        $place = Question::with('options')->withCount('options')->having('options_count', '>', 0)->whereHas('categories', function ($q) use ($catid) {
            $q->where('knowledge_cat_id', '=', $catid);
        })->inRandomOrder()->limit(30)->get(); //paginate();

        return $this->genericSuccess($place);
    }

}
