<?php

namespace App\Http\Controllers\APIV2\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Types\ApiStatusCode;
use App\Http\Controllers\API\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\District;
use App\BusinessReg;
use App\BusinessLicense;
use App\PaymentHistory;
use App\LicenseAmountHistory;
use App\BusinessLicenseCategory;

class BusinessController extends ApiController
{
    public function searchBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'select_option' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }
        $business = BusinessReg::find(request()->input('business_id'));
        if(!$business)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Business not found'
            ]);
        }
        $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();

        $payment_histories = PaymentHistory::where('business_id',$business->id)->where('is_paid',1)->get();
        $license_history_amount = LicenseAmountHistory::where('business_id',$business->id)->get();
        if(count($payment_histories) <= 0)
        {
            $payment_histories = null;
        }
        if(count($license_history_amount) <= 0)
        {
            $license_history_amount = null;
        }
        if($businessLic->BusinessType == "normal")
        {
            $businessLicenseCategory = BusinessLicenseCategory::find($businessLic->BusinessLicenseCategory);
            $license_details = [
                'BusinessType'  => ucwords($businessLic->BusinessType),
                'BusinessCategoryID'  => ($businessLic->BusinessLicenseCategory != 0) ? $businessLicenseCategory->id : null,
                 'BusinessCategory'  => ($businessLic->BusinessCategory != 0) ? $businessLic->category->title : null,
                'BusinessCategoryeName'  => ($businessLic->BusinessLicenseCategory != 0) ? $businessLicenseCategory->name : null,
                'LicenseFee'   => ($businessLic->LicenseFee != 0) ? $businessLic->LicenseFee : 0,
                'PaymentStatus'    =>  $businessLic->payment_status ? strtoupper($businessLic->payment_status) : null,
            ];
        }
        else
        {
            $license_details = null;
        }
        if(!$businessLic)
        {
            $registration_details = [
                'BusinessType'  => null,
                'BusinessCategory'  => null,
                'RegistrationFee'   => null,
                'PaymentStatus'    =>  null,
            ];
        }
        else
        {
            $registration_details = [
                'BusinessType'  => ucwords($businessLic->BusinessType),
                'BusinessCategory'  => ($businessLic->BusinessCategory != 0) ? $businessLic->category->title : null,
                'RegistrationFee'   => ($businessLic->BusinessCategory != 0) ? $businessLic->category->price : null,
                'PaymentStatus'    =>  $businessLic->payment_status ? strtoupper($businessLic->payment_status) : null,
            ];
        }
        // return $this->success([
        //     'business_details'  =>  $business,
        //     'business_license' => !$businessLic ? $businessLic : null,
        //     'payment_histories' => $payment_histories,
        //     'registration_details'  => $registration_details,
        //     'license_details'   =>  $license_details,
        //     'license_history'   => $license_history_amount,
        // ]);
        return $this->success([
            'business_id'  =>  $business->id,
            'registration_details'  => $registration_details,
            'license_details'   =>  $license_details,
        ]);
    }

    public function paymentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'method' => 'required|string',
            'pay_type' => 'required|string',
            'payer_name' => 'required|string',
            'pay_taken_by' => 'required|string',
            'price' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $businessLic = BusinessLicense::where('BusinessRegId',request()->input('business_id'))->first();
        if($request->method == "license")
        {
            if($businessLic->BusinessType == "non_profit" || $businessLic->BusinessType == "")
            {
                return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                    'errors' => 'Business Type is Non Profit.You cant pay license fee'
                ]);
            }
            $method = $request->method;
            if($businessLic->payment_status == "unpaid")
            {
                return redirect()->back()->with('error', 'Kindly Pay your Registration Fee First');
            }
            $paymentHistory = PaymentHistory::where('business_id', $businessLic->BusinessRegId)->where('type','license')->latest()->first();
            if($paymentHistory->due == 0)
            {
                return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                    'errors' => 'License Fee paid of this year'
                ]);
            }
            if($paymentHistory)
            {
                $paymentYear = date('Y',$paymentHistory->dateTime);
                $currentYear = date('Y');

                if($currentYear == $paymentYear AND $paymentHistory->is_paid == 1 AND $paymentHistory->due == 0)
                {
                    return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                        'errors' => 'Already Paid this year payment'
                    ]);
                }
                if($request->price == $businessLic->LicenseFee)
                {
                    $is_paid = 1;
                }
                else
                {
                    $is_paid = 0;
                }
                $dateTime = strtotime($request->date);
                $paymentHistory->type = $method;
                $paymentHistory->business_id = $request->input('business_id');
                $paymentHistory->price = $request->input('price');
                $paymentHistory->pay_type = $request->input('pay_type');
                $paymentHistory->payer_name = $request->input('payer_name');
                $paymentHistory->pay_taken_by   = $request->input('pay_taken_by');
                $paymentHistory->dateTime = $dateTime;
                $paymentHistory->is_paid = 1;


                if($request->price < $paymentHistory->due && $currentYear < $paymentYear)
                {
                    $payment = new PaymentHistory;
                    $payment->type = $method;
                    $payment->business_id = $request->input('business_id');
                    $payment->price = $request->input('price');
                    $payment->pay_type = $request->input('pay_type');
                    $payment->payer_name = $request->input('payer_name');
                    $payment->pay_taken_by   = $request->input('pay_taken_by');
                    $payment->is_paid = $is_paid;
                    $newEndingDate = date('Y-m-d', strtotime('+1 year'));
                    $payment->dateTime = strtotime($newEndingDate);
                    $payment->assessment_amount = $paymentHistory->assessment_amount;
                    $assessment_arrears = $paymentHistory->due - $request->price;
                    $payment->assessment_arrears = $assessment_arrears;
                    $pelanty =  $assessment_arrears;
                    $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
                    $payment->plenty = $pelanty;
                    $payment->amount_paid = 0;
                    $payment->price = $pelanty + $paymentHistory->assessment_amount;
                    $payment->due = $pelanty + $paymentHistory->assessment_amount;

                    $payment->is_paid = 0;
                    $payment->save();
                    $paymentHistory->amount_paid = $request->price;
                    $paymentHistory->due = ($paymentHistory->assessment_amount + $paymentHistory->plenty) - $request->price;
                    $paymentHistory->save();
                }
                else
                {
                    if($paymentHistory->due == 0)
                    {
                        return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                            'errors' => 'Already Paid this year payment'
                        ]);
                    }
                    if($paymentHistory->amount_paid != 0)
                    {
                        // dd("if ander");
                            $price = $request->price;
                            $payment = new PaymentHistory;
                            $payment->type = $method;
                            $payment->business_id = $request->input('business_id');
                            $payment->price = $request->input('price');
                            $payment->pay_type = $request->input('pay_type');
                            $payment->payer_name = $request->input('payer_name');
                            $payment->pay_taken_by   = $request->input('pay_taken_by');
                            $payment->is_paid = 1;
                            $newEndingDate = date('Y-m-d');
                            $payment->dateTime = strtotime($newEndingDate);

                            $assessment_amount = $paymentHistory->assessment_amount;
                            $assessment_arrears = $paymentHistory->due;
                            $amount_paid = $request->price;
                            $payment->assessment_amount = $assessment_amount;
                            $pelanty = 0;
                            $due =$assessment_arrears - $request->price;

                            $payment->assessment_arrears = $assessment_arrears;
                            $payment->plenty = $pelanty;
                            $payment->amount_paid = $amount_paid;
                            $payment->price = $pelanty + $paymentHistory->assessment_amount;
                            $payment->due = $due;

                            $payment->is_paid = 1;
                            $payment->save();

                            $licenseAmountHistory = [
                                'business_id'   => $request->input('business_id'),
                                'dateTime'  => $dateTime,
                                'assessment_amount' =>  $assessment_amount,
                                'assessment_arrears'    => $assessment_arrears,
                                'plenty'    => $pelanty,
                                'amount_paid'   => $request->price,
                                'due'   => $due
                            ];
                            $this->licenseAmountHistory($licenseAmountHistory);
                    }
                    else
                    {
                        $assessment_amount = $paymentHistory->assessment_amount;
                        $assessment_arrears = $paymentHistory->assessment_arrears;
                        $pelanty = $paymentHistory->plenty;
                        $amount_paid = $paymentHistory->amount_paid + $request->price;
                        $due = $paymentHistory->due-$request->price;
                        $licenseAmountHistory = [
                            'business_id'   => $request->input('business_id'),
                            'dateTime'  => $dateTime,
                            'assessment_amount' =>  $assessment_amount,
                            'assessment_arrears'    => $assessment_arrears,
                            'plenty'    => $pelanty,
                            'amount_paid'   => $amount_paid,
                            'due'   => $due
                        ];
                        $this->licenseAmountHistory($licenseAmountHistory);

                        $paymentHistory->amount_paid = $request->price;
                        $paymentHistory->due = $due;
                        $paymentHistory->save();
                    }

                }

                if($request->method == "license")
                {
                    $businessLic->license_payment_status = $request->payment;
                }
                else
                {
                    $businessLic->payment_status = 'paid';
                }
                $businessLic->save();
            }
            else
            {
                $businessLic = BusinessLicense::where('BusinessRegId',$request->input('business_id'))->first();
                $payment = new PaymentHistory;
                $payment->type = $method;
                $payment->business_id = $request->input('business_id');
                $payment->price = $request->input('price');
                $payment->pay_type = $request->input('pay_type');
                $payment->payer_name = $request->input('payer_name');
                $payment->pay_taken_by   = $request->input('pay_taken_by');
                $newEndingDate = date('Y-m-d');
                $payment->dateTime = strtotime($newEndingDate);
                $payment->is_paid = 1;

                $payment->assessment_amount = $businessLic->LicenseFee;
                $assessment_arrears = 0;
                $payment->assessment_arrears = $assessment_arrears;
                $pelanty = 0;
                $payment->plenty = $pelanty;
                $payment->amount_paid = $request->price;
                $payment->price = $pelanty + $request->price;
                $payment->due = $businessLic->LicenseFee - $request->price;

                $payment->is_paid = 1;
                $payment->save();
            }
        }
        else
        {
            if($businessLic->payment_status == "paid")
            {
                return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                    'errors' => 'Registration Fees already Paid'
                ]);
            }
            if($businessLic->category->price != $request->price)
            {
                return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                    'errors' => 'Registration Fees already Paid'
                ]);
            }
            $method = "registration";
            $dateTime = strtotime(date('Y-m-d H:i:s'));
            $is_paid = 1;
            $paymentHistory = new PaymentHistory();
            $paymentHistory->type = $method;
            $paymentHistory->business_id = $request->input('business_id');
            $paymentHistory->price = $request->input('price');
            $paymentHistory->pay_type = $request->input('pay_type');
            $paymentHistory->payer_name = $request->input('payer_name');
            $paymentHistory->pay_taken_by   = $request->input('pay_taken_by');
            $paymentHistory->dateTime = $dateTime;
            $paymentHistory->is_paid = $is_paid;
            $paymentHistory->save();

            if($request->method == "license")
            {
                $businessLic->license_payment_status = $request->payment;
            }
            else
            {
                $businessLic->payment_status = 'paid';
            }
            $businessLic->save();

            if($businessLic->BusinessType == "normal")
            {
                $LicenseFee = $businessLic->LicenseFee;
                $payment_history = new PaymentHistory();
                $payment_history->type = 'license';
                $payment_history->business_id = $request->input('business_id');
                $payment_history->price = $LicenseFee;
                $payment_history->pay_type = $request->input('pay_type');
                $payment_history->payer_name = $request->input('payer_name');
                $payment_history->pay_taken_by   = $request->input('pay_taken_by');
                $payment_history->dateTime = $dateTime;
                $payment_history->is_paid = 0;
                $payment_history->assessment_amount = $LicenseFee;
                $payment_history->assessment_arrears = 0;
                $payment_history->plenty = 0;
                $payment_history->amount_paid = 0;
                $payment_history->due = $LicenseFee;
                $payment_history->save();

                $licenseAmountHistory = [
                    'business_id'   => $request->input('business_id'),
                    'dateTime'  => $dateTime,
                    'assessment_amount' =>  $LicenseFee,
                    'assessment_arrears'    => 0,
                    'plenty'    => 0,
                    'amount_paid'   => 0,
                    'due'   => $LicenseFee
                ];
                $this->licenseAmountHistory($licenseAmountHistory);
            }
        }

        return $this->success([
            'success'   => true,
        ]);
    }

    private function licenseAmountHistory($data)
    {
        $year = date("Y", $data['dateTime']);
        $license_amount_history = LicenseAmountHistory::where('business_id', $data['business_id'])->where('dateTime',$year)->first();
        if(!$license_amount_history)
        {
            $entry_license_history = new LicenseAmountHistory();
            $entry_license_history->business_id = $data['business_id'];
            $entry_license_history->dateTime = $year;
            $entry_license_history->assessment_amount = $data['assessment_amount'];
            $entry_license_history->assessment_arrears = $data['assessment_arrears'];
            $entry_license_history->plenty = $data['plenty'];
            $entry_license_history->amount_paid = $data['amount_paid'];
            $entry_license_history->due = $data['due'];
            $entry_license_history->save();
        }
        else
        {
            $license_amount_history->dateTime = $year;
            $license_amount_history->assessment_amount = $data['assessment_amount'];
            $license_amount_history->assessment_arrears = $data['assessment_arrears'];
            $license_amount_history->plenty = $data['plenty'];
            $license_amount_history->amount_paid = $license_amount_history->amount_paid + $data['amount_paid'];
            $license_amount_history->due = $data['due'];
            $license_amount_history->save();
        }
    }

    public function paymentHistory(Request $request)
    {
        $business = BusinessReg::find(request()->input('business_id'));
        if(!$business)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Business not found'
            ]);
        }
        $payment_histories = PaymentHistory::where('business_id',$business->id)->where('is_paid',1)->get();
        $license_history_amount = LicenseAmountHistory::where('business_id',$business->id)->get();
        if(count($payment_histories) <= 0)
        {
            $payment_histories = null;
        }
        if(count($license_history_amount) <= 0)
        {
            $license_history_amount = null;
        }

        return $this->success([
            'payment_histories' => $payment_histories,
            'license_history'   => $license_history_amount,
        ]);
    }

    public function getBusinessDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }
        $business = BusinessReg::find(request()->input('business_id'));
        if(!$business)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Business not found'
            ]);
        }

        $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
        if(!$businessLic)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Business not found'
            ]);
        }
        $mainProjectACtivitiesExplodeArray = null;

        if($businessLic->BusinessType == "normal")
        {
            $businessResponse['id'] = $business->id;
            $businessResponse['BusinessType'] = 'normal';
            $businessResponse['nameOfOrganization'] = $business->nameOfOrganization;
            $businessResponse['dateOfEstablishment'] = $business->dateOfEstablishment;
            $businessResponse['contactNumber'] = $business->contactNumber;
            $businessResponse['NameOfOtherContactPerson'] = $business->NameOfOtherContactPerson;
            $businessResponse['nameOfProprieter'] = $business->NameOfHeadOrganization;
            $businessResponse['address'] = $business->addressHeadOrg;
            $businessResponse['IsTheOrganization'] = $business->IsTheOrganization;
            $businessResponse['natureOfbusiness'] = $business->natureOfbusiness;
            $businessResponse['companyOwnedBy'] = $business->companyOwnedBy;

            $mainProjectACtivitiesExplode = !empty($business->mainProjectACtivities) ? explode(",",$business->mainProjectACtivities) : null;
            $communityProjectEstablishmentExplode = !empty($business->communityProjectEstablishment) ? explode(",",$business->communityProjectEstablishment) : null;
            $communityDevelopmentActivitiesExplode = !empty($business->communityDevelopmentActivities) ? explode(",",$business->communityDevelopmentActivities) : null;
            
            $businessResponse['mainProjectACtivities'] = $mainProjectACtivitiesExplode;
            $businessResponse['communityProjectEstablishment'] = $communityProjectEstablishmentExplode;
            $businessResponse['communityDevelopmentActivities'] = $communityDevelopmentActivitiesExplode;
            $SignatureImg = null;
            if(!empty($business->Signature))
            {
                $SignatureImg = public_path("business_images/".$business->Signature); 
            }
            $businessResponse['Signature'] = $SignatureImg;
            
            $businessLicCollection = collect($businessLic)->except('id');
            $mergedCollection = $businessLicCollection->merge($businessResponse);
            $mergedData = $mergedCollection->all();
            
            
        }
        else{
            $businessResponse['id'] = $business->id;
            $businessResponse['BusinessType'] = 'non_profit';
            $businessResponse['nameOfOrganization'] = $business->nameOfOrganization;
            $businessResponse['dateOfEstablishment'] = $business->dateOfEstablishment;
            $businessResponse['organizationMainContactAddress'] = $business->organizationMainContactAddress;
            $businessResponse['organizationSubContactAddress'] = $business->organizationSubContactAddress;
            $businessResponse['contactNumber'] = $business->contactNumber;
            $businessResponse['emailIfAny'] = $business->emailIfAny;
            $businessResponse['NameOfHeadOrganization'] = $business->NameOfHeadOrganization;
            $businessResponse['LeadinPioneerAddress'] = $business->addressHeadOrg;
            $businessResponse['NameOfOtherContactPerson'] = $business->NameOfOtherContactPerson;
            $businessResponse['otherContactPersonAddress'] = $business->addressOtherContact;
            $businessResponse['membership_total'] = $business->membership_total;
            $businessResponse['membership_male'] = $business->membership_male;
            $businessResponse['membership_female'] = $business->membership_female;
            $businessResponse['IsTheOrganization'] = $businessLic->category;
            $businessResponse['main_aim_of_organization'] = $business->main_aim_of_organization;
            $businessResponse['nameOfChairmanOrgincation'] = $business->nameOfChairmanOrgincation;
            $businessResponse['AddresOfOrganizationChairman'] = $business->AddresOfOrganizationChairman;
            $businessResponse['phoneOfChairmanOrgincation'] = $business->phoneOfChairmanOrgincation;

            $objectOfOrganizationExplode = !empty($business->objectOfOrganization) ? explode(",",$business->objectOfOrganization) : null;
            $mainProjectACtivitiesExplode = !empty($business->mainProjectACtivities) ? explode(",",$business->mainProjectACtivities) : null;
            $categoryOfTargetBEnefeciaryExplode = !empty($business->categoryOfTargetBEnefeciary) ? explode(",",$business->categoryOfTargetBEnefeciary) : null;
            $communityProjectEstablishmentExplode = !empty($business->communityProjectEstablishment) ? explode(",",$business->communityProjectEstablishment) : null;
            $sourceOfFundingExplode = !empty($business->sourceOfFunding) ? explode(",",$business->sourceOfFunding) : null;

            $businessResponse['objectOfOrganization'] = $objectOfOrganizationExplode;
            $businessResponse['mainProjectACtivities'] = $mainProjectACtivitiesExplode;
            $businessResponse['categoryOfTargetBEnefeciary'] = $categoryOfTargetBEnefeciaryExplode;
            $businessResponse['communityProjectEstablishment'] = $communityProjectEstablishmentExplode;
            $businessResponse['sourceOfFunding'] = $sourceOfFundingExplode;

            $SignatureImg = null;
            $picImg = null;
            if(!empty($business->Signature))
            {
                $SignatureImg = public_path("business_images/".$business->Signature); 
            }
            if(!empty($business->pic))
            {
                $picImg = public_path("business_images/".$business->pic); 
            }
            $businessResponse['Signature'] = $SignatureImg;
            $businessResponse['pic'] = $picImg;

            $mergedData = $businessResponse;
        }

        return $this->success([
            'success'   => true,
            'data' => $mergedData
        ]);
    }

    public function nonProfitUpdateBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
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
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $business = BusinessReg::find(request()->input('business_id'));
        if(!$business)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Business not found'
            ]);
        }

        $BusinessReg = BusinessReg::find($request->business_id);

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
        $BusinessReg->development_officer_status = 0;
        $BusinessReg->cheif_officer_status = 0;
        $BusinessReg->save();

        $BusinessLicense = BusinessLicense::where('BusinessRegId',$BusinessReg->id)->first();
        $LastId = $BusinessReg->id;
        $BusinessLicense->BusinessRegId = $LastId;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->BusinessType = 'non_profit';
        $BusinessLicense->BusinessCategory = $request->IsTheOrganization;
        $BusinessLicense->save();

        return $this->success([
            'success'   => true,
            'message' => 'Non Profit business Updated successfully'
        ]);
    }

    public function profitUpdateBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'contactNumber' => 'required',
            'IsTheOrganization' => 'required',
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
        $business = BusinessReg::find(request()->input('business_id'));
        if(!$business)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Business not found'
            ]);
        }

        $BusinessReg = BusinessReg::find($request->business_id);
        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->contactNumber = $request->contactNumber;
        $BusinessReg->NameOfOtherContactPerson = $request->NameOfOtherContactPerson;
        $BusinessReg->IsTheOrganization = $request->IsTheOrganization;
        $BusinessReg->NameOfHeadOrganization = $request->nameOfheadOrganization;
        $BusinessReg->addressHeadOrg = $request->LeadinPioneerAddress;
        $BusinessReg->natureOfbusiness = $request->natureOfbusiness;
        $BusinessReg->mainProjectACtivities = implode(",",$request->mainProjectActivities);
        $BusinessReg->companyOwnedBy = $request->companyOwnedBy;
        $BusinessReg->communityProjectEstablishment = implode(",",$request->communityProjectEstablishmentOrganization);
        $BusinessReg->communityDevelopmentActivities = implode(",",$request->communityDevelopmentActivities);
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
        $BusinessReg->latitude = $request->latitude;
        $BusinessReg->longitude = $request->longitude;
        $BusinessReg->development_officer_status = 0;
        $BusinessReg->cheif_officer_status = 0;
        $BusinessReg->save();

        $BusinessLicense = BusinessLicense::where('BusinessRegId',$BusinessReg->id)->first();
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
        $BusinessLicense->BusinessType = 'normal';
        $BusinessLicense->BusinessSize = $request->BusinessSize;
        $BusinessLicense->BusinessLocation = $request->BusinessLocation;
        $BusinessLicense->BusinessCategory = $request->BusinessCategory;
        $BusinessLicense->BusinessLicenseCategory = $request->BusinessLicenseCategory;
        $BusinessLicense->LicenseFee = $request->LicenseFee;
        $BusinessLicense->save();

        return $this->success([
            'success'   => true,
            'message' => 'Profit business Updated successfully'
        ]);
    }
}
