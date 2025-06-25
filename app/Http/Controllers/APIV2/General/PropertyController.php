<?php

namespace App\Http\Controllers\APIV2\General;

use Folklore\Image\Facades\Image;
use App\Http\Controllers\API\ApiController;
use App\Logic\SystemConfig;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\RegistryMeter;
use App\Models\Swimming;
use App\Models\PropertyWindowType;
use App\Models\District;
use App\Models\BoundaryDelimitation;
use App\Models\User;
use App\Models\InaccessibleProperty;
use App\Models\PropertyInaccessible;
use App\Notifications\DraftDeliveredSMSNotification;
use App\Notifications\PaymentSMSNotification;
use App\Models\UserTitleTypes;
use App\Types\ApiStatusCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\AdjustmentValue;
use App\Models\Adjustment;
use App\Models\MillRate;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Twilio;
use Config;
use Twilio\Rest\Client;
use App\Models\CounsilAdjustmentGroupA;
use App\Models\PropertyToCounsilGroupA;
use App\Models\PropertyToPropertyType;
use App\BusinessLicense;
use App\BusinessReg;
use App\Models\BusinessRegistrationModel;
use App\Models\BusinessLicenseModel;
use App\BusinessType;
use App\BusinessLicenseCategory;


class PropertyController extends ApiController
{
	private $propertyId;

    public function BusinessListById($id){
        $leagues = BusinessReg::where('business_registration.id', $id)
    ->join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
    ->get();

    return $leagues;

    }

    public function updateBusiness(Request $request, $id)
    {
        $business = [];
        // dd($request);
        // $business = BusinessReg::find($id);
        // $business['nameOfOrganization'] = $request->nameOfOrganization;
        // $business['dateOfEstablishment'] = $request->dateofEstablishment;
        // $business['organizationMainContactAddress'] = $request->organContactAddress;
        // $business['organizationSubContactAddress'] = $request->organSubContactAddress;
        // $business['contactNumber'] = $request->contactNumber;
        // $business['IsTheOrganization'] = $request->checkedValue;
        // $business['NameOfHeadOrganization'] = $request->nameOfheadOrganization;
        // $business['addressHeadOrg'] = $request->LeadinPioneerAddress;
        // $business['NameOfOtherContactPerson'] = $request->nameOfOtherContPerson;
        // $business['addressOtherContact'] = $request->otherContactPersonAddress;
        // $business['membership_total'] = $request->membershipTotal;
        // $business['membership_male'] = $request->MembershipMale;
        // $business['membership_female'] = $request->MemberShipFemale;
        // $business['main_aim_of_organization'] = $request->mainAim;
        // $business['objectOfOrganization'] = $request->ObjectOfOrganization;
        // $business['mainProjectACtivities'] = $request->mainProjectActivities;
        // $business['categoryOfTargetBEnefeciary'] = $request->categoryOfTarget;
        // $business['communityProjectEstablishment'] = $request->communityProjectEstablishmentOrganization;
        // $business['LocationHasYourOrganOperating'] = $request->OrganizationOperatingDistrict;
        // $business['OrganizationIntendOperating'] = $request->OrganizationIntendDistrict;
        // $business['sourceOfFunding'] = $request->SourceOfFunding;
        // $business['AddresOfOrganizationChairman'] = $request->nameAddressOrganizationChairman;
        // if ($request->hasFile('signature')) {
        //     $signature = $request->file('signature');
        //     $extension = $signature->getClientOriginalExtension();
        //     $filename = time().rand(000,9999) . '.' . $extension;
        //     $path = "business_images";
        //     $signature->move(public_path($path),$filename);
        //     $business['Signature'] = $filename;
        // }
        // if ($request->hasFile('pic')) {
        //     $pic = $request->file('pic');
        //     $extension = $pic->getClientOriginalExtension();
        //     $filename = time().rand(000,9999) . '.' . $extension;
        //     $path = "business_images";
        //     $pic->move(public_path($path),$filename);
        //     $business['pic'] = $filename;
        // }
        // BusinessReg::where('id',$id)->update($business);


        $BusinessLicense = [];


        $BusinessLicense['BusinessName'] = $request->BusinessName;
        $BusinessLicense['BusinessRegId'] = $id;
        $BusinessLicense['BusinessAccro'] = $request->BusinessAccro;
        $BusinessLicense['NameBusinesOwner'] = $request->NameBusinesOwner;
        $BusinessLicense['phoneOne'] = $request->phoneOne;
        $BusinessLicense['phoneTwo'] = $request->phoneTwo;
        $BusinessLicense['emailIfAny'] = $request->emailIfAny;
        $BusinessLicense['ownership'] = $request->ownership;
        $BusinessLicense['BusinessHousing'] = $request->BusinessHousing;
        $BusinessLicense['house'] = $request->house;
        $BusinessLicense['Street'] = $request->Street;
        $BusinessLicense['Section'] = $request->Section;
        $BusinessLicense['Zone'] = $request->Zone;
        $BusinessLicense['BusinessType'] = $request->BusinessType;
        $BusinessLicense['BusinessClass'] = $request->BusinessClass;
        $BusinessLicense['BusinessSize'] = $request->BusinessSize;
        $BusinessLicense['BusinessLocation'] = $request->BusinessLocation;
        $BusinessLicense['BusinessLicenseCategory'] = $request->BusinessLicenseCategory;
        $BusinessLicense['LicenseFee'] = $request->LicenseFee;
        $BusinessLicense['BusinessCategory'] = $request->BusinessCategory;
        BusinessLicense::where('BusinessRegId',$id)->update($BusinessLicense);

        // $BusinessLicense->save();

        return $this->success([
            'business' => $business,
            'BusinessLicense' => $BusinessLicense
        ]);

        // return $business;


    }

