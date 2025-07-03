<?php


namespace App\Http\Controllers\Api;


use App\Logic\SystemConfig;
use App\Mail\RealEstateInterested;
use App\Models\AdminUser;
use App\Models\RealEstate;
use App\Models\RealEstateCategory;
use App\Models\AdDetail;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RealEstateController extends ApiController
{
    /**
     * Display a listing of the Category.
     *
     */
    function getAutoCategory($id = null)
    {

        $categories = RealEstateCategory::active()->where('parent_id', $id)->withCount('children')->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);

        $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
        $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::REAL_STATE_SPONSOR}]);
        if ($id) {
            $realEstateCategory = RealEstateCategory::active()->where('id', $id)->first();
            $custom = $realEstateCategory->sponsor_text ?
                collect(['sponsor' => $realEstateCategory->sponsor_text]) :
                collect(['sponsor' => $sponsorAll->{SystemConfig::REAL_STATE_SPONSOR}]);
        }
        $data = $custom->merge($categories);
        $adDetail = AdDetail::where('ad_category', 'Shop')
        ->where('ad_type', 1)
        ->where('ad_content_type', 'Image')
        ->first();

    // Check if ad exists and append it to the data
    if ($adDetail) {
        $img = url('storage/' . $adDetail->ad_image);
        $adData = [
            "id" => $adDetail->id,
            "name" => $adDetail->ad_name,
            "fullImage" => $img,
            "ad_link" => $adDetail->ad_link,
            "status" => $adDetail->status,
            "ad_type" => $adDetail->ad_type,
            "ad_category" => $adDetail->ad_category,
            "ad_content_type" => $adDetail->ad_content_type,
            "ad_video" => $adDetail->ad_video,
            "sequence" => $adDetail->sequence,
            "ad_description" => $adDetail->ad_description,
            "adImageWidth" => 375,
            "adImageHeight" => 160,
        ];

        // Convert pagination data to an array and append the ad data
        $categoriesArray = $categories->toArray();
        $categoriesArray['data'][] = $adData;

        // Replace the merged data
        $data = $custom->merge($categoriesArray);
    }
        return $this->genericSuccess($data);
    }


    /**
     *
     */
    function getProperties($categoryId, Request $request)
    {
        $user = $request->user();

        $place = RealEstate::addSelect(['is_interested' => function ($query) use ($user) {

            $query->selectRaw('COUNT(*)')->from('real_estate_interested_user')
                ->whereColumn('real_estate_interested_user.real_estate_id', 'real_estates.id')
                ->whereRaw("real_estate_interested_user.user_id = {$user->id}");
        }])->with('images')->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('real_category_id', '=', $categoryId);
        })->orderBy(DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);

        return $this->genericSuccess($place);
    }

    public function getProperty($id)
    {
        $auto = RealEstate::select(['*', 'attr.*'])->findOrFail($id);

        $attributeSet = AttributeSet::find($auto->attribute_set_id);

        $attributeGroups = $attributeSet ? $attributeSet->groups()->whereHas('attributes')->with('attributes')->get() : collect([]);

        return response()->json($auto->toArray() + [
                'attribute_groups' => $attributeGroups->map(function (AttributeGroup $attributeGroup) use ($auto) {
                    return [
                        'name' => $attributeGroup->name(),
                        'attributes' => $attributeGroup->attributes->map(function (Attribute $attribute) use ($auto) {
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
    function setInterested(Request $request, RealEstate $realEstate)
    {

        $request->validate([
            'is_interest' => 'required|boolean',
        ]);

        $user = request()->user();
        if ($request->is_interest) {
            $user->interestedRealEstate()->attach($realEstate);

            $admin = AdminUser::where('username', 'admin')->first();

            Mail::to($admin)->send(new RealEstateInterested($realEstate, $user));
        } else {
            $user->interestedRealEstate()->detach($realEstate);
        }
        return $this->success("success");
    }
}
