<?php

namespace App\Http\Controllers\API\General;

use App\Http\Controllers\API\ApiController;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Types\ApiStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Property;
use Illuminate\Validation\Rule;

class PropertyController extends ApiController
{
    //

    public function save(Request $request)
    {

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'is_completed' => 'required|boolean',
            'property_street_number' => 'string|required_if:is_completed,1',
            'property_street_name' => 'string|max:255|nullable|required_if:is_completed,1',
            'property_ward' => 'integer|required_if:is_completed,1',
            'property_constituency' => 'integer|required_if:is_completed,1',
            'property_section' => 'string|max:255|required_if:is_completed,1',
            'property_chiefdom' => 'string|max:255|required_if:is_completed,1',
            'property_district' => 'string|max:255|required_if:is_completed,1',
            'property_province' => 'string|max:255|required_if:is_completed,1',
            'property_postcode' => 'string|max:255|required_if:is_completed,1',
            'landlord_first_name' => 'string|max:255|required_if:is_completed,1',
            'landlord_middle_name' => 'string|max:255',
            'landlord_surname' => 'string|max:255|required_if:is_completed,1',
            'landlord_sex' => 'string|max:255|required_if:is_completed,1',
            'landlord_street_number' => 'string',
            'landlord_street_name' => 'string|max:255',
            'landlord_ward' => 'integer|required_if:is_completed,1',
            'landlord_constituency' => 'integer|required_if:is_completed,1',
            'landlord_section' => 'string|max:255|required_if:is_completed,1',
            'landlord_chiefdom' => 'string|max:255|required_if:is_completed,1',
            'landlord_district' => 'string|max:255|required_if:is_completed,1',
            'landlord_province' => 'string|max:255|required_if:is_completed,1',
            'landlord_postcode' => 'string|max:255|required_if:is_completed,1',
            'landlord_mobile_1' => 'required_if:is_completed,1|string|max:15',
            'landlord_mobile_2' => 'nullable|string|max:15',
            'occupancy_type'=>'nullable|required_if:is_completed,1|array',
            'occupancy_type.*'=>'nullable|required_if:is_completed,1|in:Owned Tenancy,Rented House,Unoccupied House',
            'occupancy_tenant_first_name' => 'string|max:255|required_if:is_completed,1',
            'occupancy_middle_name' => 'string|max:255',
            'occupancy_surname' => 'string|required_if:is_completed,1',
            'occupancy_mobile_1' => 'required_if:is_completed,1|string|max:15',
            'occupancy_mobile_2' => 'nullable|string|max:15',
            'assessment_categories_id' => 'nullable|required_if:is_completed,1|array',
            'assessment_categories_id.*' => 'nullable|required_if:is_completed,1|exists:property_categories,id',
            'assessment_types' => 'array|max:2|required_if:is_completed,1',
            'assessment_types.*' => 'exists:property_types,id|required_if:is_completed,1',
            'assessment_wall_materials_id' => 'string|max:255|required_if:is_completed,1',
            'assessment_roofs_materials_id' => 'string|max:255|required_if:is_completed,1',
            'assessment_dimension_id' => 'string|max:255|required_if:is_completed,1',
            'assessment_value_added_id' => 'array|required_if:is_completed,1',
            'assessment_value_added_id.*' => 'exists:property_value_added,id|required_if:is_completed,1',
            'assessment_images' => 'required_if:is_completed,1|array|max:2',
            'assessment_images.*' => 'image|mimes:jpeg,png,jpg',
            'assessment_use_id' => 'string|max:255|required_if:is_completed,1',
            'assessment_zone_id' => 'string|max:255|required_if:is_completed,1',
            'registry_meter_number' => 'required_if:is_completed,1|string|max:255',
            'registry_meter_image' => 'required_if:is_completed,1|image|mimes:jpeg,png,jpg',
            'registry_point1' => 'string|max:255|required_if:is_completed,1',
            'registry_point2' => 'string|max:255|required_if:is_completed,1',
            'registry_point3' => 'required_if:is_completed,1|string|max:255',
            'registry_point4' => 'string|max:255',
            'registry_point5' => 'string|max:255',
            'registry_point6' => 'string|max:255',
            'registry_point7' => 'string|max:255',
            'registry_point8' => 'string|max:255',
            'registry_digital_address' => [
                'required_if:is_completed,1',
                'string',
                'max:255',
                Rule::unique('property_geo_registry', 'digital_address')
                    ->ignore($request->input('property_id'), 'property_id')
            ],
            'dor_lat_long' => 'nullable|required_if:is_completed,1|max:190'
        ]);

