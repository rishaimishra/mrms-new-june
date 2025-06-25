<?php

namespace App\Http\Controllers\Api;

use App\Logic\SystemConfig;
use App\Mail\AutoInterested;
use App\Models\AdminUser;
use App\Models\Auto;
use App\Models\AutoCategory;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutoController extends ApiController
{
    /**
     * Display a listing of the Category.
     * @param null $id
     * @return JsonResponse
     */
    function getAutoCategory($id = null): JsonResponse
    {
        $categories = AutoCategory::active()->where('parent_id', $id)
            ->withCount('children')
            ->orderBy(DB::raw('sequence IS NULL, sequence'), 'asc')
            ->paginate(30);

        $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);

        $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::AUTO_SPONSOR}]);

        if ($id) {
            $autoCategory = AutoCategory::active()->where('id', $id)->first();
            $custom = $autoCategory->sponsor_text ?
                collect(['sponsor' => $autoCategory->sponsor_text]) :
                collect(['sponsor' => $sponsorAll->{SystemConfig::AUTO_SPONSOR}]);
        }

        $data = $custom->merge($categories);

        return $this->genericSuccess($data);
    }

    /**
     * @param $categoryId
     * @param Request $request
     * @return JsonResponse
     */
    function getAutos($categoryId, Request $request)
    {
        $user = $request->user();

        $place = Auto::addSelect(['is_interested' => function ($query) use ($user) {
            $query->selectRaw('COUNT(*)')->from('auto_interested_user')
                ->whereRaw("auto_interested_user.user_id = {$user->id}")
                ->whereColumn('auto_interested_user.auto_id', 'autos.id');
        }])->with('images')
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('auto_category_id', $categoryId);
            })->orderBy(DB::raw('sequence IS NULL, sequence'), 'asc')->paginate();

        return $this->genericSuccess($place);
    }

    public function getAuto($id)
    {
        $auto = Auto::select(['*', 'attr.*'])->findOrFail($id);

        $attributeSet = AttributeSet::find($auto->attribute_set_id);

        $attributeGroups = $attributeSet ? $attributeSet->groups()->whereHas('attributes')->with('attributes')->get() : collect([]);

        return response()->json($auto->toArray() + [
            'attribute_groups' => $attributeGroups->map(function(AttributeGroup $attributeGroup) use ($auto){
                return [
                    'name' => $attributeGroup->name(),
                    'attributes' => $attributeGroup->attributes->map(function(Attribute $attribute) use ($auto){
                        return [
                            'label' => $attribute->frontendLabel(),
                            'code' => Str::camel($attribute->code())
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     *
     */
    function setInterested(Request $request, Auto $auto)
    {
        $request->validate([
            'is_interest' => 'required|boolean',
        ]);

        $user = request()->user();
        if ($request->is_interest) {
            $user->interestedAutos()->attach($auto);

            $admin = AdminUser::where('username', 'admin')->first();

            Mail::to($admin)->send(new AutoInterested($auto, $user));
        } else {
            $user->interestedAutos()->detach($auto);
        }

        return $this->success("success");
    }
}