    public function dropDownData(){
        $BusinessLicenseCat = BusinessLicenseCategory::get();
        $business_type_normal = BusinessType::where('type','normal')->get();
        $business_type_non_profit = BusinessType::where('type','non_profit')->get();
        // $leagues = BusinessLicenseCategory::get();
        // $leagues = BusinessLicenseCategory::get();
        // $leagues = BusinessLicenseCategory::get();

        return $this->success([
            'BusinessLicenseCategory' => $BusinessLicenseCat,
            'business_type_normal' => $business_type_normal,
            'business_type_non_profit' => $business_type_non_profit
        ]);
    }
    public function save(Request $request)
    {
        // return $request;
        $assessment_images = new PropertyAssessmentDetail();
        \Illuminate\Support\Facades\Log::debug($request->all());
        $this->propertyId = $request->input('property_id');
        
        if ($this->propertyId && $property = Property::find($this->propertyId)) {
            
            $assessment_images = $property->assessment()->first();
        }elseif($property = Property::where('random_id', $request->random_id)->where('random_id', '<>', '')->first()){
        	$this->propertyId = $property->id;
        	$assessment_images = $property->assessment()->first();
        }

        /* @var User */
        $user = $request->user();

       /* $validator = $this->validator($request, $assessment_images);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        } */

        \DB::beginTransaction();

        $rate = $this->calculateNewRate($request);

        /* @var $property Property */
        $property = $user->properties()->firstOrNew(['id' => $this->propertyId]);

        // $groupName = $request->group_name;
        // $totalAdjustmentPercent = array_sum($request->adjustment_percentage);
        // $millrs = MillRate::where('group_name', $groupName)->first();
        // $millRate = 0;
        // if($millrs){
        //     $millRate = $millrs->rate;
        // }

        // echo '$groupName->'.$groupName.'<br/>';
        // echo 'totalAdjustmentPercent->'.$totalAdjustmentPercent.'<br/>';
        // echo 'millRate->'.$millRate.'<br/>';

        // exit;

        

        $property->fill([
            'assessment_area' => $request->assessment_area,
            'street_number' => $request->property_street_number,
            'street_numbernew' => $request->property_street_numbernew,
            'street_name' => $request->property_street_name,
            'ward' => $request->property_ward,
            'constituency' => $request->property_constituency,
            'section' => $request->property_section,
            'chiefdom' => $request->property_chiefdom,
            'district' => $request->property_district ? $request->property_district : $user->assign_district,
            'province' => $request->property_province,
            'postcode' => $request->property_postcode,
            'organization_addresss' => $request->organization_address ? $request->organization_address : null,
            'organization_tin' => $request->organization_tin ? $request->organization_tin : null,
            'organization_type' => $request->organization_type ? $request->organization_type : null,
            'organization_name' => $request->organization_name ? $request->organization_name : null,
            'is_organization' => $request->input('is_organization', false),
            'is_completed' => $request->input('is_completed', false),
            'is_property_inaccessible' => $request->input('is_property_inaccessible', false),
            'is_draft_delivered' => $request->input('is_draft_delivered', false),
            'delivered_name' => $request->input('delivered_name'),
            'delivered_number' => $request->input('delivered_number'),
               
            // 'window_type_value' =>($request->window_type_condition)? $request->window_type_condition['value'] : null,
            'random_id' => $request->input('random_id'),

        ]);


        $recipient_photo = null;

        if ($request->hasFile('delivered_image')) {
            $recipient_photo = $request->delivered_image->store(Property::DELIVERED_IMAGE);
            $property->delivered_image = $recipient_photo;
        }

        $property->save();

        $property->propertyInaccessible()->sync($request->property_inaccessible);

        $landlord = $property->landlord()->firstOrNew([]);

        /* landlord image */
        $landlord_image = $landlord->image;

        if ($request->hasFile('landlord_image')) {
            if ($landlord->hasImage()) {
                unlink($landlord->getImage());
            }
            $landlord_image = $request->landlord_image->store(Property::ASSESSMENT_IMAGE);
        }


        $landlord_title_label = UserTitleTypes::where('id',$request->landlord_ownerTitle_id)->value('label');
        /* Save/Update landlord details*/
        $landlord->fill([
            'ownerTitle' => $request->landlord_ownerTitle_id,
            'first_name' => $request->landlord_first_name,
            'middle_name' => $request->landlord_middle_name,
            'surname' => $request->landlord_surname,
            'sex' => $request->landlord_sex,
            'street_number' => $request->landlord_street_number,
            'street_numbernew' => $request->landlord_street_numbernew,
            'street_name' => $request->landlord_street_name,
            'email' => $request->landlord_email,
            'image' => $landlord_image,
            'id_number' => $request->landlord_id_number,
            'id_type' => $request->landlord_id_type,
            'tin' => $request->landlord_tin,
            'ward' => $request->landlord_ward,
            'constituency' => $request->landlord_constituency,
            'section' => $request->landlord_section,
            'chiefdom' => $request->landlord_chiefdom,
            'district' => $request->landlord_district,
            'province' => $request->landlord_province,
            'postcode' => $request->landlord_postcode,
            'mobile_1' => $request->landlord_mobile_1,
            'mobile_1' => $request->landlord_mobile_1,
            'mobile_2' => $request->landlord_mobile_2,
        ]);

        $landlord->save();

        /* Save/Update occupancy details*/

        $occupancy = $property->occupancy()->firstOrNew([]);

        $tenant_title_label = UserTitleTypes::where('id',$request->tenant_ownerTitle_id)->value('label');
        $occupancy->fill([
            'type' => $request->occupancy_type,
            'ownerTenantTitle' => $request->ownerTenantTitle,
            'tenant_first_name' => $request->occupancy_tenant_first_name,
            'middle_name' => $request->occupancy_middle_name,
            'surname' => $request->occupancy_surname,
            'mobile_1' => $request->occupancy_mobile_1,
            'mobile_2' => $request->occupancy_mobile_2
        ]);

        $occupancy->save();

        if ($request->occupancy_type && count(array_filter($request->occupancy_type))) {
            foreach (array_filter($request->occupancy_type) as $types) {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
            }
            $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
        }

        /* @var $assessment PropertyAssessmentDetail */

        /* Save/Update assessment details*/
        if ($property->assessment()->exists()) {
            $assessment = $property->generateAssessments();
        } else {
            $assessment = $property->assessment()->firstOrNew([]);
        }
        $water_percentage = 0;
        $electrical_percentage = 0;
        $waster_precentage = 0;
        $market_percentage = 0;
        $hazardous_percentage = 0;
        $drainage_percentage = 0;
        $informal_settlement_percentage = 0;
        $easy_street_access_percentage = 0;
        $paved_tarred_street_percentage = 0;

        $groupName = $request->group_name;
        $adjustmentPercentage = [];
        if(is_array($request->adjustment_ids)){
            $adjustmentsArray = $request->adjustment_ids;
            foreach($adjustmentsArray as $id)
            {
                $name_perc = Adjustment::where('id',$id)->pluck('name');
                if($id == 1){
                    $water_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 2){
                    $electrical_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 3){
                    $waster_precentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 4){
                    $market_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 5){
                    $hazardous_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 6){
                    $informal_settlement_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 7){
                    $easy_street_access_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 8){
                    $paved_tarred_street_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }else{
                    $drainage_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }
            }
        }
        if(is_array($request->adjustment_ids)){
            $adjustmentPercentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', $request->adjustment_ids)->pluck('percentage')->toArray(); 
        }


        $totalAdjustmentPercent = array_sum($adjustmentPercentage);
        
        //$totalAdjustmentPercent = array_sum($request->adjustment_percentage);

        $district = District::where('name', $request->property_district)->first();
        $mill_rate_group_name = $request->group_name;
        $millrs = MillRate::where('group_name', $mill_rate_group_name)->first();
        // $millRate = 0;
        //  $millRate = 2.25;
        $millRate = 2.5;
        if($millrs){
            $millRate = $millrs->rate;
        }

        // echo '$groupName->'.$groupName.'<br/>';
        // echo 'totalAdjustmentPercent->'.$totalAdjustmentPercent.'<br/>';
        // echo 'millRate->'.$millRate.'<br/>';

        // exit;

        $assessment_data = [
            'property_wall_materials' => $request->assessment_wall_materials_id,
            'roofs_materials' => $request->assessment_roofs_materials_id,
            'property_window_type' => $request->assessment_window_type_id,
            'property_dimension' => $request->assessment_dimension_id,
            'length' => $request->assessment_length,
            'breadth' => $request->assessment_breadth,
            'square_meter' => $request->assessment_square_meter,
            'property_rate_without_gst' => $request->assessmentRateWithoutGST > 0 ? $request->assessmentRateWithoutGST: $rate['rateWithoutGST'],
            'property_gst' => $request->assessmentRateWithGST > 0 ? $request->assessmentRateWithGST : $rate['GST'],
            'property_rate_with_gst' => $rate['rateWithGST'],
            'property_use' => $request->assessment_use_id,
            'zone' => $request->assessment_zone_id,
            'no_of_mast' => $request->total_mast,
            'no_of_shop' => $request->total_shops,
            'no_of_compound_house' => $request->total_compound_house,
            'compound_name' => $request->compound_name,
            'gated_community' => $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : null,
            'total_adjustment_percent' => $totalAdjustmentPercent,
            'group_name' => $mill_rate_group_name,
            'mill_rate' => $millRate,

            'wall_material_percentage' =>($request->wallPer)? $request->wallPer : 0,
            'wall_material_type' =>($request->wallType)? $request->wallType : 'A',

            'roof_material_percentage' =>($request->roofPer)? $request->roofPer : 0,
            'roof_material_type' =>($request->roofType)? $request->roofType : 'A',

            'value_added_percentage' =>($request->valuePer)? $request->valuePer : 0,
            'value_added_type' =>($request->valueType)? $request->valueType : 'A',

            'window_type_percentage' =>($request->windowPer)? $request->windowPer : 0,
            'window_type_type' =>($request->windowType)? $request->windowType : 'A',
            
            'water_percentage' => $water_percentage,
            'electricity_percentage' => $electrical_percentage,
            'waste_management_percentage'=> $waster_precentage,
            'market_percentage'=> $market_percentage,
            'hazardous_precentage'=> $hazardous_percentage,
            'drainage_percentage'=> $drainage_percentage,
            'informal_settlement_percentage'=> $informal_settlement_percentage,
            'easy_street_access_percentage'=> $easy_street_access_percentage,
            'paved_tarred_street_percentage'=> $paved_tarred_street_percentage,
            'sanitation' => $request->sanitation
        ];

        if ($request->hasFile('assessment_images_1')) {
            if ($assessment_images->hasImageOne()) {
                unlink($assessment_images->getImageOne());
            }
            $assessment_data['assessment_images_1'] = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->hasFile('assessment_images_2')) {
            if ($assessment_images->hasImageTwo()) {
                unlink($assessment_images->getImageTwo());
            }
            $assessment_data['assessment_images_2'] = $request->assessment_images_2->store(Property::ASSESSMENT_IMAGE);
        }








        // if ($request->input('is_draft_delivered')) {
        if ($request->input('isDraftDelivered')) {

            // if (!$assessment->demand_note_delivered_at) {
            // if ($assessment->demand_note_delivered_at) {

                //-----start----------//

                //part 1
                    $CurrentYearAssessmentAmount = 0;
                    $PastPayableDue = 0;
                    $Penalty = 0;
                    $CurrentYearTotalPayment2021 = 0;
                    $CurrentYearTotalDue = 0;
                    $CurrentYearTotalDue2021 = 0;

                    $PastPayableDue2022 = 0;
                    $CurrentYearTotalPayment2022 = 0;
                    $Penalty2022 = 0;
                    $CurrentYearTotalDue2022 = 0;
                    $PastPayableDue = 0;

                 // part -2
                for($i=0; $i<count(@$property->assessmentHistory); $i++){
                  
                    $AssessmentYear = $property->assessmentHistory[$i]->created_at->year;
                    $CurrentYearAssessmentAmount = $property->assessmentHistory[$i]->current_year_assessment_amount;
                    if($PastPayableDue > 0){
                        $Penalty = $PastPayableDue*0.25;
                    }
                    else{
                        $Penalty =0;
                    }
                    
                    $CurrentYearTotalPayment = $property->assessmentHistory[$i]->getCurrentYearTotalPayment();
                    $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                   
                   // if($i==2){
                   // dd($AssessmentYear,$CurrentYearAssessmentAmount,$PastPayableDue,$Penalty,$CurrentYearTotalPayment,$CurrentYearTotalDue,count(@$property->assessmentHistory));
                   // }
                    if($AssessmentYear!="2023"){
                       $PastPayableDue = $CurrentYearTotalDue;
                     }
                }


                // part-3
                 // dd($AssessmentYear,$CurrentYearAssessmentAmount,$PastPayableDue,$Penalty,$CurrentYearTotalPayment,$CurrentYearTotalDue);
                 $arr=[];
                 $arr['property_id']=$property->id ;
                 $arr['AssessmentYear']=@$AssessmentYear ;
                 $arr['CurrentYearAssessmentAmount']= number_format($CurrentYearAssessmentAmount,2) ;
                 $arr['PastPayableDue']= number_format($PastPayableDue,2) ;
                 $arr['Penalty']= number_format($Penalty,2) ;
                 $arr['CurrentYearTotalPayment']= number_format(@$CurrentYearTotalPayment,2) ;
                 $arr['CurrentYearTotalDue']= number_format(@$CurrentYearTotalDue,2);

                 // dd(implode(",",$arr));
                 $string=implode(",",$arr);

                // part-4 main sms part
                if ($mobile_number = $property->landlord->mobile_1) {
        
                    // dd($arr);
                     (new \App\Helper\CustomHelper)->send_sms($arr, $property->landlord->mobile_1);


                    //$property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
                    $name = $request->input('delivered_name');
                    $year = now()->format('Y');
                    if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                        $property->landlord->notify(new DraftDeliveredSMSNotification($property, $mobile_number, $name, $year));
                    }
                }
            // }
            $assessment_data['demand_note_delivered_at'] = now();
            $assessment_data['demand_note_recipient_name'] = $request->input('delivered_name');
            $assessment_data['demand_note_recipient_mobile'] = $request->input('delivered_number');
            $assessment_data['demand_note_recipient_photo'] = $recipient_photo;
        }






        $assessment->fill($assessment_data);

        if ($request->input('swimming_pool')) {
            $assessment->swimming()->associate($request->input('swimming_pool'));
        }

        $assessment->save();


        // property category
        $categories = getSyncArray($request->input('assessment_categories_id'), ['property_id' => $property->id]);
        $assessment->categories()->sync($categories);


       

        /* property type (Habitat) multiple value */
        $types = getSyncArray($request->input('assessment_types'), ['property_id' => $property->id]);
        $assessment->types()->sync($types);

        /* Property type (typesTotal) multiple value */
        if ($request->input('assessment_types_total')) {
            $typesTotal = getSyncArray($request->input('assessment_types_total'), ['property_id' => $property->id]);
            $assessment->typesTotal()->sync($typesTotal);
        }


        /* property value added multiple value */
        $valuesAdded = getSyncArray($request->input('assessment_value_added_id'), ['property_id' => $property->id]);
        $assessment->valuesAdded()->sync($valuesAdded);

        /* Geo Registry Data  */

        $geoData = [
            'point1' => $request->registry_point1,
            'point2' => $request->registry_point2,
            'point3' => $request->registry_point3,
            'point4' => $request->registry_point4,
            'point5' => $request->registry_point5,
            'point6' => $request->registry_point6,
            'point7' => $request->registry_point7,
            'point8' => $request->registry_point8,
            'digital_address' => $request->registry_digital_address,
            'dor_lat_long' => str_replace(',', ', ', $request->dor_lat_long),
        ];

