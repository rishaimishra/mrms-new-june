<?php

namespace App\Http\Controllers\APIV2\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyPayment;
use App\Models\LandlordDetail;
use App\Notifications\PaymentSMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

    private $propertyId;

    // public function show(Request $request)
    // {
    //     $property = [];
    //     $last_payment = null;
    //     $paymentInQuarter = [];
    //     $history = [];

    //     $this->validate($request, [
    //         'property_id' => 'nullable|required_without:open_location_code',
    //         'open_location_code' => 'nullable|required_without:property_id',
    //     ]);

    //     if ($request->input('open_location_code')) {

    //         $PropertyGeoRegistry = PropertyGeoRegistry::with(['property'])->where('open_location_code', "like", $request->input('open_location_code'))->first();

    //         $propertyId = $PropertyGeoRegistry->property->id;
    //     }
    //     if ($request->input('property_id')) {
    //         $propertyId = $request->input('property_id');
    //     }


    //     $property = Property::with([
    //         'landlord',
    //         'landlord.titles',
    //         'occupancy',
    //         'occupancy.titles',
    //         'occupancies',
    //         'assessment.categories',
    //         'assessment.types',
    //         'assessment.wallMaterial',
    //         'assessment.roofMaterial',
    //         'assessment.windowType',
    //         'assessment.sanitationType',
    //         'assessment.valuesAdded',
    //         'assessment.dimension',
    //         'assessment.propertyUse',
    //         'assessment.zone',
    //         'assessment.swimming',
    //         'geoRegistry',
    //         'registryMeters',
    //         'payments.admin',
    //         'assessmentHistory'
    //     ])->find($propertyId);

    //     if ($property) {
    //         $paymentInQuarter = $property->getPaymentsInQuarter();
    //     }


    //    $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();

    //    $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();
       
       
    //    if($property->assessment->swimming == null)
    //    {    
    //         $property->assessment->swimming_id =  0 ;
    //    }

    //    if($property->assessment->saniation == null)
    //    {    
    //         $property->assessment->sanitation =  0 ;
    //    }
       
       
                

    //     //$property['currentYearAssessmentAmount'] = $property->assessment->getCurrentYearAssessmentAmount();
    //     //$property['arrearDue'] = $property->assessment->getPastPayableDue();
    //     //$property['penalty'] = $property->assessment->getPenalty();
    //     //$property['amountPaid'] = $property->assessment->getCurrentYearTotalPayment();
    //     //$property['balance'] = $property->assessment->getCurrentYearTotalDue();


    //     return response()->json(compact('property', 'paymentInQuarter', 'history','pensioner_image_path','disability_image_path'));
    // }

    public function show(Request $request)
    {
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];

        $this->validate($request, [
            'property_id' => 'nullable|required_without:open_location_code',
            'open_location_code' => 'nullable|required_without:property_id',
        ]);

        if ($request->input('open_location_code')) {

            $PropertyGeoRegistry = PropertyGeoRegistry::with(['property'])->where('open_location_code', "like", $request->input('open_location_code'))->first();

            $propertyId = $PropertyGeoRegistry->property->id;
        }
        if ($request->input('property_id')) {
            $propertyId = $request->input('property_id');
        }


        $property = Property::with([
            'landlord',
            'landlord.titles',
            'occupancy',
            'occupancy.titles',
            'occupancies',
            'assessment.categories',
            'assessment.types',
            'assessment.wallMaterial',
            'assessment.roofMaterial',
            'assessment.windowType',
            'assessment.sanitationType',
            'assessment.valuesAdded',
            'assessment.dimension',
            'assessment.propertyUse',
            'assessment.zone',
            'assessment.swimming',
            'geoRegistry',
            'registryMeters',
            'payments.admin',
            'assessmentHistory'
        ])->find($propertyId);

        if ($property) {
            $paymentInQuarter = $property->getPaymentsInQuarter();
        }


       $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();

       $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();
       
       
       $property->assessment->{"rate_payable"} = number_format($property->assessment->getPropertyTaxPayable(),2,'.','');
       $property->assessment->{"property_net_assessed_vaue"} = number_format($property->assessment->getNetPropertyAssessedValue(),0,'',',');
       
       if($property->assessment->swimming == null)
       {    
            $property->assessment->swimming_id =  0 ;
       }

       if($property->assessment->saniation == null)
       {    
            $property->assessment->sanitation =  0 ;
       }
       
       $pensioner_discount = 0;
       $disability_discount = 0;
       $property_tax_payable = (float)$property->assessment->getPropertyTaxPayable();
       if(($property->assessment->pensioner_discount) && $property->assessment->disability_discount)
       {
            $pensioner_discount = $property_tax_payable * (10/100);
            $disability_discount = $property_tax_payable * (10/100);
       }else if( ($property->assessment->pensioner_discount) && $property->assessment->disability_discount != 1)
       {
            $pensioner_discount = $property_tax_payable * (10/100);
       } else if( ($property->assessment->pensioner_discount) != 1 && $property->assessment->disability_discount)
       {
            $disability_discount = $property_tax_payable * (10/100);
       } else {
            $pensioner_discount = 0;
            $disability_discount = 0;
       }
       
       $property->assessment->{"pensioner_discount"} = number_format($pensioner_discount,2,'.','');
       $property->assessment->{"disability_discount"} = number_format($disability_discount,2,'.','');

       
       $discounted_value = number_format($property->assessment->getPensionerDisabilityDiscountActual());

      // dd($discounted_value);
       
       $property_taxable_value = number_format($property->assessment->geTaxablePropertyValue(),0,'',',');
       
       $council_adjusment_labels = array();
        

                if($property->assessment->water_percentage != 0 )
                {
                    array_push($council_adjusment_labels,'Water Supply');
                    
                }
                if($property->assessment->electricity_percentage != 0 )
                {
                    array_push($council_adjusment_labels,'Electricity');
                    
                }
                if($property->assessment->waste_management_percentage != 0 )
                {
                   
                    array_push($council_adjusment_labels,'Waste Management Services/Points/Locations');
                    
                }
                if($property->assessment->market_percentage != 0 )
                {
                  
                    array_push($council_adjusment_labels,'Market');
                   
                }
                if($property->assessment->hazardous_precentage != 0 )
                {
                
                    array_push($council_adjusment_labels,'Hazardous Location/Environment');
                    
                }
                if($property->assessment->informal_settlement_percentage != 0 )
                {
                    
                    array_push($council_adjusment_labels,'Informal settlement');
                   
                }
                if($property->assessment->easy_street_access_percentage != 0 )
                {
                    
                    array_push($council_adjusment_labels,'Easy Street Access');
                    
                }
                if($property->assessment->paved_tarred_street_percentage != 0 )
                {
                   
                    array_push($council_adjusment_labels,'Paved/Tarred Road/Street');
                    
                }
                if($property->assessment->drainage_percentage != 0 )
                {
                   
                    array_push($council_adjusment_labels,'Drainage');
                   
                }
        
                // $property->assessment->{"council_adjustments_parameters"} = implode(', ',$council_adjusment_labels);




                $property->assessment->{"council_adjustments_parameters"} = round($property->assessment->getCouncilAdjustments());


                  //rate_payable in demand note rate payable 2022
                $property->assessment->{"rate_payable_new"} = round($property->assessment->getPropertyTaxPayable());



                //discounted_value  //
               if($property->assessment->pensioner_discount!="0" || $property->assessment->disability_discount!="0"){
                $property->assessment->{"discounted_value_new"} = round($disability_discount+$pensioner_discount);

                 //$discounted_value;
                 $val2=round($disability_discount+$pensioner_discount);
                }else{
                     $property->assessment->{"discounted_value_new"} = 0; 
                     $val2=0;
                }

              $property->assessment->{"discounted_rate_payable_2022"} =round($property->assessment->getPropertyTaxPayable())- (int)$val2;
               /*round($property->assessment->getPropertyTaxPayable());
*/
                if ($property->assessment->arrear_calc > 0) {
                    $penaltyy = $property->assessment->arrear_due * 0.25;
                }else{
                    $penaltyy = 0;
                }

                 //balance due
                $property->assessment->{"new_balance_due"} = ( $property->assessment->property_rate_without_gst + (int)$property->assessment->arrear_due + (int)$penaltyy)-(int)$property->assessment->amount_paid;
                // dd($property->assessment->getPastPayableDue());
                // dd($property->assessment->getCurrentYearAssessmentAmount(), $property->assessment->arrear_due);
                // $property->assessment->{"new_balance_due"} =round($property->assessment->arrear_calc, 2);


                

        //$property['currentYearAssessmentAmount'] = $property->assessment->getCurrentYearAssessmentAmount();
        //$property['arrearDue'] = $property->assessment->getPastPayableDue();
        //$property['penalty'] = $property->assessment->getPenalty();
        //$property['amountPaid'] = $property->assessment->getCurrentYearTotalPayment();
        //$property['balance'] = $property->assessment->getCurrentYearTotalDue();

        // my code start

        $property->generateAssessments();

        // load sub modals
        $property->load([
            'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
            'payments',
            'landlord'
        ]);

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
       
       
     
           // dd($AssessmentYear, $PastPayableDue);

            // assessment history table
            array_push( $dataArr,$AssessmentYear ?? 0);
            array_push($dataArr, $CurrentYearAssessmentAmount ?? 0);
            array_push($dataArr, $PastPayableDue ?? 0);
            array_push($dataArr, $Penalty ?? 0);
            array_push($dataArr,  $CurrentYearTotalPayment ?? 0);
            array_push($dataArr, $CurrentYearTotalDue ?? 0);
        
            $PastPayableDue = $CurrentYearTotalDue;
        



       
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
        // my code ends


        return response()->json(compact('property', 'paymentInQuarter', 'history','pensioner_image_path','disability_image_path','discounted_value','property_taxable_value','result'));
    }

    public function store($id, Request $request)
    {
        $property = Property::with('landlord')->findOrFail($id);
        $history = [];
        $this->validate($request, [
            'amount' => 'required',
            'penalty' => 'nullable',
            'payment_type' => 'required|in:cash,cheque',
            'cheque_number' => 'nullable|required_if:payment_type,cheque|digits_between:5,10',
            'payee_name' => 'required|max:250'
        ]);

        

        $t_amount = intval(str_replace(',', '', $request->amount));


        $t_penalty = 0;

        $balance = number_format($property->getBalance(), 0, '.', '');


        $admin = $request->user('admin-api');

        $data = $request->only([
            'payment_type',
            'cheque_number',
            'payee_name'
        ]);

        $data['assessment'] = number_format($property->assessment->getCurrentYearTotalDue(), 0, '.', '');
        $data['admin_user_id'] = $admin->id;
        $data['total'] = $t_amount + $t_penalty;
        $data['amount'] = $t_amount;
        $data['payment_made_year'] = $request->payment_made.' '.$request->payment_made_year;
        //$data['penalty'] = $t_penalty;
        $physical_receipt = null;

        if ($request->hasFile('physical_receipt_image')) {
            $physical_receipt = $request->physical_receipt_image->store(PropertyPayment::PHYSICAL_RECEIPT_IMAGE);
            $data['physical_receipt_image'] = $physical_receipt;
        }
        
        
        

        $assessment = $property->assessment()->first();

        
        $pensioner_discount_image = null;

        if ($request->hasFile('pensioner_discount_image')) {
            $pensioner_discount_image = $request->pensioner_discount_image->store(PropertyPayment::PENSIONER_DISCOUNT_IMAGE);
            $data['pensioner_discount_image'] = $pensioner_discount_image;
           
            $assessment_data = [
                'is_rejected_pensioner' => 0,
            ];

            $assessment->fill($assessment_data);
            $assessment->save();
        }

        $disability_discount_image = null;

        if ($request->hasFile('disability_discount_image')) {
            $disability_discount_image = $request->disability_discount_image->store(PropertyPayment::DISABILITY_DISCOUNT_IMAGE);
            $data['disability_discount_image'] = $disability_discount_image;
            
            $assessment_data = [
                'is_rejected_disability' => 0,
            ];

            $assessment->fill($assessment_data);
            $assessment->save();
            
        }


        $payment = $property->payments()->create($data);
        $property2 = Property::with('landlord')->findOrFail($id);
        $t_balance = number_format($property2->assessment->getCurrentYearTotalDue(), 0, '.', '');

        $payment->balance = $t_balance;

        $payment->save();

        if ($mobile_number = $property->landlord->mobile_1) {
            //$property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
            if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                $property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
            }
        }

        $property = Property::with([
            'landlord',
            'occupancy',
            'assessment',
            'geoRegistry',
            'payments',
            'assessmentHistory'
        ])->find($id);

        $paymentInQuarter = $property->getPaymentsInQuarter();
        return response()->json(compact('property', 'paymentInQuarter', 'history'));
    }

    public function storeLandLord($id, Request $request)
    {
        $property = Property::with('landlord')->findOrFail($id);
        $landlord_data = $property->landlord()->firstOrNew([]);

        $verification_document = null;
        $address_document = null;

        if ($request->hasFile('verification_document')) {
            $verification_document = $request->verification_document->store(LandlordDetail::DOCUMENT_IMAGE);
        }


        if ($request->hasFile('address_document')) {
            $address_document = $request->address_document->store(LandlordDetail::DOCUMENT_IMAGE);
        }


        $landlord_data->fill([
            'temp_first_name' => $request->landlord_first_name,
            'temp_middle_name' => $request->landlord_middle_name,
            'temp_surname' => $request->landlord_surname,
            'temp_street_number' => $request->old_street_number,
            'temp_street_numbernew' => $request->landlord_street_number,
            'temp_street_name' => $request->landlord_street_name,
            'temp_email' => $request->landlord_email,
            'temp_mobile_1' => $request->landlord_mobile_1,
            'document_image' => $verification_document,
            'address_image' => $address_document,
            'verified' => 0,
            'requested_by' => $request->requested_by
        ]);

        $landlord_data->save();
        return response()->json(['status' => 'success','image'=>$verification_document, 'address'=>$address_document],201);
    }
}

