<?php

namespace App\Http\Controllers\APIV2\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyPayment;
use App\Models\LandlordDetail;
use App\Notifications\PaymentSMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;

/** All Paypal Details class **/

use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use App\Models\PaymentAjdustDetails;
class PaymentController extends Controller
{
    public function searchPropertyDetail(Request $request)
    {
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];
        $assessmentValues = [];

        $landlord = $request->user('landlord-api');


        $property = Property::where('id',$request->property_id)->with([
            'landlord',
            'landlord.titles',
            'occupancy',
            'occupancy.titles',
            'occupancies',
            'assessment.categories',
            'assessment.types',
            'assessment.wallMaterial',
            'assessment.roofMaterial',
            'assessment.valuesAdded',
            'assessment.dimension',
            'assessment.propertyUse',
            'assessment.zone',
            'assessment.swimming',
            'assessment.windowType',
            'assessment.sanitationType',
            'geoRegistry',
            'registryMeters',
            'payments.admin',
            'assessmentHistory'
        ])->get();

        $properties_discount_pensioner_images = [];
        foreach( $property as $pr)
        {
            $propertyId = $pr->id;       
            $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();
            $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();
            $data = [
                'property_id' => $pr->id,
                'pensioner_image_path' => $pensioner_image_path,
                'disability_image_path' => $disability_image_path
            ];

            array_push($properties_discount_pensioner_images, $data);

        }

        return response()->json(compact('property', 'paymentInQuarter', 'history','landlord','properties_discount_pensioner_images'));
    }

    public function show(Request $request)
    {
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];
        $assessmentValues = [];

        $landlord = $request->user('landlord-api');


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
            'assessment.valuesAdded',
            'assessment.dimension',
            'assessment.propertyUse',
            'assessment.zone',
            'assessment.swimming',
            'assessment.windowType',
            'assessment.sanitationType',
            'geoRegistry',
            'registryMeters',
            'payments.admin',
            'assessmentHistory'
        ])->whereHas('landlord', function ($query) use ($landlord) {
            return $query->where('mobile_1', 'like', '%' . $landlord->mobile . '%');
        })->get();

        $properties_discount_pensioner_images = [];
        foreach( $property as $pr)
        {
            $propertyId = $pr->id;       
            $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();
            $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();
            $data = [
                'property_id' => $pr->id,
                'pensioner_image_path' => $pensioner_image_path,
                'disability_image_path' => $disability_image_path
            ];

            array_push($properties_discount_pensioner_images, $data);

        }

        return response()->json(compact('property', 'paymentInQuarter', 'history','landlord','properties_discount_pensioner_images'));
    }

    public function storeLandLord($id, Request $request)
    {
        $property = Property::with('landlord')->findOrFail($id);
        $landlord_data = $property->landlord()->firstOrNew([]);

        $verification_document = null;
        $address_document = null;
        $conveyance_document = null;

        if ($request->hasFile('verification_document')) {
            $verification_document = $request->verification_document->store(LandlordDetail::DOCUMENT_IMAGE);
        }


        if ($request->hasFile('address_document')) {
            $address_document = $request->address_document->store(LandlordDetail::DOCUMENT_IMAGE);
        }

        if ($request->hasFile('conveyance_proof')) {
            $conveyance_document = $request->conveyance_proof->store(LandlordDetail::DOCUMENT_IMAGE);
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
            'conveyance_image' => $conveyance_document,
            'verified' => 0,
            'requested_by' => $request->requested_by
        ]);

        $landlord_data->save();
        return response()->json(['status' => 'success','image'=>$verification_document, 'address'=>$address_document],201);
    }

    public function storeProperty($id, Request $request)
    {
        $property = Property::where('id',$id)->first();
        
        $address_document = null;
        $conveyance_document = null;

        if ($request->hasFile('address_document')) {
            $address_document = $request->address_document->store(Property::DOCUMENT_IMAGE);
        }

        if ($request->hasFile('conveyance_proof')) {
            $conveyance_document = $request->conveyance_proof->store(Property::DOCUMENT_IMAGE);
        }


        
        //$property->temp_street_number = $request->old_street_number;
        $property->temp_street_numbernew = $request->landlord_street_numbernew;
        $property->temp_street_name = $request->landlord_street_name;
        $property->address_image = $address_document;
        $property->conveyance_image = $conveyance_document;
        $property->verified = 0;
        $property->requested_by = $request->requested_by;

        $property->save();
        
        return response()->json(['status' => 'success','image'=>$conveyance_document, 'address'=>$address_document],201);



    }

    public function savePropertyPayment(Request $request){
        // return $request;
        $property = Property::with('landlord')->with(['assessmentHistory'=>function($q)use($request){
        $q->whereYear('created_at',$request->AdjPayingYear);
      }])->findOrFail($request->property_id);
    //    $property['assessmentHistory'][0]['assessment_year'];

    


        $t_amount = intval(str_replace(',', '', $request->PayingAmount));
        $t_penalty = 0;

        $admin = $request->user('admin');

        $data = $request->only([
            'PaymentType',
            'ChequeNo',
            'PayeeName'
        ]);

        $data['assessment'] = number_format($property->assessment->getCurrentYearTotalDue(), 0, '.', '');
        $data['admin_user_id'] = $admin->id ?? 1;
        $data['total'] = $t_amount + $t_penalty;
        $data['amount'] = $t_amount;
        $data['balance'] = $data['assessment']; // For Activity log tracking
        
        //$data['penalty'] = $t_penalty;
        // return $data;

        $payment = $property->payments()->create($data);
        $payment->save();
        // return $payment;

        $paymentAdjustmentDetails = new PaymentAjdustDetails;
        $paymentAdjustmentDetails->property_id = $request->property_id;
        $paymentAdjustmentDetails->payment_id = $payment->id;
        $paymentAdjustmentDetails->paying_amount=$request->PayingAmount;
        $paymentAdjustmentDetails->year=$request->AdjPayingYear;
        $paymentAdjustmentDetails->save();

        return response()->json(['status' => 'Payment save successfull','property_payement'=>$payment,'payement_adjustment_detail'=>$paymentAdjustmentDetails],200);
    }
}