        if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
            list($lat, $lng) = explode(',', $request->dor_lat_long);
            $geoData['open_location_code'] = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
        }

       // !$geoData['digital_address'] || $geoData = $this->addIdToDigitalAddress($geoData, $property);

        $geoRegistry = $property->geoRegistry()->firstOrNew([]);

        $geoRegistry->fill($geoData);
        $geoRegistry->save();

        /* save and update Registry Image */
        $registryImageId = [];
        $allregistryImage = $property->registryMeters()->pluck('id')->toArray();
        if ($request->registry && count($request->registry) and is_array($request->registry)) {
            foreach (array_filter($request->registry) as $key => $registry) {
                $image = null;
                $registryImageId[] = isset($registry['id']) ? (int) $registry['id'] : '';
                if ($request->hasFile('registry.' . $key . '.meter_image')) {
                    $registryMeters = $property->registryMeters()->where('id', isset($registry['id']) ? (int) $registry['id'] : '')->first();
                    if ($registryMeters && $registryMeters->image != null) {
                        if ($registryMeters->hasImage())
                            unlink($registryMeters->getImage());
                        // $registryMeters->delete();
                    }
                    $image = $registry['meter_image']->store(Property::METER_IMAGE);
                    $property->registryMeters()
                        ->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number'], 'image' => $image]);
                } else {
                    $property->registryMeters()->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number']]);
                }
            }
        }

        /* delete registry image which not updated*/

        $removeImageId = array_diff($allregistryImage, $registryImageId);
        if (count($removeImageId)) {
            foreach ($removeImageId as $diffId) {
                $registryMetersDelete = $property->registryMeters()->where('id', $diffId)->first();
                if ($registryMetersDelete && $registryMetersDelete->image != null) {
                    if ($registryMetersDelete->hasImage()) {
                        unlink($registryMetersDelete->getImage());
                    }

                    //$registryMetersDelete->delete();
                }
                $registryMetersDelete->delete();
            }
        }

        \DB::commit();

        $getProperty = $property->with('landlord', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'occupancies', 'categories', 'propertyInaccessible')->where('id', $property->id)->get();

                    $adjustments = [
            
                [ 'id' => '1',
                 'name' => 'Water Supply',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [ 'id' => '2',
                 'name' => 'Electricity',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [
                    'id'=> '3',
                    'name'=> 'Waste Management Services/Points/Locations',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '5',
                    'name'=> 'Hazardous Location/Environment',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '7',
                    'name'=> 'Easy Street Access',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ]
     
             ];



// ----------------   start ----------------------------------//
             //first find and delete previous data
             $srch=PropertyToCounsilGroupA::where('property_id',$property->id)->where('year',date("Y"))->first();
             if($srch){
                $dltall=PropertyToCounsilGroupA::where('property_id',$property->id)->where('year',date("Y"))->delete();
             }
             //insert new data
             $sumOfPercentage=0;
            if(@$request->newAdjustmentIds){
             foreach(json_decode(@$request->newAdjustmentIds) as $val ){
               if(@$val->amount || @$val->value){
                }else{
                if($val->sign=="+"){
                 $sumOfPercentage=$sumOfPercentage+(int)$val->percentage;
                }else{
                  $sumOfPercentage=$sumOfPercentage-(int)$val->percentage;
                }

                 $insData=new PropertyToCounsilGroupA;
                 $insData->property_id=$property->id;
                 $insData->adjustment_id=$val->id;
                  $insData->year=date("Y");
                 $insData->save();
             }// end if for amount
            }//foreach end
           }
            //update the percentage to propert assement details table
            $updt=PropertyAssessmentDetail::where('property_id',$property->id)->whereYear('created_at',date("Y"))->update(['total_adjustment_percent'=>$sumOfPercentage]);




             //new code for insert  assessment_categories_id as property type
            if(@$request->property_type_id){
                $srch=PropertyToPropertyType::orderBy('id','desc')->first();
                // dd($srch,$property->id);
                if($srch){   

                    $u=PropertyToPropertyType::where('id',$srch->id)->update(['property_id'=>$property->id,'type_id'=>$request->property_type_id]);
                        // dd(1,$property->id);
                        // dd($insert_property_type);
                }else{
                    $insert_property_type=new PropertyToPropertyType;
                    $insert_property_type->property_id=$property->id;
                    $insert_property_type->type_id=$request->property_type_id;
                    $insert_property_type->save();
                    // dd(2,$property->id);
                }
            }



//------------------------ end --------------------------------------//            
        
        return $this->success([
            'property_id' => $property->id,
            'sink' => 1,
            'is_completed' => $property->is_completed,
            'property' => $getProperty,
            'values_adjustment' => $adjustments,
            'sumOfPercentage'=>$sumOfPercentage,
            'rate'=>$rate
        ]);
    }

    protected function addIdToDigitalAddress($geoData, $property)
    {
        $digitalAddress = $property->geoRegistry()->first();

        //if (!$digitalAddress) {
         //   $geoData['digital_address'] = $geoData['digital_address'];

            return $geoData;
       // }
        //
        //        $addresses = explode('-', $digitalAddress->digital_address);
        //
        //        $last = count($addresses) > 1 ? intval(array_last($addresses)) : array_last($addresses);
        //
        //        if($last != $property->id)
        //        {
        //            $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;
        //
        //            return $geoData;
        //        }
        //
        //        $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;

        return $geoData;
    }

    public function validator($request, $assessment_images)
    {
        $registryimage = new RegistryMeter();

        if ($this->propertyId && $property = Property::find($this->propertyId)) {
            $registryimage = $property->registryMeters()->first();
        }

        $validationRequriedIf = $registryimage && $registryimage->image != null ? '' : 'required_if:is_completed,1';

        if (isset($request->is_completed) && $request->is_completed == 1) {
            $organizationYes = 'required_if:is_organization,1';
            $organizationNo = 'required_if:is_organization,0';
            $registryField = 'required';
        } else {
            $organizationYes = '';
            $organizationNo = '';
            $registryField = 'nullable';
        }

        $validator = Validator::make($request->all(), [
            'landlord_ownerTitle_id' =>'integer',
            'tenant_ownerTitle_id' => 'integer',
            'is_completed' => 'nullable|boolean',
            'is_organization' => 'required|boolean',
            'organization_name' => '' . $organizationYes . '|string|max:255',
            'organization_type' => '' . $organizationYes . '|string|max:255',
            'organization_tin' => 'nullable|string|max:255',
            'organization_address' => '' . $organizationYes . '|string|max:255',
            'assessment_area' => 'string',
            'property_street_number' => 'required_if:is_completed,1|string',
            'property_street_numbernew' => 'required_if:is_completed,1|string',
            'property_street_name' => 'required_if:is_completed,1|string|max:255|nullable',
            'property_ward' => 'required_if:is_completed,1|integer',
            'property_constituency' => 'required_if:is_completed,1|integer',
            'property_section' => 'required_if:is_completed,1|string|max:255',
            'property_chiefdom' => 'required_if:is_completed,1|string|max:255',
            'property_district' => 'required_if:is_completed,1|string|max:255',
            'property_province' => 'required_if:is_completed,1|string|max:255',
            'property_postcode' => 'required_if:is_completed,1|string|max:255',
            'landlord_first_name' => '' . $organizationNo . '|string|max:255',
            'landlord_middle_name' => 'nullable|string|max:255',
            'landlord_surname' => '' . $organizationNo . '|string|max:255',
            'landlord_sex' => '' . $organizationNo . '|string|max:255',
            'landlord_street_number' => 'string',
            'landlord_street_numbernew' => 'string',
            'landlord_street_name' => 'string|max:255',
            'landlord_email' => "nullable|email",
            'landlord_tin' => 'nullable|string|max:255',
            'landlord_id_type' => 'nullable|string|max:255',
            'landlord_id_number' => 'nullable|string|max:255',
            'landlord_image' => 'nullable|max:10240‬',
            'landlord_ward' => 'required_if:is_completed,1|integer',
            'landlord_constituency' => 'required_if:is_completed,1|integer',
            'landlord_section' => 'required_if:is_completed,1|string|max:255',
            'landlord_chiefdom' => 'required_if:is_completed,1|string|max:255',
            'landlord_district' => 'required_if:is_completed,1|string|max:255',
            'landlord_province' => 'required_if:is_completed,1|string|max:255',
            'landlord_postcode' => 'required_if:is_completed,1|string|max:255',
            'landlord_mobile_1' => 'required_if:is_completed,1|string|max:15',
            'landlord_mobile_2' => 'nullable|string|max:15',
            'occupancy_type' => 'nullable|required_if:is_completed,1|array',
            'occupancy_type.*' => 'nullable|required_if:is_completed,1|in:Owned Tenancy,Rented House,Unoccupied House',
            'occupancy_tenant_first_name' => 'nullable|string|max:255',
            'occupancy_middle_name' => 'nullable|string|max:255',
            'occupancy_surname' => 'nullable|string',
            'occupancy_mobile_1' => 'nullable|string|max:15',
            'occupancy_mobile_2' => 'nullable|string|max:15',
            'assessment_categories_id' => 'nullable|required_if:is_completed,1|array',
            'assessment_categories_id.*' => 'nullable|required_if:is_completed,1|exists:property_categories,id',
            'assessment_images_1' => '' . ($assessment_images->assessment_images_1 == null ? 'required_if:is_completed,1|' : ''),
            'assessment_images_2' => '' . ($assessment_images->assessment_images_2 == null ? 'required_if:is_completed,1|' : ''),
            'assessment_types' => 'required_if:is_completed,1|array|max:2',
            'assessment_types.*' => 'required_if:is_completed,1|exists:property_types,id',
            "assessment_types_total" => 'nullable|array|max:2',
            "assessment_types_total.*" => 'nullable|exists:property_types,id',
            'assessment_wall_materials_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_length' => 'nullable',
            'assessment_breadth' => 'nullable',
            'assessment_square_meter' => 'nullable',
            'assessment_roofs_materials_id' => 'required_if:is_completed,1|string|max:255',
            //'assessment_dimension_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_value_added_id' => 'required_if:is_completed,1|array',
            'assessment_value_added_id.*' => 'required_if:is_completed,1|exists:property_value_added,id',
            'assessment_use_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_zone_id' => 'required_if:is_completed,1|string|max:255',
            'compound_name' => 'nullable|string|max:255',
            'total_compound_house' => 'nullable|string|max:255',
            'total_shops' => 'nullable|string|max:255',
            'total_mast' => 'nullable|string|max:255',
            'registry' => 'array',
            'registry.*.meter_image' => [
                'max:10240‬'
            ],
            'registry.*.meter_number' => 'nullable|string|max:255',
            'registry_point1' => 'required_if:is_completed,1|string|max:255',
            'registry_point2' => 'required_if:is_completed,1|string|max:255',
            'registry_point3' => 'required_if:is_completed,1|string|max:255',
            'registry_point4' => 'nullable|string|max:255',
            'registry_point5' => 'nullable|string|max:255',
            'registry_point6' => 'nullable|string|max:255',
            'registry_point7' => 'nullable|string|max:255',
            'registry_point8' => 'nullable|string|max:255',
            'registry_digital_address' => [
                'required_if:is_completed,1',
                'string',
                'max:159'
            ],
            'dor_lat_long' => 'nullable|required_if:is_completed,1|max:190',
            'gated_community' => 'nullable|required_if:is_completed,1|boolean',
            'swimming_pool' => 'nullable|exists:swimmings,id',
            'is_property_inaccessible' => 'required|boolean',
            'property_inaccessible' => 'nullable|required_if:is_property_inaccessible,1|array',
            'property_inaccessible.*' => 'nullable|required_if:is_property_inaccessible,1|exists:property_inaccessibles,id',
            'is_draft_delivered' => 'nullable|boolean',
            'delivered_name' => 'nullable|max:70',
            'delivered_number' => 'nullable|string|max:55',
            'delivered_image' => 'nullable|max:10240‬'

        ]);

        return $validator->after(function ($validator) use ($request) {

            $openLocationCode = '';
            if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
                list($lat, $lng) = explode(',', $request->dor_lat_long);
                $openLocationCode = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
            }
            
            if($this->propertyId){
                $propertyExist = Property::where('id', '<>', $this->propertyId)
                ->whereHas('geoRegistry', function($q) use ($openLocationCode){
                    $q->where('open_location_code', $openLocationCode)
                    ->where('open_location_code','<>','');
                })->first();
            }else{
                $propertyExist = Property::whereHas('geoRegistry', function($q) use ($openLocationCode){
                    $q->where('open_location_code', $openLocationCode)
                    ->where('open_location_code','<>','');;
                })->first();
            }
            
            if ($propertyExist) {
                //$validator->errors()->add('open_location_code', 'This digital address is already exist');
            }

        });

    }













    public function getIncompleteProperty(Request $request)
    {
        $property = $request->user()->properties()
            ->with('images', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'payments', 'landlord', 'assessment.typesTotal:id,label,value', 'assessment.types:id,label,value', 'assessment.valuesAdded:id,label,value', 'occupancies:id,occupancy_type,property_id', 'assessment.categories:id,label,value', 'propertyInaccessible:id,label')
            ->orderBy('id', 'desc')
            ->get();
       
          
                        $adjustments = [
            
                [ 'id' => '1',
                 'name' => 'Water Supply',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [ 'id' => '2',
                 'name' => 'Electricity',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [
                    'id'=> '3',
                    'name'=> 'Waste Management Services/Points/Locations',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '5',
                    'name'=> 'Hazardous Location/Environment',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '7',
                    'name'=> 'Easy Street Access',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ]
     
             ];
             
             $parr = [];
             foreach($property as $p)
             {
                $adjustments = [];
                // dd($p->assessment->water_percentage!=0);
                // $water_percentage_assessment = $p->assessment->water_percentage;
                

                if(!empty($p->assessment->water_percentage) && $p->assessment->water_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '1',
                    'name' => 'Water Supply',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->electricity_percentage) && $p->assessment->electricity_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '2',
                    'name' => 'Electricity',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->waste_management_percentage) && $p->assessment->waste_management_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '3',
                    'name' => 'Waste Management Services/Points/Locations',
                    'percentage' => '5',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->market_percentage) && $p->assessment->market_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '4',
                    'name' => 'Market',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->hazardous_precentage) && $p->assessment->hazardous_precentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '5',
                    'name' => 'Hazardous Location/Environment',
                    'percentage' => '15',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->informal_settlement_percentage) && $p->assessment->informal_settlement_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '6',
                    'name' => 'Informal settlement',
                    'percentage' => '21',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->easy_street_access_percentage) && $p->assessment->easy_street_access_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '7',
                    'name' => 'Easy Street Access',
                    'percentage' => '7',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->paved_tarred_street_percentage) && $p->assessment->paved_tarred_street_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '8',
                    'name' => 'Paved/Tarred Road/Street',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if(!empty($p->assessment->drainage_percentage) && $p->assessment->drainage_percentage != 0 )
                {
                    array_push($adjustments, 
                    [ 'id' => '9',
                    'name' => 'Drainage',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                // $all_adjustments_under_that_property=PropertyToCounsilGroupA::where('property_id',$p->id)->where('year',$p->assessment->created_at->format('Y'))->pluck('adjustment_id')->toArray();
                // if(count($all_adjustments_under_that_property)>0){
                //   $all_adjustment=CounsilAdjustmentGroupA::whereIn('id',$all_adjustments_under_that_property)->get();
                // }
                // else{
                //     $all_adjustment=[];
                // }
                if(isset($p->assessment) && $p->assessment !== null && is_object($p->assessment)) {
                    $assessmentYear = $p->assessment->created_at->format('Y');
                    $all_adjustments_under_that_property = PropertyToCounsilGroupA::where('property_id', $p->id)
                        ->where('year', $assessmentYear)
                        ->pluck('adjustment_id')
                        ->toArray();
                
                    if(count($all_adjustments_under_that_property) > 0) {
                        $all_adjustment = CounsilAdjustmentGroupA::whereIn('id', $all_adjustments_under_that_property)->get();
                    } else {
                        $all_adjustment = [];
                    }
                } else {
                    // Handle the case where $p->assessment is not set or not an object
                    // You might want to log this or handle it differently based on your application's logic
                    $all_adjustment = [];
                }
                array_add($p, 'values_adjustment', $all_adjustment);
             }
             
             
        return $this->success([
            'property' => $property,
            'values_adjustment' => $adjustments
        ]);
    }


















    public function getMyDistrict(Request $request)
    {
        // $result = BoundaryDelimitation::where('district', $request->user()->assign_district)->get();

        $result = \DB::table('boundary_delimitations')
            ->leftJoin('districts', 'boundary_delimitations.district', '=', 'districts.name')
            ->select('boundary_delimitations.id', 'boundary_delimitations.ward', 'boundary_delimitations.constituency', 'boundary_delimitations.section', 'boundary_delimitations.chiefdom', 'boundary_delimitations.district', 'boundary_delimitations.province', 'boundary_delimitations.council', 'boundary_delimitations.prefix', 'districts.group_name')
            ->where('boundary_delimitations.district', $request->user()->assign_district)
            ->get();


        return $this->success([
            'result' => $result,
        ]);
    }

    public function calculateRate($request)
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
        $no_of_mast = $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];

        if (isset($request->assessment_value_added_id) && is_array($request->assessment_value_added_id)) {
            foreach ($valueAdded as $value) {
                if (in_array($value, $request->assessment_value_added_id)) {
                    $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                    if ($value == 9) {
                        $shopValue = $amount->value;
                    }
                    if ($value == 8) {
                        $mastValue = $amount->value;
                    }
                }
            }
            $valueAdded = array_diff($request->assessment_value_added_id, $valueAdded);
        }

        if (isset($request->assessment_categories_id) and $request->assessment_categories_id != null)
            $property_categories = PropertyCategory::whereIn('id', $request->assessment_categories_id)->get();

        if (isset($request->assessment_wall_materials_id) and $request->assessment_wall_materials_id != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->assessment_wall_materials_id);

        if (isset($request->assessment_roofs_materials_id) and $request->assessment_roofs_materials_id != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->assessment_roofs_materials_id);

        if (is_array($request->assessment_value_added_id) and count($request->assessment_value_added_id) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->assessment_types) and count($request->assessment_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->assessment_types)->sum('value');

        if (isset($request->assessment_dimension_id) and $request->assessment_dimension_id != null)
            $property_dimension = PropertyDimension::select('value')->find($request->assessment_dimension_id);

        if (isset($request->assessment_use_id) and $request->assessment_use_id != null)
            $property_use = PropertyUse::select('value')->find($request->assessment_use_id);

        if (isset($request->assessment_zone_id) and $request->assessment_zone_id != null)
            $zones = PropertyZones::select('value')->find($request->assessment_zone_id);

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
        $swimming_pool = optional(Swimming::find($request->swimming_pool))->value;

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

        return $result;
    }

    // ALTER TABLE mrms_dpmtommy.activity_log MODIFY COLUMN id INT auto_increment primary key























    public function calculateNewRate($request)
    {
        $property_category = 0;
        $rate_square_meter = 2750.00;
        $wall_material = 0;
        $window_val = 0;
        $roof_material = 0;
        $value_added_val = 0;
        $property_type_val = 0;
        $property_dimension = 0;
        $property_use = 0;
        $zones = 0;
        $no_of_shops = $request->total_shops ? $request->total_shops : 0;
        $no_of_mast = $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];

        if (isset($request->assessment_value_added_id) && is_array($request->assessment_value_added_id)) {
            foreach ($valueAdded as $value) {
                if (in_array($value, $request->assessment_value_added_id)) {
                    $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                    if ($value == 9) {
                        $shopValue = $amount->value;
                    }
                    if ($value == 8) {
                        $mastValue = $amount->value;
                    }
                }
            }
            $valueAdded = array_diff($request->assessment_value_added_id, $valueAdded);
        }
        
        if(isset($request->assessment_window_type_id) and $request->assessment_window_type_id != null){
            $window_val = PropertyWindowType::select('value')->find($request->assessment_window_type_id);
        }
        if (isset($request->assessment_categories_id) and $request->assessment_categories_id != null)
            $property_categories = PropertyCategory::whereIn('id', $request->assessment_categories_id)->get();

        if (isset($request->assessment_wall_materials_id) and $request->assessment_wall_materials_id != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->assessment_wall_materials_id);

        if (isset($request->assessment_roofs_materials_id) and $request->assessment_roofs_materials_id != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->assessment_roofs_materials_id);

        if (is_array($request->assessment_value_added_id) and count($request->assessment_value_added_id) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->assessment_types) and count($request->assessment_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->assessment_types)->sum('value');

        // if (isset($request->assessment_dimension_id) and $request->assessment_dimension_id != null)
        //     $property_dimension = PropertyDimension::select('value')->find($request->assessment_dimension_id);
        if (isset($request->assessment_length) and $request->assessment_length != null and (isset($request->assessment_breadth) and $request->assessment_breadth != null) ) {



            if ($request->has('property_district')) {
                $district = District::where('name', $request->property_district)->first();
                if ($district->sq_meter_value) {
                    //$rate_square_meter = $district->sq_meter_value;
                }
            }

            $property_dimension = ($request->assessment_length * $request->assessment_breadth) * $rate_square_meter;
            //$property_dimension = ($request->assessment_area) * $rate_square_meter;
            //$property_dimension = $request->property_dimension * getSystemConfig(SystemConfig::CURRENT_RATE);
            //$property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        }

        if (isset($request->assessment_area) and $request->assessment_area != null) {



            if ($request->has('property_district')) {
                $district = District::where('name', $request->property_district)->first();
                if ($district->sq_meter_value) {
                    //$rate_square_meter = $district->sq_meter_value;
                }
            }

            //$property_dimension = ($request->assessment_length * $request->assessment_breadth) * $rate_square_meter;
            $property_dimension = ($request->assessment_area) * $rate_square_meter;
            //$property_dimension = $request->property_dimension * getSystemConfig(SystemConfig::CURRENT_RATE);
            //$property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        }


        if (isset($request->assessment_use_id) and $request->assessment_use_id != null)
            $property_use = PropertyUse::select('value')->find($request->assessment_use_id);

        if (isset($request->assessment_zone_id) and $request->assessment_zone_id != null)
            $zones = PropertyZones::select('value')->find($request->assessment_zone_id);

        /*number of Shop available*/

        if ($shopValue > 0)
            $value_added_val = $value_added_val + ($shopValue * $no_of_shops);

        /*number of mast available*/
        if ($mastValue > 0)
            $value_added_val = $value_added_val + ($mastValue * $no_of_mast);

        // $step1 = $wall_material['value'] + $roof_material['value'] + $value_added_val;
        // $step2 = $property_type_val;
        // $step3 = $property_dimension['value'];
        // $step4 = $property_use['value'];
        // $step5 = $zones['value'];
        // $step6 = 0;
        $swimming_pool = optional(Swimming::find($request->swimming_pool))->value;
        $step1 = optional($wall_material)->value + optional($roof_material)->value + $value_added_val + optional($window_val)->value + ($swimming_pool ? $swimming_pool : 0);
        $step2 = optional($property_use)->value;
        $step3 = optional($zones)->value;
        $step4 = $property_type_val;
        //$step3 = $property_dimension['value'];
        $step0 = $property_dimension;
        $step6 = 0;
        

        $gated_community = $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 1;

        if (count($property_categories) && $property_categories->count()) {
            $step6 = 1;

            foreach ($property_categories as $prop_category) {
                $step6 *= $prop_category->value;
            }
        }

        //$result['rateWithoutGST'] = @(((($step1 * $step2 * $step3 * $step4) * $gated_community) + ($swimming_pool ? $swimming_pool : 0)) / ($step6 > 0 ? $step6 : 1));
        $result['rateWithoutGST'] = @((($step0 + ($step1 *  $step2 * $step3 * $step4)) * $gated_community)  + ($swimming_pool ? $swimming_pool : 0)) * ($step6 > 0 ? $step6 : 1);
        $wallMaterialPercentage = ($request->wallPer)? $request->wallPer : 0;
        $roofMaterialPercentage = ($request->roofPer)? $request->roofPer : 0;
        $valueAddedPercentage = ($request->valuePer)? $request->valuePer : 0;
        $windowTypePercentage = ($request->windowPer)? $request->windowPer : 0;

        //Total percentage of property characteristic
        $totalPercentage = array_sum([$wallMaterialPercentage, $roofMaterialPercentage, $valueAddedPercentage, $windowTypePercentage]);





        //If property characteristic exist
        if($totalPercentage){
            $result['rateWithoutGST'] = $result['rateWithoutGST'] + ($result['rateWithoutGST'] * ($totalPercentage/100));  
        }

        //dd($result['rateWithoutGST']);


//----------------//new percentage code
        $sumOfPercentage=0;
         if(@$request->newAdjustmentIds){
             foreach(json_decode(@$request->newAdjustmentIds) as $val ){
                if(@$val->amount || @$val->value){
                }else{
                if($val->sign=="+"){
                 $sumOfPercentage=$sumOfPercentage+(int)$val->percentage;
                }else{
                  $sumOfPercentage=$sumOfPercentage-(int)$val->percentage;
                }
               }// end if for amont
            } // end foreach
         }
            
            $result['percent_of_adjustments'] =$sumOfPercentage;
            // //its minus or plus check that

        //If value added exist NEW CALCULATION
        
        if(@$request->newAdjustmentIds){
          if(count(json_decode(@$request->newAdjustmentIds))>0){
            $result['rateWithoutGST'] = $result['rateWithoutGST'] * ((100+($sumOfPercentage))/100); 
          }
        }


 //------------PREVIOUS CALCULATION --------------//
        // if(is_array($request->adjustment_ids) && count($request->adjustment_ids)){
        //     $adjustmentPercentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', $request->adjustment_ids)->pluck('percentage')->toArray();

        //     $result['rateWithoutGST'] = $result['rateWithoutGST'] * ((100-array_sum($adjustmentPercentage))/100);            
        // }


        $result['GST'] = $result['rateWithoutGST'] * .15;

        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);
        $result['rateWithoutGST'] = $result['rateWithoutGST'] / 1000;

        return $result;
    }



















    public function saveImage(Request $request)
    {

        if ($request->hasFile('assessment_images_1')) {

            $assessment_data = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
        }

        return url(Image::url($assessment_data, 200, 200, ['crop']));
    }