//
        //'registry_meter_images' => 'required|string|max:255',
        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $rate = $this->calculateRate($request);


        if (isset($request->property_id) and $request->property_id != '') {
            //$getproperty = Property::with( 'geoRegistry:id,property_id')->findOrFail($request->property_id);
            //dd($request);



            $property = Property::find($request->property_id);
            $property->user_id = $user->id;
            $property->street_number = $request->input('property_street_number');
            $property->street_name = $request->property_street_name;
            $property->ward = $request->property_ward;
            $property->constituency = $request->property_constituency;
            $property->section = $request->property_section;
            $property->chiefdom = $request->property_chiefdom;
            $property->district = $request->property_district;
            $property->province = $request->property_province;
            $property->postcode = $request->property_postcode;
            $property->is_completed = $request->is_completed;

            $property->save();

            $property->landlord()->where('property_id', $request->property_id)->update([
                'first_name' => $request->landlord_first_name,
                'middle_name' => $request->landlord_middle_name,
                'surname' => $request->landlord_surname,
                'sex' => $request->landlord_sex,
                'street_number' => $request->landlord_street_number,
                'street_name' => $request->landlord_street_name,
                'ward' => $request->landlord_ward,
                'constituency' => $request->landlord_constituency,
                'section' => $request->landlord_section,
                'chiefdom' => $request->landlord_chiefdom,
                'district' => $request->landlord_district,
                'province' => $request->landlord_province,
                'postcode' => $request->landlord_postcode,
                'mobile_1' => $request->landlord_mobile_1,
                'mobile_2' => $request->landlord_mobile_2
            ]);

            $property->occupancy()->where('property_id', $request->property_id)->update([
                'tenant_first_name' => $request->occupancy_tenant_first_name,
                'middle_name' => $request->occupancy_middle_name,
                'surname' => $request->occupancy_surname,
                'mobile_1' => $request->occupancy_mobile_1,
                'mobile_2' => $request->occupancy_mobile_2
            ]);

            if (count(array_filter($request->occupancy_type))) {
                foreach (array_filter($request->occupancy_type) as $types) {
                    $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
                }
                $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
            }

            $property->assessment()->where('property_id', $request->property_id)->update([
                'property_wall_materials' => $request->assessment_wall_materials_id,
                'roofs_materials' => $request->assessment_roofs_materials_id,
                'property_dimension' => $request->assessment_dimension_id,
                'property_rate_without_gst' => $rate['rateWithoutGST'],
                'property_gst' => $rate['GST'],
                'property_rate_with_gst' => $rate['rateWithGST'],
                'property_use' => $request->assessment_use_id,
                'zone' => $request->assessment_zone_id
            ]);

            $property->images()->where('property_id', $request->property_id)->delete();


            if (count($request->assessment_images) and is_array($request->assessment_images)) {
                foreach (array_filter($request->assessment_images) as $image) {
                    $image = $image->store(Property::ASSESSMENT_IMAGE);

                    $property->images()->create(['image' => $image, 'type' => 'assessment']);
                }
            }


            $property->types()->sync($request->assessment_types);
            $property->valueAdded()->sync($request->assessment_value_added_id);

            $geoDate=[
                'meter_number' => $request->registry_meter_number,
                'point1' => $request->registry_point1,
                'point2' => $request->registry_point2,
                'point3' => $request->registry_point3,
                'point4' => $request->registry_point4,
                'point5' => $request->registry_point5,
                'point6' => $request->registry_point6,
                'point7' => $request->registry_point7,
                'point8' => $request->registry_point8,
                'digital_address' => $request->registry_digital_address,
                'dor_lat_long' => $request->dor_lat_long
            ];


            if ($request->file('registry_meter_image')) {
                $geoDate['meter_images'] = $request->registry_meter_image->store(Property::METER_IMAGE);
            }

            $property->geoRegistry()->where('property_id', $request->property_id)->update($geoDate);


        } else {


            $registeryImageName = null;
            if ($request->hasFile('registry_meter_image')) {
                $registeryImageName = $request->registry_meter_image->store(Property::METER_IMAGE);
            }

            $property = new Property();
            $property->user_id = $user->id;
            $property->street_number = $request->input('property_street_number');
            $property->street_name = $request->property_street_name;
            $property->ward = $request->property_ward;
            $property->constituency = $request->property_constituency;
            $property->section = $request->property_section;
            $property->chiefdom = $request->property_chiefdom;
            $property->district = $request->property_district;
            $property->province = $request->property_province;
            $property->postcode = $request->property_postcode;
            $property->is_completed = $request->input('is_completed', false);

            $property->save();

            $property->landlord()->create([
                'first_name' => $request->landlord_first_name,
                'middle_name' => $request->landlord_middle_name,
                'surname' => $request->landlord_surname,
                'sex' => $request->landlord_sex,
                'street_number' => $request->landlord_street_number,
                'street_name' => $request->landlord_street_name,
                'ward' => $request->landlord_ward,
                'constituency' => $request->landlord_constituency,
                'section' => $request->landlord_section,
                'chiefdom' => $request->landlord_chiefdom,
                'district' => $request->landlord_district,
                'province' => $request->landlord_province,
                'postcode' => $request->landlord_postcode,
                'mobile_1' => $request->landlord_mobile_1,
                'mobile_2' => $request->landlord_mobile_2
            ]);

            $property->occupancy()->create([
                'tenant_first_name' => $request->occupancy_tenant_first_name,
                'middle_name' => $request->occupancy_middle_name,
                'surname' => $request->occupancy_surname,
                'mobile_1' => $request->occupancy_mobile_1,
                'mobile_2' => $request->occupancy_mobile_2
            ]);


            if (count(array_filter($request->occupancy_type))) {
                foreach (array_filter($request->occupancy_type) as $types) {
                    $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
                }
                $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
            }

            $property->assessment()->create([
                'property_wall_materials' => $request->assessment_wall_materials_id,
                'roofs_materials' => $request->assessment_roofs_materials_id,
                'property_dimension' => $request->assessment_dimension_id,
                'property_rate_without_gst' => $rate['rateWithoutGST'],
                'property_gst' => $rate['GST'],
                'property_rate_with_gst' => $rate['rateWithGST'],
                'property_use' => $request->assessment_use_id,
                'zone' => $request->assessment_zone_id
            ]);

            $property->categories()->sync($request->assessment_categories_id);

            if ($request->assessment_images && count($request->assessment_images) and is_array($request->assessment_images)) {
                foreach (array_filter($request->assessment_images) as $image) {
                    $image = $image->store(Property::ASSESSMENT_IMAGE);

                    $property->images()->create(['image' => $image, 'type' => 'assessment']);
                }
            }


            $property->types()->attach($request->assessment_types);
            $property->valueAdded()->attach($request->assessment_value_added_id);

            $property->geoRegistry()->create(
                [
                    'meter_number' => $request->registry_meter_number,
                    'meter_images' => $registeryImageName,
                    'point1' => $request->registry_point1,
                    'point2' => $request->registry_point2,
                    'point3' => $request->registry_point3,
                    'point4' => $request->registry_point4,
                    'point5' => $request->registry_point5,
                    'point6' => $request->registry_point6,
                    'point7' => $request->registry_point7,
                    'point8' => $request->registry_point8,
                    'digital_address' => $request->registry_digital_address,
                    'dor_lat_long' => $request->dor_lat_long
                ]
            );
        }


        return $this->success([
            'property_id' => $property->id,
            'sink' => 1,
            'is_completed' => $property->is_completed
        ]);
    }

    public function getIncompleteProperty()
    {
        $user = Auth::user();
        $property = Property::with('images', 'occupancy', 'assessment', 'geoRegistry', 'payments', 'landlord', 'types:id,label,value', 'valueAdded:id,label,value')->where([['is_completed', '=', 0], ['user_id', '=', $user->id]])->get();

        return $this->success([
            'property' => $property,
        ]);
    }

    public function calculateRate($request)
    {
        $wall_material=0;
        $roof_material=0;
        $value_added_val=0;
        $property_type_val=0;
        $property_dimension=0;
        $property_use=0;
        $zones=0;
        $property_categories = [];

        if(isset($request->assessment_categories_id) and $request->assessment_categories_id!=null)
        $property_categories = PropertyCategory::whereIn('id', $request->assessment_categories_id)->get();

        if(isset($request->assessment_wall_materials_id) and $request->assessment_wall_materials_id!=null)
        $wall_material = PropertyWallMaterials::select('value')->find($request->assessment_wall_materials_id);

        if(isset($request->assessment_roofs_materials_id) and $request->assessment_roofs_materials_id!=null)
        $roof_material = PropertyRoofsMaterials::select('value')->find($request->assessment_roofs_materials_id);

        if(is_array($request->assessment_value_added_id) and count($request->assessment_value_added_id)>0)
        $value_added_val = PropertyValueAdded::whereIn('id',$request->assessment_value_added_id)->sum('value');

        if(is_array($request->assessment_types) and count($request->assessment_types)>0)
        $property_type_val = PropertyType::whereIn('id',$request->assessment_types)->sum('value');

        if(isset($request->assessment_dimension_id) and $request->assessment_dimension_id!=null)
        $property_dimension =PropertyDimension::select('value')->find($request->assessment_dimension_id);

        if(isset($request->assessment_use_id) and $request->assessment_use_id!=null)
        $property_use = PropertyUse::select('value')->find($request->assessment_use_id);

        if(isset($request->assessment_zone_id) and $request->assessment_zone_id!=null)
        $zones = PropertyZones::select('value')->find($request->assessment_zone_id);

        $step1 =  $wall_material['value']+$roof_material['value']+$value_added_val;
        $step2 =  $property_type_val;
        $step3 =  $property_dimension['value'];
        $step4 =  $property_use['value'];
        $step5 =  $zones['value'];
        $step6 = 0;

        if($property_categories->count())
        {
            $step6 = 1;

            foreach ($property_categories as $prop_category)
            {
                $step6 *= $prop_category->value;
            }
        }


        $result['rateWithoutGST'] = @round((($step1*$step2*$step3*$step4)/$step5)/$step6,4);


        $result['GST'] =            round($result['rateWithoutGST']*.15,4);


        $result['rateWithGST'] =    round($result['rateWithoutGST']+$result['GST'],4);

        return $result;


    }

}
