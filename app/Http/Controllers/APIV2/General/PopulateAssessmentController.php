<?php

namespace App\Http\Controllers\APIV2\General;

use App\Http\Controllers\API\ApiController;
use App\Logic\SystemConfig;
use App\Models\LandlordDetail;
use App\Models\MetaValue;
use App\Models\OccupancyDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyInaccessible;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\Swimming;
use App\Models\PropertySanitationType;
use App\Models\District;
use App\Models\PropertyCharacteristicValue;
use App\Models\PropertyWindowType;
use App\Models\UserTitleTypes;

class PopulateAssessmentController extends ApiController
{
    public function populateField()
    {

        $result['property_categories']      = PropertyCategory::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_wall_materials']  = PropertyWallMaterials::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_roofs_materials'] = PropertyRoofsMaterials::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_value_added']     = PropertyValueAdded::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_types']           = PropertyType::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_dimension']       = PropertyDimension::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_use']             = PropertyUse::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_zones']           = PropertyZones::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['swimming_pools']           = Swimming::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['property_sanitation']      = PropertySanitationType::select('id', 'label', 'value')->where('is_active', 1)->get();
        $result['gated_community']          = getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY);
        $result['sigma_pay_url']            = getSystemConfig(SystemConfig::SIGMA_PAY_URL);
        $result['property_inaccessible']    = PropertyInaccessible::select('id as value', 'label')->where('is_active', 1)->get();
        $result['district']                 = District::select('*', 'sq_meter_value as value')->get();
        $result['property_window_types']    = PropertyWindowType::select('id','label','value')->get();
        $result['user_title_types']         = UserTitleTypes::select('id','label')->get();

        $adjustments        = \DB::table('adjustment_values')
            ->leftJoin('adjustments', 'adjustment_values.adjustment_id', '=', 'adjustments.id')
            ->select('adjustments.id','adjustments.name', 'adjustment_values.group_name', 'adjustment_values.percentage')
            ->get();
        $adjustmentValues  = [];  

        for($i=0; $i<count($adjustments); $i++){
            $data = [];
            $data['id'] = $adjustments[$i]->id;
            $data['name'] = $adjustments[$i]->name;
            $data['percentage'] = $adjustments[$i]->percentage;
            $data['group_name'] = $adjustments[$i]->group_name;
            $adjustmentValues[] = $data;
            //$adjustmentValues[$adjustments[$i]->group_name][] = $data;
        }  
        $result['adjustment_values'] = $adjustmentValues;

        $charactRs = PropertyCharacteristicValue::orderBy('group_name')->orderBy('id')->get();

        $characteristicValues = [];
        foreach ($charactRs as $key => $char) {
            $data = [];
            $data['id'] = $char->id;
            $data['name'] = $char->propertyCharacteristic->name;
            $data['good_percentage'] = $char->good;
            $data['average_percentage'] = $char->average;
            $data['bad_percentage'] = $char->bad;
            $data['group_name'] = $char->group_name;
            $characteristicValues[] = $data;           
        }
        $result['characteristic_values'] = $characteristicValues;

        return $this->success([
            'result' => $result,
        ]);
    }

    public function getMeta()
    {
        $result['firstNames'] =  LandlordDetail::select('first_name as label', 'first_name as value')->whereNotNull('first_name')->union(OccupancyDetail::select('tenant_first_name as label', 'tenant_first_name as value')->whereNotNull('tenant_first_name'))->union(MetaValue::select('value as label', 'value')->where('name', 'first_name'))->distinct()->get()->each->setVisible(['label', 'value']);
        $result['lastNames'] = LandlordDetail::select('surname as label', 'surname as value')->whereNotNull('surname')->union(OccupancyDetail::select('surname as label', 'surname as value')->whereNotNull('surname'))->union(MetaValue::select('value as label', 'value')->where('name', 'surname'))->distinct()->get()->each->setVisible(['label', 'value']);;
        $result['streetNames'] = LandlordDetail::select('street_name as label', 'street_name as value')->whereNotNull('street_name')->union(MetaValue::select('value as label', 'value')->whereNotNull('value')->where('name', 'street_name'))->distinct()->get()->each->setVisible(['label', 'value']);

        return $this->success([
            'result' => $result,
        ]);
    }

    
}