public function createInAccessibleProperties(Request $request)
    {
        $inaccessibleProperty = new InaccessibleProperty;
        $inaccessible_property_img = null;
        try {
            if ($request->hasFile('inaccessible_property_image')) {
                $inaccessible_property_img = $request->inaccessible_property_image->store(InaccessibleProperty::INACCESSBILE_PROPERTY_IMAGE);
            }
        }catch(Exception $e){
            echo $e->getMessage();

        }
        $reason_id = $request->reason;
        $lat = $request->lat;
        $long = $request->long;
        $enumerator = $request->enumerator;
        $reason_label = PropertyInaccessible::where('id',$reason_id)->value('label');
        $inaccessibleProperty->reason = $reason_label;
        $inaccessibleProperty->inaccessbile_property_image =  $inaccessible_property_img;
        $inaccessibleProperty->inaccessbile_property_lat = $lat;
        $inaccessibleProperty->inaccessbile_property_long = $long;
        $inaccessibleProperty->enumerator = $enumerator;
        $inaccessibleProperty->save();
        return $this->success([
            "inaccessible_property" => "Saved"
            // "path" => $destinationPath
        ]);

    }


    public function updatePropertyAssessmentDetail(Request $request)
    {

       
        $property_id = $request->property_id;
        $length = $request->length;
        $breadth = $request->breadth;
        $area = $request->area;
        $is_map_set = $request->is_map_set;
        $detail = PropertyAssessmentDetail::where('id', '=', $request->assessment_id)->firstOrFail();
        $detail->square_meter = round($area,2);
        $detail->length = round($length,2);
        $detail->breadth = round($breadth,2);
        $detail->is_map_set = $is_map_set;
        $detail->save();
        $data = [
            'property_id' => $property_id,
            'length' => $length,
            'area' => $area
        ];

        return $this->success([
            "data" => $data,
            "detail" => $detail
            // "path" => $destinationPath
        ]);
        
    }


    public function updatePropertyAssessmentPensionDiscount(Request $request)
    {
        $property_id = $request->property_id;
        $is_pension_set = $request->is_pension_set;
        $detail = PropertyAssessmentDetail::where('id', '=', $request->assessment_id)->firstOrFail();
        $detail->pensioner_discount = $is_pension_set;
        $detail->save();
        

        return $this->success([
            "detail" => $detail
            // "path" => $destinationPath
        ]);
        
    }

    public function updatePropertyAssessmentDisabilityDiscount(Request $request)
    {
        $property_id = $request->property_id;
        $is_disability_set = $request->is_disability_set;
        $detail = PropertyAssessmentDetail::where('id', '=', $request->assessment_id)->firstOrFail();
        $detail->disability_discount = $is_disability_set;
        $detail->save();
        

        return $this->success([
            "detail" => $detail
            // "path" => $destinationPath
        ]);
        
    }
    
    
    public function pldcCouncilAdjustment(Request $request)
    {
        $ward = $request->ward;
        $section = $request->section;

        $property = Property::where('ward', $ward)->where('section', $section)->get();

        foreach($property as $p)
        {
            $assessment = $p->assessment()->first();

            $water_percentage = 0;
            $electrical_percentage = 0;
            $waster_precentage = 0;
            $market_percentage = 0;
            $hazardous_percentage = 0;
            $drainage_percentage = 0;
            $informal_settlement_percentage = 0;
            $easy_street_access_percentage = 0;
            $paved_tarred_street_percentage = 0;
            $group_name = '';

            if(is_array($request->adjustment_ids)){
                $adjustmentsArray = $request->adjustment_ids;
                foreach($adjustmentsArray as $id)
                {
                    if($id == 1){
                        $water_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 2){
                        $electrical_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 3){
                        $waster_precentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 4){
                        $market_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 5){
                        $hazardous_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 6){
                        $informal_settlement_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 7){
                        $easy_street_access_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 8){
                        $paved_tarred_street_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }else{
                        $drainage_percentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }
                }
            }

            if(is_array($request->adjustment_ids)){
                $adjustmentPercentage = AdjustmentValue::where('group_name', $request->group_name)->whereIn('adjustment_id', $request->adjustment_ids)->pluck('percentage')->toArray(); 
            }
    
    
            $totalAdjustmentPercent = array_sum($adjustmentPercentage);

            $assessment_data = [
                'water_percentage' => $water_percentage,
                'electricity_percentage' => $electrical_percentage,
                'waste_management_percentage'=> $waster_precentage,
                'market_percentage'=> $market_percentage,
                'hazardous_precentage'=> $hazardous_percentage,
                'drainage_percentage'=> $drainage_percentage,
                'informal_settlement_percentage'=> $informal_settlement_percentage,
                'easy_street_access_percentage'=> $easy_street_access_percentage,
                'paved_tarred_street_percentage'=> $paved_tarred_street_percentage,
                'total_adjustment_percent' => $totalAdjustmentPercent
            ];

            $assessment->fill($assessment_data);
            $assessment->save();
        }
        

        
        return $this->success([
            "stat" => $property[0]->assessment()->first()
        ]);
        
    }
    
    
    public function setPropSanitation(Request $request)
    {
        $ward = $request->ward;
        $section = $request->section;

        $property = Property::where('ward', $ward)->get();

        
        foreach($property as $p)
        {
            $sanitation = 2;
            $assessment = $p->assessment()->first();
            $wall_id = $assessment['property_wall_materials'];

            if($wall_id == "5" || $wall_id == "6" || $wall_id == "7" || $wall_id == "8")
            {
                $sanitation = 2;
            }else if($wall_id == "2")
            {
                $sanitation = 1;
            }

            $assessment_data = [
                'sanitation' => $sanitation,
            ];

            $assessment->fill($assessment_data);
            $assessment->save();
            
        }
        

        
        return $this->success([
            "stat" => $property[0]->assessment()->first()
        ]);

        
    }
    
    public function updateEnumerator(Request $request)
    {
        $from_ward = $request->from_ward;
        $to_ward = $request->to_ward;
        $from_enumerator = $request->from_enumerator;
        $to_enumerator = $request->to_enumerator;
        

        $property = Property::whereBetween('ward', [$from_ward,$to_ward])->where('user_id',$from_enumerator)->get();

        
        foreach($property as $p)
        {
            $p->user_id = $to_enumerator;
            $p->save();
        }
        

        
        return $this->success([
            "stat" => $property[0]
        ]);

    }
    
    
    
    public function deleteProperty(Request $request)
    {
        $property_array = [11, 
        27, 
        28, 
        48, 
        59, 
        102, 
        107, 
        151, 
        170, 
        172, 
        206, 
        208, 
        214, 
        228, 
        233, 
        234, 
        256, 
        271, 
        280, 
        307, 
        332, 
        347, 
        348, 
        375, 
        410, 
        417, 
        437, 
        473, 
        474, 
        497, 
        535, 
        546, 
        562, 
        563, 
        578, 
        633, 
        663, 
        664, 
        670, 
        680, 
        703, 
        710, 
        730, 
        731, 
        746, 
        753, 
        825, 
        856, 
        977, 
        999, 
        1000, 
        1029, 
        1030, 
        1031, 
        1036, 
        1073, 
        1078, 
        1080, 
        1172, 
        1174, 
        1185, 
        1186, 
        1200, 
        1224, 
        1282, 
        1284, 
        1293, 
        1294, 
        1296, 
        1314, 
        1352, 
        1353, 
        1355, 
        1356, 
        1358, 
        1360, 
        1373, 
        1386, 
        1395, 
        1403, 
        1404, 
        1405, 
        1415, 
        1416, 
        1418, 
        1422, 
        1424, 
        1433, 
        1436, 
        1462, 
        1464, 
        1503, 
        1601, 
        1626, 
        1629, 
        1633, 
        1634, 
        1637, 
        1646, 
        1648, 
        1651, 
        1653, 
        1721, 
        1743, 
        1753, 
        1773, 
        1779, 
        1797, 
        1801, 
        1809, 
        1813, 
        1815, 
        1824, 
        1829, 
        1850, 
        1852, 
        1869, 
        1870, 
        1872, 
        1873, 
        1875, 
        1876, 
        1907, 
        1918, 
        1922, 
        1924, 
        1931, 
        1939, 
        1941, 
        1944, 
        1946, 
        1949, 
        1950, 
        1958, 
        1960, 
        1961, 
        1964, 
        1968, 
        1990, 
        2010, 
        2014, 
        2019, 
        2020, 
        2046, 
        2055, 
        2057, 
        2069, 
        2071, 
        2083, 
        2101, 
        2116, 
        2118, 
        2179, 
        2188, 
        2189, 
        2195, 
        2207, 
        2242, 
        2260, 
        2313, 
        2328, 
        2350, 
        2364, 
        2376, 
        2377, 
        2385, 
        2386, 
        2389, 
        2390, 
        2391, 
        2392, 
        2401, 
        2402, 
        2405, 
        2410, 
        2429, 
        2465, 
        2468, 
        2471, 
        2476, 
        2477, 
        2497, 
        2499, 
        2504, 
        2508, 
        2510, 
        2565, 
        2571, 
        2574, 
        2598, 
        2601, 
        2610, 
        2623, 
        2654, 
        2655, 
        2656, 
        2660, 
        2685, 
        2686, 
        2687, 
        2688, 
        2689, 
        2753, 
        2756, 
        2766, 
        2767, 
        2769, 
        2779, 
        2799, 
        2800, 
        2803, 
        2804, 
        2811, 
        2812, 
        2819, 
        2820, 
        2821, 
        2824, 
        2834, 
        2835, 
        2845, 
        2848, 
        2858, 
        2875, 
        2880, 
        2892, 
        2903, 
        2907, 
        2982, 
        2986, 
        2987, 
        2990, 
        3008, 
        3010, 
        3011, 
        3022, 
        3023, 
        3027, 
        3028, 
        3040, 
        3041, 
        3048, 
        3049, 
        3066, 
        3069, 
        3071, 
        3075, 
        3076, 
        3098, 
        3106, 
        3108, 
        3127, 
        3134, 
        3142, 
        3147, 
        3159, 
        3160, 
        3163, 
        3164, 
        3173, 
        3174, 
        3176, 
        3177, 
        3178, 
        3179, 
        3215, 
        3217, 
        3220, 
        3221, 
        3232, 
        3234, 
        3251, 
        3260, 
        3261, 
        3262, 
        3277, 
        3302, 
        3303, 
        3308, 
        3314, 
        3316, 
        3321, 
        3337, 
        3348, 
        3353, 
        3374, 
        3375, 
        3377, 
        3382, 
        3383, 
        3407, 
        3425, 
        3458, 
        3459, 
        3495, 
        3496, 
        3497, 
        3501, 
        3503, 
        3523, 
        3527, 
        3539, 
        3540, 
        3552, 
        3575, 
        3625, 
        3626, 
        3627, 
        3652, 
        3668, 
        3680, 
        3710, 
        3711, 
        3712, 
        3713, 
        3728, 
        3733, 
        3750, 
        3765, 
        3777, 
        3785, 
        3786, 
        3787, 
        3807, 
        3868, 
        3878, 
        3883, 
        3908, 
        3909, 
        3911, 
        3926, 
        3927, 
        3929, 
        3930, 
        3932, 
        3935, 
        4001, 
        4006, 
        4007, 
        4014, 
        4015, 
        4018, 
        4080, 
        4145, 
        4146, 
        4190, 
        4216, 
        4220, 
        4228, 
        4237, 
        4262, 
        4287, 
        4288, 
        4289, 
        4298, 
        4299, 
        4301, 
        4303, 
        4304, 
        4305, 
        4307, 
        4308, 
        4309, 
        4314, 
        4315, 
        4316, 
        4319, 
        4325, 
        4326, 
        4395, 
        4413, 
        4414, 
        4417, 
        4436, 
        4437, 
        4443, 
        4448, 
        4561, 
        4562, 
        4576, 
        4591, 
        4595, 
        4601, 
        4607, 
        4608, 
        4609, 
        4631, 
        4633, 
        4634, 
        4651, 
        4652, 
        4658, 
        4659, 
        4660, 
        4661, 
        4663, 
        4679, 
        4685, 
        4711, 
        4745, 
        4772, 
        4773, 
        4774, 
        4778, 
        4791, 
        4820, 
        4823, 
        4859, 
        4876, 
        4911, 
        4918, 
        4919, 
        4941, 
        4960, 
        5005, 
        5009, 
        5030, 
        5050, 
        5051, 
        5052, 
        5054, 
        5059, 
        5064, 
        5070, 
        5071, 
        5078, 
        5098, 
        5101, 
        5102, 
        5112, 
        5128, 
        5130, 
        5136, 
        5152, 
        5156, 
        5159, 
        5164, 
        5165, 
        5204, 
        5216, 
        5221, 
        5227, 
        5264, 
        5265, 
        5266, 
        5268, 
        5269, 
        5272, 
        5295, 
        5296, 
        5305, 
        5317, 
        5318, 
        5333, 
        5339, 
        5373, 
        5449, 
        5475, 
        5476, 
        5477, 
        5479, 
        5490, 
        5491, 
        5506, 
        5507, 
        5518, 
        5521, 
        5522, 
        5553, 
        5557, 
        5560, 
        5562, 
        5563, 
        5565, 
        5566, 
        5590, 
        5592, 
        5593, 
        5595, 
        5613, 
        5624, 
        5625, 
        5626, 
        5627, 
        5630, 
        5631, 
        5708, 
        5709, 
        5724, 
        5733, 
        5734, 
        5735, 
        5742, 
        5782, 
        5788, 
        5790, 
        5791, 
        5856, 
        5941, 
        5942, 
        5943, 
        5944, 
        5950, 
        5963, 
        5964, 
        5967, 
        5988, 
        6005, 
        6008, 
        6011, 
        6012, 
        6013, 
        6014, 
        6052, 
        6057, 
        6074, 
        6081, 
        6116, 
        6130, 
        6156, 
        6157, 
        6187, 
        6211, 
        6218, 
        6224, 
        6246, 
        6320, 
        6338, 
        6340, 
        6341, 
        6342, 
        6345, 
        6346, 
        6359, 
        6372, 
        6377, 
        6574, 
        6575, 
        6611, 
        6636, 
        6658, 
        6659, 
        6660, 
        6669, 
        6709, 
        6719, 
        6726, 
        6728, 
        6735, 
        6740, 
        6749, 
        6750, 
        6768, 
        6770, 
        6771, 
        6772, 
        6773, 
        6774, 
        6782, 
        6788, 
        6815, 
        6833, 
        6862, 
        6863, 
        6868, 
        6897, 
        6899, 
        6918, 
        6920, 
        6922, 
        6928, 
        6929, 
        6934, 
        6935, 
        6940, 
        6948, 
        6958, 
        6970, 
        6971, 
        6973, 
        7024, 
        7027, 
        7038, 
        7047, 
        7074, 
        7077, 
        7078, 
        7079, 
        7086, 
        7104, 
        7108, 
        7124, 
        7233, 
        7234, 
        7235, 
        7254, 
        7265, 
        7292, 
        7296, 
        7297, 
        7334, 
        7338, 
        7342, 
        7380, 
        7381, 
        7382, 
        7383, 
        7384, 
        7385, 
        7389, 
        7392, 
        7393, 
        7395, 
        7398, 
        7399, 
        7407, 
        7408, 
        7410, 
        7416, 
        7417, 
        7425, 
        7426, 
        7427, 
        7436, 
        7437, 
        7438, 
        7439, 
        7484, 
        7501, 
        7503, 
        7504, 
        7524, 
        7552, 
        7556, 
        7557, 
        7558, 
        7579, 
        7590, 
        7607, 
        7608, 
        7623, 
        7630, 
        7672, 
        7675, 
        7676, 
        7680, 
        7688, 
        7694, 
        7701, 
        7729, 
        7761, 
        7762, 
        7783, 
        7790, 
        7798, 
        7807, 
        7809, 
        7844, 
        7873, 
        7876, 
        7894, 
        7939, 
        7941, 
        7945, 
        7947, 
        7950, 
        7957, 
        7968, 
        7990, 
        7991, 
        8007, 
        8026, 
        8057, 
        8058, 
        8059, 
        8072, 
        8074, 
        8095, 
        8098, 
        8153, 
        8160, 
        8162, 
        8168, 
        8204, 
        8205, 
        8206, 
        8238, 
        8263, 
        8280, 
        8281, 
        8292, 
        8402, 
        8403, 
        8404, 
        8406, 
        8407, 
        8408, 
        8418, 
        8430, 
        8464, 
        8489, 
        8509, 
        8573, 
        8595, 
        8596, 
        8600, 
        8601, 
        8664, 
        8672, 
        8677, 
        8695, 
        8707, 
        8717, 
        8718, 
        8728, 
        8729, 
        8735, 
        8744, 
        8796, 
        8797, 
        8798, 
        8802, 
        8812, 
        8813, 
        8818, 
        8819, 
        8824, 
        8825, 
        8892, 
        8901, 
        8929, 
        8930, 
        8953, 
        8954, 
        8957, 
        8978, 
        9034, 
        9076, 
        9077, 
        9079, 
        9084, 
        9085, 
        9095, 
        9108, 
        9110, 
        9112, 
        9114, 
        9127, 
        9128, 
        9134, 
        9152, 
        9178, 
        9181, 
        9182, 
        9219, 
        9220, 
        9221, 
        9228, 
        9233, 
        9284, 
        9286, 
        9327, 
        9328, 
        9350, 
        9352, 
        9365, 
        9375, 
        9403, 
        9427, 
        9428, 
        9449, 
        9454, 
        9458, 
        9463, 
        9481, 
        9482, 
        9485, 
        9491, 
        9492, 
        9514, 
        9525, 
        9526, 
        9530, 
        9533, 
        9534, 
        9551, 
        9552, 
        9563, 
        9565, 
        9570, 
        9578, 
        9580, 
        9581, 
        9582, 
        9636, 
        9655, 
        9659, 
        9661, 
        9666, 
        9674, 
        9688, 
        9701, 
        9704, 
        9712, 
        9713, 
        9722, 
        9723, 
        9724, 
        9748, 
        9749, 
        9766, 
        9777, 
        9778, 
        9847, 
        9874, 
        9967, 
        9972, 
        10525, 
        10526, 
        10527, 
        10528, 
        10529, 
        10530, 
        10531, 
        10534, 
        17081, 
        17086, 
        17091, 
        17094, 
        17096, 
        17114, 
        17132, 
        17133, 
        17135, 
        17145, 
        17207, 
        17218, 
        17222, 
        17236];
        

        foreach($property_array as $id){
            try
            {
                $property = Property::findOrFail($id);
                $property->landlord()->delete();
                $property->occupancy()->delete();
                //$property->assessments()->delete();
                $property->geoRegistry()->delete();
                $property->categories()->detach();
                $property->occupancies()->delete();
                $property->payments()->delete();
                $property->registryMeters()->delete();
                $property->propertyInaccessible()->detach();
                $property->delete();
            }
            
            catch(ModelNotFoundException $e)
            {
                continue;
            }
        }

        return $this->success([
            "stat" => $property_array
        ]);
    }



    public function xyz(){
          //-----start----------//
        $propertyId=78901;
        $property = Property::find($propertyId);
        //  $mill_rate_group_name ="A";
        // $millrs = MillRate::where('group_name', $mill_rate_group_name)->first();
        // dd($millrs);

                //part 1
                    $CurrentYearAssessmentAmount = 0;
                    $PastPayableDue = 0;
                    $Penalty = 0;
                    $CurrentYearTotalPayment2021 = 0;
                    $CurrentYearTotalDue = 0;
                    $CurrentYearTotalDue2021 = 0;

                    $PastPayableDue2022 = 0;
                    $CurrentYearTotalPayment2022 = 0;
                    $Penalty2022 = 0;
                    $CurrentYearTotalDue2022 = 0;
                    $PastPayableDue = 0;

                 // part -2
                for($i=0; $i<count(@$property->assessmentHistory); $i++){
                  
                    $AssessmentYear = $property->assessmentHistory[$i]->created_at->year;
                    $CurrentYearAssessmentAmount = $property->assessmentHistory[$i]->current_year_assessment_amount;
                    if($PastPayableDue > 0){
                        $Penalty = $PastPayableDue*0.25;
                    }
                    else{
                        $Penalty =0;
                    }
                    
                    $CurrentYearTotalPayment = $property->assessmentHistory[$i]->getCurrentYearTotalPayment();
                    $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                   
                   // if($i==2){
                   // dd($AssessmentYear,$CurrentYearAssessmentAmount,$PastPayableDue,$Penalty,$CurrentYearTotalPayment,$CurrentYearTotalDue,count(@$property->assessmentHistory));
                   // }
                    if($AssessmentYear!="2023"){
                       $PastPayableDue = $CurrentYearTotalDue;
                     }
                }


                // part-3
                 // dd($AssessmentYear,$CurrentYearAssessmentAmount,$PastPayableDue,$Penalty,$CurrentYearTotalPayment,$CurrentYearTotalDue);
                 $arr=[];
                 $arr['AssessmentYear']=$AssessmentYear ;
                 $arr['CurrentYearAssessmentAmount']= number_format($CurrentYearAssessmentAmount,2) ;
                 $arr['PastPayableDue']= number_format($PastPayableDue,2) ;
                 $arr['Penalty']= number_format($Penalty,2) ;
                 $arr['CurrentYearTotalPayment']= number_format($CurrentYearTotalPayment,2) ;
                 $arr['CurrentYearTotalDue']= number_format($CurrentYearTotalDue,2);

                 dd($arr);
    }









    public function get_all_counsil(){
        $allCounsils=CounsilAdjustmentGroupA::all();
        $allCounsilsforLocation=CounsilAdjustmentGroupA::where('type','location')->get();
        $allCounsilsforService=CounsilAdjustmentGroupA::where('type','service')->get();
        $allCounsilsforProperty=CounsilAdjustmentGroupA::where('type','property')->get();
        $allCounsilsforPropertyCharacter=CounsilAdjustmentGroupA::whereNotIn('type',['property','service','location'])->get();

         return $this->success([
            'all_Counsils' => $allCounsils,
            'all_Counsils_for_Location' => $allCounsilsforLocation,
            'all_Counsils_for_Service' => $allCounsilsforService,
            'all_Counsils_for_Property' => $allCounsilsforProperty,
            'all_Counsils_for_Property_Character' => $allCounsilsforPropertyCharacter,
        ]);
    }

    public function getLandLordAssesMent($id){
        // $property = Property::with([
        //     'landlord',
        //     'assessment'
        //     // 'user',
        //     // 'payments',
        //     // 'districts'
        // ])->where('id', $id)->get();
        // $data = Property::find($id);
            try {
                $property = Property::findOrFail($id);

            } catch (\Throwable $th) {
                return response()->json(array(
                    'code'      =>  404,
                    'message'   =>  'No Property Found'
                ), 404);  
            }
        $property->generateAssessments();

        // load sub modals
        $property->load([
            'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
            'payments',
            'landlord'
        ]);

        // dd($property->landlord);
        $Landlord_data['property_id'] = $property->landlord->property_id;
        $Landlord_data['first_name'] = $property->landlord->first_name;
        $Landlord_data['middle_name'] = $property->landlord->middle_name;
        $Landlord_data['sur_name'] = $property->landlord->surname;

        $CurrentYearAssessmentAmount = 0;
        $PastPayableDue = 0;
        $Penalty = 0;
        $CurrentYearTotalPayment2021 = 0;
        $CurrentYearTotalDue = 0;
        $CurrentYearTotalDue2021 = 0;

        $PastPayableDue2022 = 0;
        $CurrentYearTotalPayment2022 = 0;
        $Penalty2022 = 0;
        $CurrentYearTotalDue2022 = 0;
        $PastPayableDue = 0;
        $dataArr = [];
        $property_details = [];
        $Landlord_details = [];
        for($i=0; $i<count($property->assessmentHistory); $i++){
    //    dd($property->assessmentHistory[$i]->getCurrentYearTotalPayment());
        
        $AssessmentYear = $property->assessmentHistory[$i]->created_at->year;
        $CurrentYearAssessmentAmount = $property->assessmentHistory[$i]->current_year_assessment_amount;
        if($PastPayableDue > 0){
            $Penalty = $PastPayableDue*0.25;
        }
        else{
            $Penalty =0;
        }
        $CurrentYearTotalPayment = $property->assessmentHistory[$i]->getCurrentYearTotalPayment();
        $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
       
        if($AssessmentYear == 2024){
            // dd($CurrentYearAssessmentAmount, $PastPayableDue,$Penalty,$CurrentYearTotalDue);
            $data['assessment_year'] = 2024;
            $data['assessment_amount'] = $CurrentYearAssessmentAmount;
            $data['arrear'] = $PastPayableDue;
            $data['penalty'] = $Penalty;
            $data['amount_paid'] = $CurrentYearTotalPayment;
            $data['due'] = $CurrentYearTotalDue;
        }
     
        
            $PastPayableDue = $CurrentYearTotalDue;
        

            // assessment history table
            array_push( $dataArr,$AssessmentYear ?? 0);
            array_push($dataArr, $CurrentYearAssessmentAmount ?? 0);
            array_push($dataArr, $PastPayableDue ?? 0);
            array_push($dataArr, $Penalty ?? 0);
            array_push($dataArr,  $CurrentYearTotalPayment ?? 0);
            array_push($dataArr, $CurrentYearTotalDue ?? 0);

        
            //property images
            if($property->assessment->getAdminImageOneUrl(100,100)){
            $image_one = $property->assessment->getAdminImageOneUrl(100,100);
            }
          
        
            if($property->assessment->getAdminImageTwoUrl(100,100)){
                $image_two = $property->assessment->getAdminImageTwoUrl(100,100);
            }

            //property details
            
            $property_details['street_number'] = $property->street_number;
               
            $property_details['street_name'] =$property->street_name;
                
            $property_details['ward'] =$property->ward;
              
            $property_details['constituency'] =$property->constituency;
                
            $property_details['section'] =$property->section;
               
            $property_details['chiefdom'] =$property->chiefdom;
               
            $property_details['district'] =$property->district;
               
            $property_details['province'] =$property->province;
             
            $property_details['postcode'] =$property->postcode;
                
                    // <h6>Property Inaccessible</h6>
            $property_details['is_property_inaccessible'] = $property->is_property_inaccessible ? 'Yes' : 'No' ;
               
                    // <h6>Property Inaccessible</h6>
            $property_details['is_property_inaccessible_label'] = $property->propertyInaccessible->pluck('label')->implode(', ') ;
                
                    // <h6>Demand Note Delivered</h6>
            $property_details['is_draft_delivered'] = $property->is_draft_delivered ? 'Yes' : 'No' ;
            


                 
                        // <h6>Recipient Name</h6>
                $property_details['delivered_name'] = $property->delivered_name ?: 'Un-specified' ;
            


                        // <h6>Recipient Number</h6>
                $property_details['delivered_number'] = $property->delivered_number ?: 'Un-specified' ;
             


                          // <h6>Recipient Image</h6>

                $property_details['delivered_image'] =$property->getDeliveredImagePath(50,50);
                               
                
            //landlord details
            // dd($property->landlord);
        $Landlord_details['property_id'] = $property->landlord->property_id;
        $Landlord_details['first_name'] = $property->landlord->first_name;
        $Landlord_details['middle_name'] = $property->landlord->middle_name;
        $Landlord_details['sur_name'] = $property->landlord->surname;
        $Landlord_details['sex'] = $property->landlord->sex;
        $Landlord_details['organization_name'] = $property->landlord->organization_name;
        $Landlord_details['organization_type'] = $property->landlord->organization_type;
        $Landlord_details['organization_addresss'] = $property->landlord->organization_addresss;
        $Landlord_details['email'] = $property->landlord->email;
        $Landlord_details['street_number'] = $property->landlord->street_number;
        $Landlord_details['street_name'] = $property->landlord->street_name;
        $Landlord_details['ward'] = $property->landlord->ward;
        $Landlord_details['constituency'] = $property->landlord->constituency;
        $Landlord_details['section'] = $property->landlord->section;
        $Landlord_details['chiefdom'] = $property->landlord->chiefdom;
        $Landlord_details['district'] = $property->landlord->district;
        $Landlord_details['province'] = $property->landlord->province;
        $Landlord_details['postcode'] = $property->landlord->postcode;
        $Landlord_details['mobile_1'] = $property->landlord->mobile_1;
        $Landlord_details['mobile_2'] = $property->landlord->mobile_2;

        //Transactions

        
                                    


        // Initialize an empty result array
        $result = [];

       


       
    }

     // Loop through the assessment history
     $history = $dataArr;
     $count = count($history);
     for ($i = 0; $i < $count; $i += 6) {
         $year = $history[$i];
         $result[] = [
             'assessment_year' => $history[$i],
             'assessment_amount' => $history[$i + 1],
             'arrear' => $history[$i + 2],
             'penalty' => $history[$i + 3],
             'amount_paid' => $history[$i + 4],
             'due' => $history[$i + 5]
         ];
     }

     $Transaction = [];

     foreach($property->payments()->latest()->get() as $payment){

           // Add item to the JSON array
    $Transaction[] = [
        'property_id' => $payment->property_id,
        'id' => $payment->id,
        'name' => $payment->admin->getName(),
        'assessment' => number_format($payment->assessment),
        'penalty' => number_format($payment->penalty),
        'total' => number_format(@$payment->total),
        'balance' => number_format(@$payment->balance < 0 ? 0 : $payment->balance),
        'payment_type' =>ucwords(@$payment->payment_type),
        'cheque_number' => @$payment->cheque_number,
        'payee_name' => @$payment->payee_name,
        'created_at' => \Carbon\Carbon::parse(@$payment->created_at)->format('Y M, d H:i A')
    ];
                         
}

     //response 
        return $this->success([
            'assessment_data' =>$data,
            'landlord_data' =>$Landlord_data,
            'image_one' => $image_one,
            'image_two' => $image_two,
            'property_details' => $property_details,
            'Landlord_details' => $Landlord_details,
            'Transaction' => $Transaction,
            'assessment_history' => $result
            
        ]);

        // dd($data);

    }


