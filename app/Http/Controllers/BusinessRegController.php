<?php

namespace App\Http\Controllers;

use App\BusinessReg;
use App\BusinessType;
use App\BusinessLicense;
use PDF;
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

    public function nonProfitBusinessReg()
    {
        $businessTypes = BusinessType::where('type','non_profit')->get();
        return view('non_profit_business_reg')->with(compact('businessTypes'));
    }

    public function profitBusinessReg()
    {
        $businessTypes = BusinessType::where('type','normal')->get();
        $businessLicenseCategory = BusinessLicenseCategory::where('type','normal')->Orderby("name", "asc")->get();
        $BusinessLicenseCategory =[];
        foreach($businessLicenseCategory as $index => $category)
        {
            $BusinessLicenseCategory[$index]['id'] = $category->id;
            $BusinessLicenseCategory[$index]['name'] = $category->name;
            $BusinessLicenseCategory[$index]['price'] = $category->small;
        }
        return view('profit_business_reg')->with(compact('businessTypes','BusinessLicenseCategory'));
    }

    public function nonProfitBusinessRegStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'organContactAddress' => 'required',
            'organSubContactAddress' => 'required',
            'contactNumber' => 'required',
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
            'SourceOfFunding' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return [
                'errors'    => $errors
            ];
        }
        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
        }

        $BusinessReg = new \App\BusinessReg();

        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->organizationMainContactAddress = $request->organContactAddress;
        $BusinessReg->organizationSubContactAddress = $request->organSubContactAddress;
        $BusinessReg->contactNumber = $request->contactNumber;
        // $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $BusinessReg->addressHeadOrg = $request->LeadinPioneerAddress;
        $BusinessReg->NameOfOtherContactPerson = $request->nameOfOtherContPerson;
        $BusinessReg->addressOtherContact = $request->otherContactPersonAddress;
        $BusinessReg->membership_total = $request->membershipTotal;
        $BusinessReg->membership_male = $request->MembershipMale;
        $BusinessReg->membership_female = $request->MemberShipFemale;
        $BusinessReg->main_aim_of_organization = $request->mainAim;
        $BusinessReg->objectOfOrganization = implode(",",$request->ObjectOfOrganization);
        $BusinessReg->mainProjectACtivities = implode(",",$request->mainProjectActivities);
        $BusinessReg->categoryOfTargetBEnefeciary = implode(",",$request->categoryOfTarget);
        $BusinessReg->communityProjectEstablishment = implode(",",$request->communityProjectEstablishmentOrganization);
        $BusinessReg->sourceOfFunding = implode(",",$request->SourceOfFunding);
        $BusinessReg->AddresOfOrganizationChairman = $request->chairmanAddress;
        $BusinessReg->nameOfChairmanOrgincation = $request->chairmanName;
        $BusinessReg->phoneOfChairmanOrgincation = $request->chairmanPhone;
        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            $uploadedImages = [];
            foreach ($images as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = time() . rand(000, 9999) . '.' . $extension;
                $path = public_path('business_images');
                $image->move($path, $filename);
                $uploadedImages[] = $filename;
            }
            if(count($uploadedImages) > 0)
            {
                $BusinessReg->Signature = implode(",",$uploadedImages);
            }
        }
        $BusinessReg->development_officer_status = 0;
        $BusinessReg->cheif_officer_status = 0;
        $BusinessReg->save();

        $BusinessLicense = new \App\BusinessLicense();
        $LastId = $BusinessReg->id;
        $BusinessLicense->BusinessRegId = $LastId;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->BusinessType = $request->BusinessType;
        $BusinessLicense->BusinessCategory = $request->BusinessCategory;
        $BusinessLicense->save();

        // $details = [
        //     'nameOfOrganization' => $request->nameOfOrganization,
        //     'dateofEstablishment' => $request->dateofEstablishment,
        //     'organContactAddress' => $request->organContactAddress,
        //     'organSubContactAddress' => $request->organSubContactAddress,
        //     'contactNumber' => $request->contactNumber,
        //     'oranganisationName' => $request->oranganisationName,
        //     'nameOfheadOrganization' => $request->nameOfheadOrganization,
        //     'LeadinPioneerAddress' => $request->LeadinPioneerAddress,
        //     'nameOfOtherContPerson' => $request->nameOfOtherContPerson,
        //     'otherContactPersonAddress' => $request->otherContactPersonAddress,
        //     'membershipTotal' => $request->membershipTotal,
        //     'MembershipMale' => $request->MembershipMale,
        //     'MemberShipFemale' => $request->MemberShipFemale,
        //     'mainAim' => $request->mainAim,
        //     'ObjectOfOrganization' => $request->ObjectOfOrganization,
        //     'mainProjectActivities' => $request->mainProjectActivities,
        //     'categoryOfTarget' => $request->categoryOfTarget,
        //     'communityProjectEstablishmentOrganization' => $request->communityProjectEstablishmentOrganization,
        //     'OrganizationOperatingDistrict' => $request->OrganizationOperatingDistrict,
        //     'OrganizationIntendDistrict' => $request->OrganizationIntendDistrict,
        //     'SourceOfFunding' => $request->SourceOfFunding,
        //     'nameAddressOrganizationChairman' => $request->nameAddressOrganizationChairman,
        //     'BusinessName' => $request->BusinessName,
        //     'BusinessAccro' => $request->BusinessAccro,
        //     'NameBusinesOwner' => $request->NameBusinesOwner,
        //     'phoneOne' => $request->phoneOne,
        //     'phoneTwo' => $request->phoneTwo,
        //     'emailIfAny' => $request->emailIfAny,
        //     'ownership' => $request->ownership,
        //     'BusinessHousing' => $request->BusinessHousing,
        //     'house' => $request->house,
        //     'Street' => $request->Street,
        //     'Section' => $request->Section,
        //     'Zone' => $request->Zone,
        //     'BusinessType' => $request->BusinessType == "normal" ? "Normal" : "Non Profit",
        //     'BusinessLicenseCategory' => $request->BusinessLicenseCategory,
        //     'BusinessSize' => $request->BusinessSize,
        //     'BusinessLocation' => $request->BusinessLocation,
        // ];
        // $attachment = public_path("business_images/".$BusinessReg->Signature);
        // $attachment2 = public_path("business_images/".$BusinessReg->pic);
        // Mail::send('admin.emails.business_registration', ['details' => $details], function($message) use($request,$filename,$filename2,$attachment,$attachment2){
        //     $message->to($request->emailIfAny);
        //     $message->subject('Thanks for registering a new business');
        //     $message->attach($attachment, ['as' => $filename]);
        //     $message->attach($attachment2, ['as' => $filename2]);
        // });

        return $BusinessReg;
    }

    public function nonProfitBusinessRegUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'organContactAddress' => 'required',
            'organSubContactAddress' => 'required',
            'contactNumber' => 'required',
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
            'SourceOfFunding' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return [
                'errors'    => $errors
            ];
        }

        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
        }

        $BusinessReg = BusinessReg::find($request->BusinessID);

        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->organizationMainContactAddress = $request->organContactAddress;
        $BusinessReg->organizationSubContactAddress = $request->organSubContactAddress;
        $BusinessReg->contactNumber = $request->contactNumber;
        // $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $BusinessReg->addressHeadOrg = $request->LeadinPioneerAddress;
        $BusinessReg->NameOfOtherContactPerson = $request->nameOfOtherContPerson;
        $BusinessReg->addressOtherContact = $request->otherContactPersonAddress;
        $BusinessReg->membership_total = $request->membershipTotal;
        $BusinessReg->membership_male = $request->MembershipMale;
        $BusinessReg->membership_female = $request->MemberShipFemale;
        $BusinessReg->main_aim_of_organization = $request->mainAim;
        $BusinessReg->objectOfOrganization = implode(",",$request->ObjectOfOrganization);
        $BusinessReg->mainProjectACtivities = implode(",",$request->mainProjectActivities);
        $BusinessReg->categoryOfTargetBEnefeciary = implode(",",$request->categoryOfTarget);
        $BusinessReg->communityProjectEstablishment = implode(",",$request->communityProjectEstablishmentOrganization);
        $BusinessReg->sourceOfFunding = implode(",",$request->SourceOfFunding);
        $BusinessReg->AddresOfOrganizationChairman = $request->chairmanAddress;
        $BusinessReg->nameOfChairmanOrgincation = $request->chairmanName;
        $BusinessReg->phoneOfChairmanOrgincation = $request->chairmanPhone;
        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            $uploadedImages = [];
            foreach ($images as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = time() . rand(000, 9999) . '.' . $extension;
                $path = public_path('business_images');
                $image->move($path, $filename);
                $uploadedImages[] = $filename;
            }
            if(count($uploadedImages) > 0)
            {
                $BusinessReg->Signature = implode(",",$uploadedImages);
            }
        }
        $BusinessReg->save();

        $BusinessLicense = BusinessLicense::where('BusinessRegId',$request->BusinessID)->first();
        $LastId = $BusinessReg->id;
        $BusinessLicense->BusinessRegId = $LastId;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->BusinessType = $request->BusinessType;
        $BusinessLicense->BusinessCategory = $request->BusinessCategory;
        $BusinessLicense->save();

        // $details = [
        //     'nameOfOrganization' => $request->nameOfOrganization,
        //     'dateofEstablishment' => $request->dateofEstablishment,
        //     'organContactAddress' => $request->organContactAddress,
        //     'organSubContactAddress' => $request->organSubContactAddress,
        //     'contactNumber' => $request->contactNumber,
        //     'oranganisationName' => $request->oranganisationName,
        //     'nameOfheadOrganization' => $request->nameOfheadOrganization,
        //     'LeadinPioneerAddress' => $request->LeadinPioneerAddress,
        //     'nameOfOtherContPerson' => $request->nameOfOtherContPerson,
        //     'otherContactPersonAddress' => $request->otherContactPersonAddress,
        //     'membershipTotal' => $request->membershipTotal,
        //     'MembershipMale' => $request->MembershipMale,
        //     'MemberShipFemale' => $request->MemberShipFemale,
        //     'mainAim' => $request->mainAim,
        //     'ObjectOfOrganization' => $request->ObjectOfOrganization,
        //     'mainProjectActivities' => $request->mainProjectActivities,
        //     'categoryOfTarget' => $request->categoryOfTarget,
        //     'communityProjectEstablishmentOrganization' => $request->communityProjectEstablishmentOrganization,
        //     'OrganizationOperatingDistrict' => $request->OrganizationOperatingDistrict,
        //     'OrganizationIntendDistrict' => $request->OrganizationIntendDistrict,
        //     'SourceOfFunding' => $request->SourceOfFunding,
        //     'nameAddressOrganizationChairman' => $request->nameAddressOrganizationChairman,
        //     'BusinessName' => $request->BusinessName,
        //     'BusinessAccro' => $request->BusinessAccro,
        //     'NameBusinesOwner' => $request->NameBusinesOwner,
        //     'phoneOne' => $request->phoneOne,
        //     'phoneTwo' => $request->phoneTwo,
        //     'emailIfAny' => $request->emailIfAny,
        //     'ownership' => $request->ownership,
        //     'BusinessHousing' => $request->BusinessHousing,
        //     'house' => $request->house,
        //     'Street' => $request->Street,
        //     'Section' => $request->Section,
        //     'Zone' => $request->Zone,
        //     'BusinessType' => $request->BusinessType == "normal" ? "Normal" : "Non Profit",
        //     'BusinessLicenseCategory' => $request->BusinessLicenseCategory,
        //     'BusinessSize' => $request->BusinessSize,
        //     'BusinessLocation' => $request->BusinessLocation,
        // ];
        // $attachment = public_path("business_images/".$BusinessReg->Signature);
        // $attachment2 = public_path("business_images/".$BusinessReg->pic);
        // Mail::send('admin.emails.business_registration', ['details' => $details], function($message) use($request,$filename,$filename2,$attachment,$attachment2){
        //     $message->to($request->emailIfAny);
        //     $message->subject('Thanks for registering a new business');
        //     $message->attach($attachment, ['as' => $filename]);
        //     $message->attach($attachment2, ['as' => $filename2]);
        // });

        return $BusinessReg;
    }

    public function profitBusinessRegStore(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'contactNumber' => 'required',
            'checkedValue' => 'required',
            'nameOfheadOrganization' => 'required',
            'LeadinPioneerAddress' => 'required',
            'mainProjectActivities' => 'required',
            'communityProjectEstablishmentOrganization' => 'required',
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
        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
        }
        $BusinessReg = new \App\BusinessReg();

        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->contactNumber = $request->contactNumber;
        $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $BusinessReg->addressHeadOrg = $request->LeadinPioneerAddress;
        $BusinessReg->NameOfOtherContactPerson = $request->NameOfOtherContactPerson;
        $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->natureOfbusiness = $request->natureOfbusiness;
        $BusinessReg->mainProjectACtivities = implode(",",$request->mainProjectActivities);
        $BusinessReg->companyOwnedBy = $request->companyOwnedBy;
        $BusinessReg->communityProjectEstablishment = implode(",",$request->communityProjectEstablishmentOrganization);
        $BusinessReg->communityDevelopmentActivities = implode(",",$request->communityDevelopmentActivities);
        $BusinessReg->AddresOfOrganizationChairman = $request->nameAddressOrganizationChairman;
        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            $uploadedImages = [];
            foreach ($images as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = time() . rand(000, 9999) . '.' . $extension;
                $path = public_path('business_images');
                $image->move($path, $filename);
                $uploadedImages[] = $filename;
            }
            if(count($uploadedImages) > 0)
            {
                $BusinessReg->Signature = implode(",",$uploadedImages);
            }
        }
        $BusinessReg->latitude = $request->latitude;
        $BusinessReg->longitude = $request->longitude;
        $BusinessReg->development_officer_status = 0;
        $BusinessReg->cheif_officer_status = 0;
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
            'contactNumber' => $request->contactNumber,
            'oranganisationName' => $request->checkedValue,
            'nameOfheadOrganization' => $request->nameOfheadOrganization,
            'LeadinPioneerAddress' => $request->LeadinPioneerAddress,
            'nameOfOtherContPerson' => $request->nameOfOtherContPerson,
            'mainProjectActivities' => implode(",",$request->mainProjectActivities),
            'natureOfbusiness' => $request->natureOfbusiness,
            'companyOwnedBy' => $request->companyOwnedBy,
            'communityProjectEstablishmentOrganization' => implode(",",$request->communityProjectEstablishmentOrganization),
            'communityDevelopmentActivities' => implode(",",$request->communityDevelopmentActivities),
            'nameAddressOrganizationChairman' => $request->nameAddressOrganizationChairman ? $request->nameAddressOrganizationChairman : '-',
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
        // if(!empty($filename))
        // {
        //     Mail::send('admin.emails._business_registration', ['details' => $details], function($message) use($request,$filename,$attachment){
        //         $message->to($request->emailIfAny);
        //         $message->subject('Thanks for registering a new business');
        //         $message->attach($attachment, ['as' => $filename]);
        //     });
        // }
        // else
        // {
        //     Mail::send('admin.emails.profit_business_registration', ['details' => $details], function($message) use($request){
        //         $message->to($request->emailIfAny);
        //         $message->subject('Thanks for registering a new business');
        //     });
        // }

        return $BusinessReg;
    }

    public function profitBusinessRegUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'contactNumber' => 'required',
            'checkedValue' => 'required',
            'nameOfheadOrganization' => 'required',
            'LeadinPioneerAddress' => 'required',
            'mainProjectActivities' => 'required',
            'communityProjectEstablishmentOrganization' => 'required',
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

        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
        }
        // return $request;
        $BusinessReg = BusinessReg::find($request->BusinessID);
        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->contactNumber = $request->contactNumber;
        $BusinessReg->NameOfOtherContactPerson = $request->NameOfOtherContactPerson;
        $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $BusinessReg->addressHeadOrg = $request->LeadinPioneerAddress;
        $BusinessReg->IsTheOrganization = $request->checkedValue;
        $BusinessReg->natureOfbusiness = $request->natureOfbusiness;
        $BusinessReg->mainProjectACtivities = implode(",",$request->mainProjectActivities);
        $BusinessReg->companyOwnedBy = $request->companyOwnedBy;
        $BusinessReg->communityProjectEstablishment = implode(",",$request->communityProjectEstablishmentOrganization);
        $BusinessReg->communityDevelopmentActivities = implode(",",$request->communityDevelopmentActivities);
        $BusinessReg->AddresOfOrganizationChairman = $request->nameAddressOrganizationChairman;
        if ($request->hasFile('signature')) {
            $images = $request->file('signature');
            $uploadedImages = [];
            foreach ($images as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = time() . rand(000, 9999) . '.' . $extension;
                $path = public_path('business_images');
                $image->move($path, $filename);
                $uploadedImages[] = $filename;
            }
            if(count($uploadedImages) > 0)
            {
                $BusinessReg->Signature = implode(",",$uploadedImages);
            }
        }
        $BusinessReg->latitude = $request->latitude;
        $BusinessReg->longitude = $request->longitude;
        $BusinessReg->save();

        $BusinessLicense = BusinessLicense::where('BusinessRegId',$request->BusinessID)->first();
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
            'contactNumber' => $request->contactNumber,
            'oranganisationName' => $request->checkedValue,
            'nameOfheadOrganization' => $request->nameOfheadOrganization,
            'LeadinPioneerAddress' => $request->LeadinPioneerAddress,
            'nameOfOtherContPerson' => $request->nameOfOtherContPerson,
            'mainProjectActivities' => implode(",",$request->mainProjectActivities),
            'natureOfbusiness' => $request->natureOfbusiness,
            'companyOwnedBy' => $request->companyOwnedBy,
            'communityProjectEstablishmentOrganization' => implode(",",$request->communityProjectEstablishmentOrganization),
            'communityDevelopmentActivities' => implode(",",$request->communityDevelopmentActivities),
            'nameAddressOrganizationChairman' => $request->nameAddressOrganizationChairman ? $request->nameAddressOrganizationChairman : '-',
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
        // if(!empty($filename))
        // {
        //     Mail::send('admin.emails.profit_business_registration', ['details' => $details], function($message) use($request,$filename,$attachment){
        //         $message->to($request->emailIfAny);
        //         $message->subject('Thanks for registering a new business');
        //         $message->attach($attachment, ['as' => $filename]);
        //     });
        // }
        // else
        // {
        //     Mail::send('admin.emails.profit_business_registration', ['details' => $details], function($message) use($request){
        //         $message->to($request->emailIfAny);
        //         $message->subject('Thanks for registering a new business');
        //     });
        // }

        return $BusinessReg;
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

  public function sendBusinessEditFormEmail(Request $request)
  {
    $BusinessReg = BusinessReg::find($request->business_id);
    if(!$BusinessReg)
    {
        return back()->with('error','Business Not Found');
    }
    $businessLic = BusinessLicense::where('BusinessRegId',$BusinessReg->id)->first();
    if(!$businessLic)
    {
        return back()->with('error','Business Not Found');
    }
    if($businessLic->emailIfAny != $request->email)
    {
        return back()->with('error','Email is not valid for this Business');
    }

    if($request->method != "print")
    {
        $encrptedBusinessID = encrypt($BusinessReg->id);
        $email = $businessLic->emailIfAny;
        if($businessLic->BusinessType == "normal")
        {
            $moduleName = implode(",",$request->moduleName);
            $link = route("business_reg.updateProfitBusiness",$encrptedBusinessID);
            $link = $link.'&module='.$moduleName;
        }
        else
        {
            $moduleName = implode(",",$request->moduleName);
            $link = route("business_reg.updateNonProfitBusiness",$encrptedBusinessID);
            $link = $link.'&module='.$moduleName;
        }
        $data = [
            'subject' => 'Update Business Registration',
            'message' => 'Please click the link below to update your business registration information.',
            'link' => $link,
        ];

        Mail::send([], [], function ($message) use ($email, $data) {
            $message->to($email)
                    ->subject('Update Business Registration')
                    ->setBody(view('admin.emails.send_email_business_update', $data)->render(), 'text/html');
        });

        return back()->with('success','Link has been sent to your email.');
    }
    else
    {
        if($BusinessReg->development_officer_status == 0 AND $BusinessReg->cheif_officer_status == 0)
        {
            return back()->with('error','Your Business is not approved yet');
        }

        if($businessLic->BusinessType == "normal")
        {
            $details = [
                'nameOfOrganization' => $BusinessReg->nameOfOrganization,
                'dateofEstablishment' => $BusinessReg->dateofEstablishment,
                'contactNumber' => $BusinessReg->contactNumber,
                'NameOfOtherContactPerson' => $BusinessReg->NameOfOtherContactPerson,
                'IsTheOrganization' => $BusinessReg->IsTheOrganization,
                'nameOfheadOrganization' => $BusinessReg->NameOfHeadOrganization,
                'LeadinPioneerAddress' => $BusinessReg->addressHeadOrg,
                'natureOfbusiness' => $BusinessReg->natureOfbusiness,
                'mainProjectActivities' => $BusinessReg->mainProjectACtivities,
                'companyOwnedBy' => $BusinessReg->companyOwnedBy,
                'communityProjectEstablishmentOrganization' => $BusinessReg->communityProjectEstablishment,
                'communityDevelopmentActivities' => $BusinessReg->communityDevelopmentActivities,
                'AddresOfOrganizationChairman' => $BusinessReg->AddresOfOrganizationChairman,
                'BusinessName' => $businessLic->BusinessName,
                'BusinessAccro' => $businessLic->BusinessAccro,
                'NameBusinesOwner' => $businessLic->NameBusinesOwner,
                'phoneOne' => $businessLic->phoneOne,
                'phoneTwo' => $businessLic->phoneTwo,
                'emailIfAny' => $businessLic->emailIfAny,
                'ownership' => $businessLic->ownership,
                'BusinessHousing' => $businessLic->BusinessHousing,
                'house' => $businessLic->house,
                'Street' => $businessLic->Street,
                'Section' => $businessLic->Section,
                'Zone' => $businessLic->Zone,
                'BusinessType' => $businessLic->BusinessType == "normal" ? "Normal" : "Non Profit",
                'BusinessLicenseCategory' => $businessLic->BusinessLicenseCategory,
                'BusinessSize' => $businessLic->BusinessSize,
                'BusinessLocation' => $businessLic->BusinessLocation,
            ];
            $pdf = PDF::loadView('emails.profit_business_registration', $details);
            $attachment = $pdf->output();
            $filename = 'Document.pdf';
            Mail::send('emails.business-detail', $details, function ($message) use ($businessLic,$attachment,$filename) {
                $message->to($businessLic->emailIfAny);
                $message->subject('Get Business Detail');
                $message->attachData($attachment, $filename);
            });
        }
        else
        {
            $details = [
                'nameOfOrganization' => $BusinessReg->nameOfOrganization,
                'dateofEstablishment' => $BusinessReg->dateofEstablishment,
                'contactNumber' => $BusinessReg->contactNumber,
                'organizationMainContactAddress' => $BusinessReg->organizationMainContactAddress,
                'organizationSubContactAddress' => $BusinessReg->organizationSubContactAddress,
                'NameOfHeadOrganization' => $BusinessReg->NameOfHeadOrganization,
                'LeadinPioneerAddress' => $BusinessReg->addressHeadOrg,
                'NameOfOtherContactPerson' => $BusinessReg->NameOfOtherContactPerson,
                'otherContactPersonAddress' => $BusinessReg->otherContactPersonAddress,
                'membership_total' => $BusinessReg->membership_total,
                'membership_male' => $BusinessReg->membership_male,
                'membership_female' => $BusinessReg->membership_female,
                'IstheOrganization' => $businessLic->BusinessCategory,
                'emailIfAny' => $businessLic->emailIfAny,
                'BusinessType' => $businessLic->BusinessType == "normal" ? "Normal" : "Non Profit",
                'main_aim_of_organization' => $BusinessReg->main_aim_of_organization,
                'objectOfOrganization' => $BusinessReg->objectOfOrganization,
                'mainProjectACtivities' => $BusinessReg->mainProjectACtivities,
                'categoryOfTargetBEnefeciary' => $BusinessReg->categoryOfTargetBEnefeciary,
                'communityProjectEstablishment' => $BusinessReg->communityProjectEstablishment,
                'sourceOfFunding' => $BusinessReg->sourceOfFunding,
                'nameOfChairmanOrgincation' => $BusinessReg->nameOfChairmanOrgincation,
                'AddresOfOrganizationChairman' => $BusinessReg->AddresOfOrganizationChairman,
                'phoneOfChairmanOrgincation' => $BusinessReg->phoneOfChairmanOrgincation,
            ];
            $pdf = PDF::loadView('emails.non_profit_business_registration', $details);
            $attachment = $pdf->output();
            $filename = 'Document.pdf';
            Mail::send('emails.business-detail', $details, function ($message) use ($businessLic,$attachment,$filename) {
                $message->to($businessLic->emailIfAny);
                $message->subject('Get Business Detail');
                $message->attachData($attachment, $filename);
            });
        }

        return back()->with('success','Print has been sent to your email.');
    }

  }

  public function updateProfitBusiness($token)
  {
    $explode = explode("&module=",$token);
    $token = $explode[0];
    $moduleName = explode(",",$explode[1]);

    $business_id = decrypt($token);
    $business = BusinessReg::find($business_id);
    $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
    if($businessLic->BusinessType == "normal")
    {
        $businessTypes = BusinessType::where('type','normal')->get();
        $businessLicenseCategory = BusinessLicenseCategory::where('type','normal')->Orderby("name", "asc")->get();
        $BusinessLicenseCategory =[];
        foreach($businessLicenseCategory as $index => $category)
        {
            $BusinessLicenseCategory[$index]['id'] = $category->id;
            $BusinessLicenseCategory[$index]['name'] = $category->name;
            $BusinessLicenseCategory[$index]['price'] = $category->small;
        }

        $mainProjectACtivities = explode(",",$business->mainProjectACtivities);
        $communityProjectEstablishmentOrganization = explode(",",$business->communityProjectEstablishment);
        $communityDevelopmentActivities = explode(",",$business->communityDevelopmentActivities);

        return view("profit_update_business_reg")->with(compact('business','businessLic','moduleName','mainProjectACtivities','communityProjectEstablishmentOrganization','communityDevelopmentActivities','businessTypes','BusinessLicenseCategory'));
    }
  }

  public function updateNonProfitBusiness($token)
  {
    $explode = explode("&module=",$token);
    $token = $explode[0];
    $moduleName = explode(",",$explode[1]);

    $business_id = decrypt($token);
    $business = BusinessReg::find($business_id);
    $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
    if($businessLic->BusinessType == "non_profit")
    {
        $businessTypes = BusinessType::where('type','non_profit')->get();

        $objectOfOrganization = explode(",",$business->objectOfOrganization);
        $mainProjectACtivities = explode(",",$business->mainProjectACtivities);
        $categoryOfTarget = explode(",",$business->categoryOfTargetBEnefeciary);
        $communityProjectEstablishmentOrganization = explode(",",$business->communityProjectEstablishment);
        $SourceOfFunding = explode(",",$business->sourceOfFunding);

        return view("non_profit_update_business_reg")->with(compact('business','businessLic','businessTypes','SourceOfFunding','communityProjectEstablishmentOrganization','mainProjectACtivities','categoryOfTarget','objectOfOrganization','moduleName'));
    }
  }


}
