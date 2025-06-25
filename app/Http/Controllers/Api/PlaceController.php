<?php


namespace App\Http\Controllers\Api;

use App\Logic\SystemConfig;
use App\Models\Place;
use App\Models\PlaceCategory;

class PlaceController extends ApiController
{
    /**
     * Display a listing of the Category.
     *
     */
    function getPlaceCategory($id = null)
    {

        $categories = PlaceCategory::active()->where('parent_id', $id)->withCount('children')->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);

        $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
        $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::PLACE_SPONSOR}]);
        if ($id) {
            $placeCategory = PlaceCategory::active()->where('id', $id)->first();
            $custom = $placeCategory->sponsor_text ?
                collect(['sponsor' => $placeCategory->sponsor_text]) :
                collect(['sponsor' => $sponsorAll->{SystemConfig::PLACE_SPONSOR}]);
        }
        $data = $custom->merge($categories);
        return $this->genericSuccess($data);

    }

    /**
     *
     */
    function getPlace($catid)
    {

        $place = Place::with('images')->whereHas('categories', function ($q) use ($catid) {
            $q->where('place_category_id', '=', $catid);

        })->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);

        return $this->genericSuccess($place);

    }
}