public function getAllProperty(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'user_id' => 'required', // 'required' means the user_id field is mandatory
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 422);
        //     // Return an error response with validation errors if validation fails
        // }
        // $property = Property::with([
        //     'landlord',
        //     'assessment',
        //     'geoRegistry',
        //     'user',
        //     'occupancies',
        //     'propertyInaccessible',
        //     'payments',
        //     'districts',
        //     'images',
        //     'assessment',
        // ])->where('user_id', $request->user_id)->paginate(20);

        // return $this->success([
        //     'property' =>$property

        // ]);


        $validator = Validator::make($request->all(), [
            'user_id' => 'required', // 'required' means the user_id field is mandatory
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
            // Return an error response with validation errors if validation fails
        }
        $property = Property::with([
            'landlord',
            'assessment',
            'geoRegistry',
            'user',
            'occupancies',
            'propertyInaccessible',
            'payments',
            'districts',
            'images',
            'assessment',
        ])->where('user_id', $request->user_id)->get();


 // $adjustments = [
            
 //                [ 'id' => '1',
 //                 'name' => 'Water Supply',
 //                 'percentage' => '3',
 //                 'group_name' => '"A"'
 //                ],
 //                [ 'id' => '2',
 //                 'name' => 'Electricity',
 //                 'percentage' => '3',
 //                 'group_name' => '"A"'
 //                ],
 //                [
 //                    'id'=> '3',
 //                    'name'=> 'Waste Management Services/Points/Locations',
 //                    'percentage'=> '5',
 //                    'group_name'=> '"A"'
 //                ],
 //                [
 //                    'id'=> '5',
 //                    'name'=> 'Hazardous Location/Environment',
 //                    'percentage'=> '5',
 //                    'group_name'=> '"A"'
 //                ],
 //                [
 //                    'id'=> '7',
 //                    'name'=> 'Easy Street Access',
 //                    'percentage'=> '5',
 //                    'group_name'=> '"A"'
 //                ]
     
 //             ];
             
 //             $parr = [];
 //             foreach($property as $p)
 //             {
 //                $adjustments = [];
 //                // dd($p->assessment->water_percentage!=0);
 //                // $water_percentage_assessment = $p->assessment->water_percentage;
                

 //                if(!empty($p->assessment->water_percentage) && $p->assessment->water_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '1',
 //                    'name' => 'Water Supply',
 //                    'percentage' => '3',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->electricity_percentage) && $p->assessment->electricity_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '2',
 //                    'name' => 'Electricity',
 //                    'percentage' => '3',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->waste_management_percentage) && $p->assessment->waste_management_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '3',
 //                    'name' => 'Waste Management Services/Points/Locations',
 //                    'percentage' => '5',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->market_percentage) && $p->assessment->market_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '4',
 //                    'name' => 'Market',
 //                    'percentage' => '3',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->hazardous_precentage) && $p->assessment->hazardous_precentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '5',
 //                    'name' => 'Hazardous Location/Environment',
 //                    'percentage' => '15',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->informal_settlement_percentage) && $p->assessment->informal_settlement_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '6',
 //                    'name' => 'Informal settlement',
 //                    'percentage' => '21',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->easy_street_access_percentage) && $p->assessment->easy_street_access_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '7',
 //                    'name' => 'Easy Street Access',
 //                    'percentage' => '7',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->paved_tarred_street_percentage) && $p->assessment->paved_tarred_street_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '8',
 //                    'name' => 'Paved/Tarred Road/Street',
 //                    'percentage' => '3',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                if(!empty($p->assessment->drainage_percentage) && $p->assessment->drainage_percentage != 0 )
 //                {
 //                    array_push($adjustments, 
 //                    [ 'id' => '9',
 //                    'name' => 'Drainage',
 //                    'percentage' => '3',
 //                    'group_name' => '"A"'
 //                    ]);
 //                }
 //                // $all_adjustments_under_that_property=PropertyToCounsilGroupA::where('property_id',$p->id)->where('year',$p->assessment->created_at->format('Y'))->pluck('adjustment_id')->toArray();
 //                // if(count($all_adjustments_under_that_property)>0){
 //                //   $all_adjustment=CounsilAdjustmentGroupA::whereIn('id',$all_adjustments_under_that_property)->get();
 //                // }
 //                // else{
 //                //     $all_adjustment=[];
 //                // }
 //                if(isset($p->assessment) && $p->assessment !== null && is_object($p->assessment)) {
 //                    $assessmentYear = $p->assessment->created_at->format('Y');
 //                    $all_adjustments_under_that_property = PropertyToCounsilGroupA::where('property_id', $p->id)
 //                        ->where('year', $assessmentYear)
 //                        ->pluck('adjustment_id')
 //                        ->toArray();
                
 //                    if(count($all_adjustments_under_that_property) > 0) {
 //                        $all_adjustment = CounsilAdjustmentGroupA::whereIn('id', $all_adjustments_under_that_property)->get();
 //                    } else {
 //                        $all_adjustment = [];
 //                    }
 //                } else {
 //                    // Handle the case where $p->assessment is not set or not an object
 //                    // You might want to log this or handle it differently based on your application's logic
 //                    $all_adjustment = [];
 //                }
 //                array_add($p, 'values_adjustment', $all_adjustment);
 //             }
             
             
             $adjustments = [];

        return $this->success([
            'property' => $property,
            'values_adjustment' => $adjustments
        ]);


    }

    public function update_new_property(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->user_id);
            $property = Property::with([
                'landlord',
                'assessment',
                'geoRegistry',
                'user',
                'occupancies',
                'propertyInaccessible',
                'payments',
                'districts',
                'images',
                'assessment',
            ])->findOrFail($request->id);

            $rate = $this->calculateNewRate($request);
            
            $property->fill([
                'assessment_area' => $request->assessment_area,
                'street_number' => $request->property_street_number,
                'street_numbernew' => $request->property_street_numbernew,
                'street_name' => $request->property_street_name,
                'ward' => $request->property_ward,
                'constituency' => $request->property_constituency,
                'section' => $request->property_section,
                'chiefdom' => $request->property_chiefdom,
                'district' => $request->property_district ?: $user->assign_district,
                'province' => $request->property_province,
                'postcode' => $request->property_postcode,
                'organization_addresss' => $request->organization_address ?: null,
                'organization_tin' => $request->organization_tin ?: null,
                'organization_type' => $request->organization_type ?: null,
                'organization_name' => $request->organization_name ?: null,
                'is_organization' => $request->input('is_organization', false),
                'is_completed' => $request->input('is_completed', false),
                'is_property_inaccessible' => $request->input('is_property_inaccessible', false),
                'is_draft_delivered' => $request->input('is_draft_delivered', false),
                'delivered_name' => $request->input('delivered_name'),
                'delivered_number' => $request->input('delivered_number'),
                'random_id' => $request->input('random_id'),
            ]);

            if ($request->hasFile('delivered_image')) {
                $recipient_photo = $request->file('delivered_image')->store(Property::DELIVERED_IMAGE);
                $property->delivered_image = $recipient_photo;
            }

            $property->save();
            $property->propertyInaccessible()->sync($request->property_inaccessible);

            $landlord = $property->landlord()->firstOrNew([]);
            if ($request->hasFile('landlord_image')) {
                if ($landlord->hasImage()) {
                    unlink($landlord->getImage());
                }
                $landlord->image = $request->file('landlord_image')->store(Property::ASSESSMENT_IMAGE);
            }

            $landlord->fill([
                'ownerTitle' => $request->landlord_ownerTitle_id,
                'first_name' => $request->landlord_first_name,
                'middle_name' => $request->landlord_middle_name,
                'surname' => $request->landlord_surname,
                'sex' => $request->landlord_sex,
                'street_number' => $request->landlord_street_number,
                'street_numbernew' => $request->landlord_street_numbernew,
                'street_name' => $request->landlord_street_name,
                'email' => $request->landlord_email,
                'id_number' => $request->landlord_id_number,
                'id_type' => $request->landlord_id_type,
                'tin' => $request->landlord_tin,
                'ward' => $request->landlord_ward,
                'constituency' => $request->landlord_constituency,
                'section' => $request->landlord_section,
                'chiefdom' => $request->landlord_chiefdom,
                'district' => $request->landlord_district,
                'province' => $request->landlord_province,
                'postcode' => $request->landlord_postcode,
                'mobile_1' => $request->landlord_mobile_1,
                'mobile_2' => $request->landlord_mobile_2,
            ]);

            $landlord->save();

            $occupancy = $property->occupancy()->firstOrNew([]);
            $occupancy->fill([
                'type' => $request->occupancy_type,
                'ownerTenantTitle' => $request->ownerTenantTitle,
                'tenant_first_name' => $request->occupancy_tenant_first_name,
                'middle_name' => $request->occupancy_middle_name,
                'surname' => $request->occupancy_surname,
                'mobile_1' => $request->occupancy_mobile_1,
                'mobile_2' => $request->occupancy_mobile_2,
            ]);
            $occupancy->save();

            if ($request->occupancy_type && count(array_filter($request->occupancy_type))) {
                foreach (array_filter($request->occupancy_type) as $type) {
                    $property->occupancies()->firstOrCreate(['occupancy_type' => $type]);
                }
                $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
            }

            $assessment = $property->assessment()->firstOrNew([]);
            $assessment->fill([
                'property_wall_materials' => $request->assessment_wall_materials_id,
                'roofs_materials' => $request->assessment_roofs_materials_id,
                'property_window_type' => $request->assessment_window_type_id,
                'property_dimension' => $request->assessment_dimension_id,
                'length' => $request->assessment_length,
                'breadth' => $request->assessment_breadth,
                'square_meter' => $request->assessment_square_meter,
                'property_rate_without_gst' => $request->assessmentRateWithoutGST > 0 ? $request->assessmentRateWithoutGST : $rate['rateWithoutGST'],
                'property_gst' => $request->assessmentRateWithGST > 0 ? $request->assessmentRateWithGST : $rate['GST'],
                'property_rate_with_gst' => $rate['rateWithGST'],
                'property_use' => $request->assessment_use_id,
                'zone' => $request->assessment_zone_id,
                'no_of_mast' => $request->total_mast,
                'no_of_shop' => $request->total_shops,
                'no_of_compound_house' => $request->total_compound_house,
                'compound_name' => $request->compound_name,
                'gated_community' => $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : null,
                'total_adjustment_percent' => array_sum($request->adjustment_ids ? AdjustmentValue::whereIn('id', $request->adjustment_ids)->pluck('percentage')->toArray() : []),
                'group_name' => $request->group_name,
                'mill_rate' => MillRate::where('group_name', $request->group_name)->value('rate') ?? 2.25,
                'water_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 1)->value('percentage') ?? 0,
                'electricity_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 2)->value('percentage') ?? 0,
                'waste_management_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 3)->value('percentage') ?? 0,
                'market_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 4)->value('percentage') ?? 0,
                'hazardous_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 5)->value('percentage') ?? 0,
                'drainage_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 6)->value('percentage') ?? 0,
                'informal_settlement_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 7)->value('percentage') ?? 0,
                'others_percentage' => AdjustmentValue::where('group_name', $request->group_name)->where('adjustment_id', 8)->value('percentage') ?? 0,
            ]);
        // Assuming $assessment_images is defined somewhere before this code block

