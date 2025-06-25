<?php

namespace App\Http\Controllers\API\General;

use App\Http\Controllers\API\ApiController;
use App\Models\LandlordDetail;
use App\Models\OccupancyDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;

class PopulateAssessmentController extends ApiController
{
    public function populateField()
    {

        $result['property_categories']      = PropertyCategory::select('id','label','value')->get();
        $result['property_wall_materials']  = PropertyWallMaterials::select('id','label','value')->get();
        $result['property_roofs_materials'] = PropertyRoofsMaterials::select('id','label','value')->get();
        $result['property_value_added']     = PropertyValueAdded::select('id','label','value')->get();
        $result['property_types']           = PropertyType::select('id','label','value')->get();
        $result['property_dimension']       = PropertyDimension::select('id','label','value')->get();
        $result['property_use']             = PropertyUse::select('id','label','value')->get();
        $result['property_zones']           = PropertyZones::select('id','label','value')->get();



        return $this->success([
            'result' => $result,
        ]);
    }
}
