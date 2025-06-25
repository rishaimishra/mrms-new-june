<?php

namespace App\Http\Controllers\API\General;

use App\Http\Controllers\API\ApiController;
use App\Models\PropertyDimension;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyZones;
use App\Types\ApiStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PropertyCategory;
use App\Models\PropertyWallMaterials;

class CalculatePropertyRateController extends ApiController
{
    public function Calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_categories' => 'required',
            'wall_material' => 'required',
            'roof_material' => 'required',
            'value_added' => 'required|array',
            'property_type' => 'required|array|max:2',
            'property_dimension' => 'required',
            'property_use' => 'required',
            'zones' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $property_category = PropertyCategory::select('value')->find($request->property_categories);
        $wall_material = PropertyWallMaterials::select('value')->find($request->wall_material);
        $roof_material = PropertyRoofsMaterials::select('value')->find($request->roof_material);
        $value_added_val = PropertyValueAdded::whereIn('id', $request->value_added)->sum('value');
        $property_type_val = PropertyType::whereIn('id', $request->property_type)->sum('value');
        $property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        $property_use = PropertyUse::select('value')->find($request->property_use);
        $zones = PropertyZones::select('value')->find($request->zones);

        $property_category = PropertyCategories::select('value')->find($request->property_categories);
        $wall_material = PropertyWallMaterials::select('value')->find($request->wall_material);
        $roof_material = PropertyRoofsMaterials::select('value')->find($request->roof_material);
        $value_added_val = PropertyValueAdded::whereIn('id', $request->value_added)->sum('value');
        $property_type_val = PropertyTypes::whereIn('id', $request->property_type)->sum('value');
        $property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        $property_use = PropertyUse::select('value')->find($request->property_use);
        $zones = PropertyZones::select('value')->find($request->zones);

        $step1 = $wall_material['value'] + $roof_material['value'] + $value_added_val;
        $step2 = $property_type_val;
        $step3 = $property_dimension['value'];
        $step4 = $property_use['value'];
        $step5 = $zones['value'];
        $step6 = $property_category['value'];

        $result['rateWithoutGST'] = round((($step1 * $step2 * $step3 * $step4) / $step5) / $step6, 4);
        $result['GST'] = round($result['rateWithoutGST'] * (15 / 100), 4);
        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);

        return $this->success([
            'result' => $result,
        ]);
    }
}