// Check if assessment image 1 is uploaded
if ($request->hasFile('assessment_images_1')) {
    
    // If an existing image is present, delete it
    // if ($assessment_images->hasImageOne()) {
    //     unlink($assessment_images->getImageOne());
    // }
    // Store the new image and update assessment_data
    $assessment_data['assessment_images_1'] = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
}

// Check if assessment image 2 is uploaded
if ($request->hasFile('assessment_images_2')) {
    // If an existing image is present, delete it
    // if ($assessment_images->hasImageTwo()) {
    //     unlink($assessment_images->getImageTwo());
    // }
    // Store the new image and update assessment_data
    $assessment_data['assessment_images_2'] = $request->assessment_images_2->store(Property::ASSESSMENT_IMAGE);
}

            $assessment->fill($assessment_data);
            // return $assessment;
            $assessment->save();
            $geoData = [
                'point1' => $request->registry_point1,
                'point2' => $request->registry_point2,
                'point3' => $request->registry_point3,
                'point4' => $request->registry_point4,
                'point5' => $request->registry_point5,
                'point6' => $request->registry_point6,
                'point7' => $request->registry_point7,
                'point8' => $request->registry_point8,
                'digital_address' => $request->registry_digital_address,
                'dor_lat_long' => str_replace(',', ', ', $request->dor_lat_long),
            ];
    
            if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
                list($lat, $lng) = explode(',', $request->dor_lat_long);
                $geoData['open_location_code'] = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
            }
    
           // !$geoData['digital_address'] || $geoData = $this->addIdToDigitalAddress($geoData, $property);
    
            $geoRegistry = $property->geoRegistry()->firstOrNew([]);
    
            $geoRegistry->fill($geoData);
            $geoRegistry->save();
    
            /* save and update Registry Image */
            $registryImageId = [];
            $allregistryImage = $property->registryMeters()->pluck('id')->toArray();
            if ($request->registry && count($request->registry) and is_array($request->registry)) {
                foreach (array_filter($request->registry) as $key => $registry) {
                    $image = null;
                    $registryImageId[] = isset($registry['id']) ? (int) $registry['id'] : '';
                    if ($request->hasFile('registry.' . $key . '.meter_image')) {
                        $registryMeters = $property->registryMeters()->where('id', isset($registry['id']) ? (int) $registry['id'] : '')->first();
                        if ($registryMeters && $registryMeters->image != null) {
                            if ($registryMeters->hasImage())
                                unlink($registryMeters->getImage());
                            // $registryMeters->delete();
                        }
                        $image = $registry['meter_image']->store(Property::METER_IMAGE);
                        $property->registryMeters()
                            ->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number'], 'image' => $image]);
                    } else {
                        $property->registryMeters()->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number']]);
                    }
                }
            }
    
            /* delete registry image which not updated*/
    
            $removeImageId = array_diff($allregistryImage, $registryImageId);
            if (count($removeImageId)) {
                foreach ($removeImageId as $diffId) {
                    $registryMetersDelete = $property->registryMeters()->where('id', $diffId)->first();
                    if ($registryMetersDelete && $registryMetersDelete->image != null) {
                        if ($registryMetersDelete->hasImage()) {
                            unlink($registryMetersDelete->getImage());
                        }
    
                        //$registryMetersDelete->delete();
                    }
                    $registryMetersDelete->delete();
                }
            }
            DB::commit();

            return response()->json(['success' => 'Property updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update property: ' . $e->getMessage()], 500);
        }
    }

}

