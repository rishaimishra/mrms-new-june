<?php

namespace App\Http\Controllers;

use App\BusinessReg;
use App\BusinessType;
use App\BusinessLicense;
use Illuminate\Http\Request;
use App\BusinessLicenseCategory;
use App\Models\BusinessLicenseModel;
use Illuminate\Support\Facades\Mail;
use App\Models\BusinessRegistrationModel;
use Illuminate\Support\Facades\Validator;


class BusinessRegController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('business-reg');
    }

    public function BusinessList(){
        $leagues = BusinessReg::
        // select('league_name')
    join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
    // ->where('countries.country_name', $country)
    ->get();

        return $leagues;
    }

    public function BusinessListById($id){
        $leagues = BusinessReg::where('business_registration.id', $id)
    ->join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
    ->get();

    return $leagues;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'organContactAddress' => 'required',
            'organSubContactAddress' => 'required',
            'contactNumber' => 'required',
            'checkedValue' => 'required',
            'nameOfheadOrganization' => 'required',
            'LeadinPioneerAddress' => 'required',
            'nameOfOtherContPerson' => 'required',
            'otherContactPersonAddress' => 'required',
            'membershipTotal' => 'required',
            'MembershipMale' => 'required',
            'MemberShipFemale' => 'required',
            'mainAim' => 'required',
            'ObjectOfOrganization' => 'required',
            'mainProjectActivities' => 'required',
            'categoryOfTarget' => 'required',
            'communityProjectEstablishmentOrganization' => 'required',
            'OrganizationOperatingDistrict' => 'required',
            'OrganizationIntendDistrict' => 'required',
            'SourceOfFunding' => 'required',
            'nameAddressOrganizationChairman' => 'required',
            'BusinessName' => 'required',
            'BusinessAccro' => 'required',
            'NameBusinesOwner' => 'required',
            'phoneOne' => 'required',
            'phoneTwo' => 'required',
            'emailIfAny' => 'required',
            'ownership' => 'required',
            'BusinessHousing' => 'required',
            'house' => 'required',
            'Street' => 'required',
            'Section' => 'required',
            'Zone' => 'required',
            'BusinessType' => 'required',
            'BusinessSize' => 'required',
            'BusinessLocation' => 'required',
        ],[
            'checkedValue.required' => "Oranganisation Name is Required"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return [
                'errors'    => $errors
            ];
        }

        $BusinessReg = new \App\BusinessReg();

        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->organizationMainContactAddress = $request->organContactAddress;
        $BusinessReg->organizationSubContactAddress = $request->organSubContactAddress;
        $BusinessReg->contactNumber = $request->contactNumber;
        $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $BusinessReg->addressHeadOrg = $request->LeadinPioneerAddress;
        $BusinessReg->NameOfOtherContactPerson = $request->nameOfOtherContPerson;
        $BusinessReg->addressOtherContact = $request->otherContactPersonAddress;
        $BusinessReg->membership_total = $request->membershipTotal;
        $BusinessReg->membership_male = $request->MembershipMale;
        $BusinessReg->membership_female = $request->MemberShipFemale;
        $BusinessReg->main_aim_of_organization = $request->mainAim;
        $BusinessReg->objectOfOrganization = $request->ObjectOfOrganization;
        $BusinessReg->mainProjectACtivities = $request->mainProjectActivities;
        $BusinessReg->categoryOfTargetBEnefeciary = $request->categoryOfTarget;
        $BusinessReg->communityProjectEstablishment = $request->communityProjectEstablishmentOrganization;
        $BusinessReg->LocationHasYourOrganOperating = $request->OrganizationOperatingDistrict;
        $BusinessReg->OrganizationIntendOperating = $request->OrganizationIntendDistrict;
        $BusinessReg->sourceOfFunding = $request->SourceOfFunding;
        $BusinessReg->AddresOfOrganizationChairman = $request->nameAddressOrganizationChairman;
        if ($request->hasFile('Signature')) {
            $district->Signature = $request->Signature->store(District::IMAGE_PATH);
        }
        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');
            $extension = $signature->getClientOriginalExtension();
            $filename = time().rand(000,9999) . '.' . $extension;
            $path = "business_images";
            $signature->move(public_path($path),$filename);
            $BusinessReg->Signature = $filename;
        }
        if ($request->hasFile('pic')) {
            $pic = $request->file('pic');
            $extension = $pic->getClientOriginalExtension();
            $filename2 = time().rand(000,9999) . '.' . $extension;
            $path = "business_images";
            $pic->move(public_path($path),$filename2);
            $BusinessReg->pic = $filename2;
        }
        $BusinessReg->latitude = $request->latitude;
        $BusinessReg->longitude = $request->longitude;
        $BusinessReg->save();

        $BusinessLicense = new \App\BusinessLicense();
        $LastId = $BusinessReg->id;
        $BusinessLicense->BusinessName = $request->BusinessName;
        $BusinessLicense->BusinessRegId = $LastId;
        $BusinessLicense->BusinessAccro = $request->BusinessAccro;
        $BusinessLicense->NameBusinesOwner = $request->NameBusinesOwner;
        $BusinessLicense->phoneOne = $request->phoneOne;
        $BusinessLicense->phoneTwo = $request->phoneTwo;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->ownership = $request->ownership;
        $BusinessLicense->BusinessHousing = $request->BusinessHousing;
        $BusinessLicense->house = $request->house;
        $BusinessLicense->Street = $request->Street;
        $BusinessLicense->Section = $request->Section;
        $BusinessLicense->Zone = $request->Zone;
        $BusinessLicense->BusinessType = $request->BusinessType;
        $BusinessLicense->BusinessClass = '';
        $BusinessLicense->BusinessSize = $request->BusinessSize;
        $BusinessLicense->BusinessLocation = $request->BusinessLocation;
        $BusinessLicense->BusinessCategory = $request->BusinessCategory;
        $BusinessLicense->BusinessLicenseCategory = $request->BusinessLicenseCategory;
        $BusinessLicense->LicenseFee = $request->LicenseFee;
        $BusinessLicense->save();

        $details = [
            'nameOfOrganization' => $request->nameOfOrganization,
            'dateofEstablishment' => $request->dateofEstablishment,
            'organContactAddress' => $request->organContactAddress,
            'organSubContactAddress' => $request->organSubContactAddress,
            'contactNumber' => $request->contactNumber,
            'oranganisationName' => $request->oranganisationName,
            'nameOfheadOrganization' => $request->nameOfheadOrganization,
            'LeadinPioneerAddress' => $request->LeadinPioneerAddress,
            'nameOfOtherContPerson' => $request->nameOfOtherContPerson,
            'otherContactPersonAddress' => $request->otherContactPersonAddress,
            'membershipTotal' => $request->membershipTotal,
            'MembershipMale' => $request->MembershipMale,
            'MemberShipFemale' => $request->MemberShipFemale,
            'mainAim' => $request->mainAim,
            'ObjectOfOrganization' => $request->ObjectOfOrganization,
            'mainProjectActivities' => $request->mainProjectActivities,
            'categoryOfTarget' => $request->categoryOfTarget,
            'communityProjectEstablishmentOrganization' => $request->communityProjectEstablishmentOrganization,
            'OrganizationOperatingDistrict' => $request->OrganizationOperatingDistrict,
            'OrganizationIntendDistrict' => $request->OrganizationIntendDistrict,
            'SourceOfFunding' => $request->SourceOfFunding,
            'nameAddressOrganizationChairman' => $request->nameAddressOrganizationChairman,
            'BusinessName' => $request->BusinessName,
            'BusinessAccro' => $request->BusinessAccro,
            'NameBusinesOwner' => $request->NameBusinesOwner,
            'phoneOne' => $request->phoneOne,
            'phoneTwo' => $request->phoneTwo,
            'emailIfAny' => $request->emailIfAny,
            'ownership' => $request->ownership,
            'BusinessHousing' => $request->BusinessHousing,
            'house' => $request->house,
            'Street' => $request->Street,
            'Section' => $request->Section,
            'Zone' => $request->Zone,
            'BusinessType' => $request->BusinessType == "normal" ? "Normal" : "Non Profit",
            'BusinessLicenseCategory' => $request->BusinessLicenseCategory,
            'BusinessSize' => $request->BusinessSize,
            'BusinessLocation' => $request->BusinessLocation,
        ];
        $attachment = public_path("business_images/".$BusinessReg->Signature);
        $attachment2 = public_path("business_images/".$BusinessReg->pic);
        Mail::send('admin.emails.business_registration', ['details' => $details], function($message) use($request,$filename,$filename2,$attachment,$attachment2){
            $message->to($request->emailIfAny);
            $message->subject('Thanks for registering a new business');
            $message->attach($attachment, ['as' => $filename]);
            $message->attach($attachment2, ['as' => $filename2]);
        });

        return $BusinessReg;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $business = BusinessReg::find($id);
        $business->nameOfOrganization = $request->nameOfOrganization;
        $business->dateOfEstablishment = $request->dateofEstablishment;
        $business->organizationMainContactAddress = $request->organContactAddress;
        $business->organizationSubContactAddress = $request->organSubContactAddress;
        $business->contactNumber = $request->contactNumber;
        $business->IsTheOrganization = $request->checkedValue;
        $business->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $business->addressHeadOrg = $request->LeadinPioneerAddress;
        $business->NameOfOtherContactPerson = $request->nameOfOtherContPerson;
        $business->addressOtherContact = $request->otherContactPersonAddress;
        $business->membership_total = $request->membershipTotal;
        $business->membership_male = $request->MembershipMale;
        $business->membership_female = $request->MemberShipFemale;
        $business->main_aim_of_organization = $request->mainAim;
        $business->objectOfOrganization = $request->ObjectOfOrganization;
        $business->mainProjectACtivities = $request->mainProjectActivities;
        $business->categoryOfTargetBEnefeciary = $request->categoryOfTarget;
        $business->communityProjectEstablishment = $request->communityProjectEstablishmentOrganization;
        $business->LocationHasYourOrganOperating = $request->OrganizationOperatingDistrict;
        $business->OrganizationIntendOperating = $request->OrganizationIntendDistrict;
        $business->sourceOfFunding = $request->SourceOfFunding;
        $business->AddresOfOrganizationChairman = $request->nameAddressOrganizationChairman;
        $business->Signature = $request->Signature;
        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');
            $extension = $signature->getClientOriginalExtension();
            $filename = time().rand(000,9999) . '.' . $extension;
            $path = "business_images";
            $signature->move(public_path($path),$filename);
            $business->Signature = $filename;
        }
        if ($request->hasFile('pic')) {
            $pic = $request->file('pic');
            $extension = $pic->getClientOriginalExtension();
            $filename = time().rand(000,9999) . '.' . $extension;
            $path = "business_images";
            $pic->move(public_path($path),$filename);
            $business->pic = $filename;
        }

        $business->save();

        $BusinessLicense = BusinessLicense::where('BusinessRegId','=',$id)->first();
        $BusinessLicense->BusinessName = $request->BusinessName;
        $BusinessLicense->BusinessRegId = $id;
        $BusinessLicense->BusinessAccro = $request->BusinessAccro;
        $BusinessLicense->NameBusinesOwner = $request->NameBusinesOwner;
        $BusinessLicense->phoneOne = $request->phoneOne;
        $BusinessLicense->phoneTwo = $request->phoneTwo;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->ownership = $request->ownership;
        $BusinessLicense->BusinessHousing = $request->BusinessHousing;
        $BusinessLicense->house = $request->house;
        $BusinessLicense->Street = $request->Street;
        $BusinessLicense->Section = $request->Section;
        $BusinessLicense->Zone = $request->Zone;
        $BusinessLicense->BusinessType = $request->BusinessType;
        $BusinessLicense->BusinessClass = $request->BusinessClass;
        $BusinessLicense->BusinessSize = $request->BusinessSize;
        $BusinessLicense->BusinessLocation = $request->BusinessLocation;
        $BusinessLicense->BusinessLicenseCategory = $request->BusinessLicenseCategory;
        $BusinessLicense->LicenseFee = $request->LicenseFee;
        $BusinessLicense->save();

        return $business;


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }















  public function step1_get(){
    return view('business_reg_page_one');
  }


  public function step1_post(Request $request){
    dd($request->all());
  }

  public function getBusinessType($type)
  {
    $businessType = BusinessType::where('type',$type)->get();
    return $businessType;
  }


  public function getBusinessLicenseCategory($type)
  {
    $query = new BusinessLicenseCategory();
    $businessLicenseCategory = $query->Orderby("name", "asc")->get();
    $array =[];
    foreach($businessLicenseCategory as $index => $category)
    {
        if($type == "small")
        {
            $array[$index]['id'] = $category->id;
            $array[$index]['name'] = $category->name;
            $array[$index]['price'] = $category->small;
        }
        elseif($type == "medium")
        {
            $array[$index]['id'] = $category->id;
            $array[$index]['name'] = $category->name;
            $array[$index]['price'] = $category->medium;
        }
        elseif($type == "large")
        {
            $array[$index]['id'] = $category->id;
            $array[$index]['name'] = $category->name;
            $array[$index]['price'] = $category->large;
        }
    }

    return $array;
  }

  public function getLicenseIDFeeCategory($id)
  {
    $businessLicenseCategory = BusinessLicenseCategory::find($id);
    return [
        'id'    => $businessLicenseCategory->id,
        'name'  =>  $businessLicenseCategory->name,
    ];
  }

  public function getBusinessAllLicenseCategory($type)
  {
    $businessLicenseCategory = BusinessLicenseCategory::where('type',$type)->Orderby("name", "asc")->get();
    $array =[];
    foreach($businessLicenseCategory as $index => $category)
    {
        $array[$index]['id'] = $category->id;
        $array[$index]['name'] = $category->name;
        $array[$index]['price'] = $category->small;
    }
    return $array;
  }

  public function getLicenseFeeCategory($id,$size)
  {
    $businessLicenseCategory = BusinessLicenseCategory::find($id);
    return [
        'id'    => $businessLicenseCategory->id,
        'name'  =>  $businessLicenseCategory->name,
        'price' => $businessLicenseCategory->$size
    ];
  }


}
