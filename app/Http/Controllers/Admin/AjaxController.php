<?php

namespace App\Http\Controllers\Admin;

use App\Logic\SystemConfig;
use App\Models\AdjustmentValue;
use App\Models\BoundaryDelimitation;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWindowType;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\Property;
use App\Models\District;
use App\Models\Swimming;
use App\Models\PropertySanitationType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AjaxController extends Controller
{
    public function getWardOptions(Request $request)
    {
        if ($request->type == 'constituency') {
            $constituency = BoundaryDelimitation::where('ward', $request->ward)->pluck('constituency', 'constituency');

            return response()->json($constituency);
        }

        if ($request->type == 'section') {
            $section = BoundaryDelimitation::where('ward', $request->ward)->pluck('section', 'section');

            return response()->json($section);
        }

        if ($request->type == 'chiefdom') {
            $chiefdom = BoundaryDelimitation::where('ward', $request->ward)->pluck('chiefdom', 'chiefdom');

            return response()->json($chiefdom);
        }

        if ($request->type == 'district') {
            $district = BoundaryDelimitation::where('ward', $request->ward)->pluck('district', 'district');

            return response()->json($district);
        }

        if ($request->type == 'province') {
            $province = BoundaryDelimitation::where('ward', $request->ward)->pluck('province', 'province');

            return response()->json($province);
        }
    }

    public function calculateRate(Request $request)
    {

        $property_category = 0;
        $wall_material = 0;
        $roof_material = 0;
        $value_added_val = 0;
        $property_type_val = 0;
        $property_dimension = 0;
        $property_use = 0;
        $zones = 0;
        $no_of_shops = $request->total_shops ? $request->total_shops : 0;
        $no_of_mast =  $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];

        //$request->property_value_added = explode(',',$request->property_value_added);
        //$request->property_types = explode(',',$request->property_types);
        foreach ($valueAdded as $value) {
            if (is_array($request->property_value_added) and count($request->property_value_added) > 0) {
                if (in_array($value, $request->property_value_added)) {
                    $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                    $shopValue = $value == 9 ? $amount->value : 0;
                    $mastValue = $value == 8 ? $amount->value : 0;
                }
            }
        }
        if (is_array($request->property_value_added) and count($request->property_value_added) > 0) {
            $valueAdded = array_diff($request->property_value_added, $valueAdded);
        }



        if (isset($request->property_categories) and $request->property_categories != null)
            $property_categories = PropertyCategory::whereIn('id', $request->property_categories)->get();

        if (isset($request->property_wall_materials) and $request->property_wall_materials != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->property_wall_materials);

        if (isset($request->roofs_materials) and $request->roofs_materials != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->roofs_materials);

        if (is_array($request->property_value_added) and count($request->property_value_added) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->property_types) and count($request->property_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->property_types)->sum('value');

        if (isset($request->property_dimension) and $request->property_dimension != null)
            $property_dimension = PropertyDimension::select('value')->find($request->property_dimension);

        if (isset($request->property_use) and $request->property_use != null)
            $property_use = PropertyUse::select('value')->find($request->property_use);

        if (isset($request->zone) and $request->zone != null)
            $zones = PropertyZones::select('value')->find($request->zone);

        /*number of Shop available*/

        if ($shopValue > 0)
            $value_added_val = $value_added_val + ($shopValue * $no_of_shops);

        /*number of mast available*/
        if ($mastValue > 0)
            $value_added_val = $value_added_val + ($mastValue * $no_of_mast);


        $step1 = $wall_material['value'] + $roof_material['value'] + $value_added_val;
        $step2 = $property_type_val;
        $step3 = $property_dimension['value'];
        $step4 = $property_use['value'];
        $step5 = $zones['value'];
        $step6 = 0;
        $swimming_pool =  optional(Swimming::find($request->swimming_pool))->value;

        $gated_community = $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 1;

        if (count($property_categories) && $property_categories->count()) {
            $step6 = 1;

            foreach ($property_categories as $prop_category) {
                $step6 *= $prop_category->value;
            }
        }

        $result['rateWithoutGST'] = @(((($step1 * $step2 * $step3 * $step4) * $gated_community) + ($swimming_pool ? $swimming_pool : 0)) / ($step6 > 0 ? $step6 : 1));

        $result['GST'] = $result['rateWithoutGST'] * .15;

        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);

        $result['formatWithoutGST'] = number_format($result['rateWithoutGST'], 0, '', ',');

        $result['formatGST'] = number_format($result['GST'], 0, '', ',');

        $result['formatWithGST'] = number_format($result['rateWithGST'], 0, '', ',');


        return response()->json($result);
    }

    public function calculateNewRate(Request $request)
    {
	        $property_category = 0;
        $rate_square_meter = 2750.00;
        $wall_material = 0;
        $roof_material = 0;
        $value_added_val = 0;
        $property_type_val = 0;
        $property_dimension = 0;
        $property_window_type = 0;
        $property_use = 0;
        $zones = 0;
        $no_of_shops = $request->total_shops ? $request->total_shops : 0;
        $no_of_mast =  $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];
        $property_council_adjustment = 0;

        //$request->property_value_added = explode(',',$request->property_value_added);
        //$request->property_types = explode(',',$request->property_types);
        foreach ($valueAdded as $value) {
            if (in_array($value, $request->property_value_added)) {
                $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                $shopValue = $value == 9 ? $amount->value : 0;
                $mastValue = $value == 8 ? $amount->value : 0;
            }
        }
        $valueAdded = array_diff($request->property_value_added, $valueAdded);

        if (isset($request->property_window_type) and $request->property_window_type != null)
            $property_window_type_value = PropertyWindowType::select('value')->find($request->property_window_type);

        if (isset($request->property_categories) and $request->property_categories != null)
            $property_categories = PropertyCategory::whereIn('id', $request->property_categories)->get();

        if (isset($request->property_wall_materials) and $request->property_wall_materials != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->property_wall_materials);

        if (isset($request->roofs_materials) and $request->roofs_materials != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->roofs_materials);

        if (is_array($request->property_value_added) and count($request->property_value_added) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->property_types) and count($request->property_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->property_types)->sum('value');

        if (is_array($request->property_council_adjustments) and count($request->property_council_adjustments) > 0)
            $property_council_adjustment = AdjustmentValue::where('group_name','=',$request->property_council_group_name)->whereIn('adjustment_id', $request->property_council_adjustments)->sum('percentage');

        if (isset($request->property_length) and $request->property_length != null and (isset($request->property_breadth) and $request->property_breadth != null)) {

            $property = Property::find($request->property_id);

            if ($property->district) {
                $district = District::where('name', $property->district)->first();
                if ($district->sq_meter_value) {
                    //$rate_square_meter = $district->sq_meter_value;
                }
            }

            $property_dimension = ($request->property_length * $request->property_breadth);
            //$property_dimension = $request->property_dimension * getSystemConfig(SystemConfig::CURRENT_RATE);
            //$property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        }


        if (isset($request->property_use) and $request->property_use != null)
            $property_use = PropertyUse::select('value')->find($request->property_use);

        if (isset($request->zone) and $request->zone != null)
            $zones = PropertyZones::select('value')->find($request->zone);

        
        $property_sanitation_value = 1;
        if(isset($request->property_sanitation))
            $property_sanitation_value = PropertySanitationType::select('value')->find($request->property_sanitation);

        /*number of Shop available*/

        if ($shopValue > 0)
            $value_added_val = $value_added_val + ($shopValue * $no_of_shops);

        /*number of mast available*/
        if ($mastValue > 0)
            $value_added_val = $value_added_val + ($mastValue * $no_of_mast);

        
        $swimming_pool =  optional(Swimming::find($request->swimming_pool))->value;

        $totalAddition = $wall_material['value'] + $roof_material['value'] + $value_added_val + ($swimming_pool ? $swimming_pool : 0);
        $propertyUse = $property_use['value'];
        $zone = $zones['value'];
        $propertyType = $property_type_val;
        $propertyDimesion = $property_dimension;


        $step6 = 1;
        

        $gated_community = $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 1;

        if (count($property_categories) && $property_categories->count()) {
            $step6 = 1;

            foreach ($property_categories as $prop_category) {
                $step6 *= $prop_category->value;
            }
        }
        
        $result['rateWithoutGST'] = (($propertyDimesion*$rate_square_meter)+$totalAddition) * $gated_community * $propertyUse * $step6 *$zone * $propertyType;
        
        $wallMaterialPercentage =  0;
        $roofMaterialPercentage = 0;
        $valueAddedPercentage =  0;
        $windowTypePercentage = 0;
        $totalPercentage = array_sum([$wallMaterialPercentage, $roofMaterialPercentage, $valueAddedPercentage, $windowTypePercentage]);

        $result['GST'] = $result['rateWithoutGST'] * .15;

        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);

        $result['formatWithoutGST'] = number_format($result['rateWithoutGST']/1000, 0, '', ',');

        $result['formatGST'] = number_format($result['GST'], 0, '', ',');

        $result['formatWithGST'] = number_format($result['rateWithGST'], 0, '', ',');

        $result['rateWithoutGST'] = $result['rateWithoutGST']/1000 * ((100-$property_council_adjustment)/100);


        return response()->json($result);
    }
}
