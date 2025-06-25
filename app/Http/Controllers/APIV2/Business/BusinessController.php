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
use Illuminate\Support\Facades\Mail;
use PDF;

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
            $license_details_merge = LicenseAmountHistory::where('business_id',$business->id)->latest()->first();

            $license_details_merge_array = json_decode(json_encode($license_details_merge), true);

            $license_details = array_merge($license_details, $license_details_merge_array);
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
        // dd($request->all());
        $businessLic = BusinessLicense::where('BusinessRegId',request()->input('business_id'))->first();
        if($request->method == "license")
        {
            $validator = Validator::make($request->all(), [
                'price' =>  'required|numeric',
                'pay_type' => 'required|string',
                'payer_name' => 'required|string',
                'pay_taken_by' => 'required|string',
            ]);
            if ($validator->fails())
            {
                return redirect()->back()->withErrors($validator->errors());
            }
            $price = $request->price;
            if(!empty($request->discount))
            {
                if($request->discount > getLicenseRemainingAmount($businessLic->BusinessRegId))
                {
                    return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                        'errors' => 'Discount must be less than Price'
                    ]);
                }
                $discount = number_format2Dec($request->discount);
                $price = $price - $discount;
            }
            else
            {
                $discount = 0;
            }
            if($request->plenty == 1)
            {
                $plenty_off = getPlenty($businessLic->BusinessRegId);
            }
            else
            {
                $plenty_off = 0;
            }

            $method = $request->method;
            if($businessLic->payment_status == "unpaid")
            {
                return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                    'errors' => 'Kindly Pay your Registration Fee First'
                ]);
            }
            $paymentHistory = PaymentHistory::where('business_id', $businessLic->BusinessRegId)->where('type','license')->latest()->first();
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
                if($price == $businessLic->LicenseFee)
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


                if($price < $paymentHistory->due && $currentYear < $paymentYear)
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
                    $payment->assessment_amount = number_format2Dec($paymentHistory->assessment_amount);
                    $assessment_arrears = $paymentHistory->due - $price;
                    $payment->assessment_arrears = $assessment_arrears;
                    $pelanty =  $assessment_arrears;
                    // $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
                    $pelanty = ((25/100)*$assessment_arrears);
                    $payment->plenty = $pelanty;
                    $payment->amount_paid = 0;
                    $payment->price = $pelanty + $assessment_arrears + number_format2Dec($paymentHistory->assessment_amount);
                    $payment->due = $pelanty + $assessment_arrears + number_format2Dec($paymentHistory->assessment_amount) - $discount - $plenty_off;
                    $payment->discount = $discount;
                    $payment->is_paid = 0;
                    $payment->save();
                    $paymentHistory->amount_paid = $price;
                    $paymentHistory->due = ($paymentHistory->assessment_amount + $paymentHistory->plenty) - $price;
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
                        // return "if";
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

                            $assessment_amount = number_format2Dec($paymentHistory->assessment_amount);
                            $assessment_arrears = number_format2Dec($paymentHistory->due);
                            $amount_paid = number_format2Dec($price);
                            $payment->assessment_amount = $assessment_amount;
                            $pelanty = 0;
                            $due =$assessment_arrears - $price - $discount - $plenty_off;
                            $due = number_format2Dec($due);
                            if($request->payment_type == "partial")
                            {
                                // return "if";
                                if($discount > 0 AND $plenty_off == 0)
                                {
                                    $amount_paid = number_format2Dec($request->price);
                                    $discount = $paymentHistory->discount + $discount;
                                    $due = $paymentHistory->due - ($request->price + $discount);
                                    $pelanty = $paymentHistory->plenty;
                                }
                                // return $due;
                                // return $request->price;
                                if($plenty_off != 0 AND empty($discount))
                                {
                                    $amount_paid = number_format2Dec($request->price);
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                    $due = $paymentHistory->due - $plenty_off - $request->price;
                                    $pelanty = $paymentHistory->plenty - $plenty_off;
                                }
                                if($plenty_off != 0 AND $discount > 0)
                                {
                                    $amount_paid = $request->price;
                                    $pelanty = $paymentHistory->plenty - $plenty_off;
                                    $due = $paymentHistory->due - $discount - $request->price -$plenty_off;
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                }
                                if($plenty_off == 0 AND empty($discount))
                                {
                                    $due = $paymentHistory->due - $price;
                                    $discount = $paymentHistory->discount + $discount;
                                    $amount_paid = number_format2Dec($request->price);
                                }
                                // return "end";
                            }
                            else
                            {
                                // return "if";
                                if($plenty_off != 0 AND $discount == 0)
                                {
                                    $amount_paid = ($paymentHistory->due - $discount - $plenty_off);
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                    $pelanty = $paymentHistory->plenty - $plenty_off;
                                    $due = 0;
                                }
                                else if($discount > 0 AND $plenty_off == 0)
                                {
                                    // $amount_paid = $paymentHistory->amount_paid;
                                    $amount_paid = number_format2Dec($paymentHistory->due - $discount);
                                    $discount = $paymentHistory->discount + $discount;
                                    $due = (($paymentHistory->due - $discount) + $discount) / $request->price;
                                    if($due == 1)
                                    {
                                        $due = 0;
                                    }
                                }
                                else if($plenty_off != 0 AND $discount > 0)
                                {
                                    $amount_paid = $request->price - ($discount + $plenty_off);
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                    $pelanty = 0;
                                    $due = 0;
                                }
                                else if($plenty_off == 0 AND $discount == 0)
                                {
                                    $due = $paymentHistory->due - $price - $discount;
                                    $discount = $paymentHistory->discount + $discount;
                                    $amount_paid = $price;
                                }else{
                                    return "bilal";
                                }

                            }
                            $amount_paid = number_format2Dec($amount_paid);
                            $due = number_format2Dec($due);

                            $payment->assessment_arrears = $assessment_arrears;
                            $payment->plenty = $pelanty;
                            $payment->amount_paid = $amount_paid;
                            $payment->price = $pelanty + number_format2Dec($paymentHistory->assessment_amount);
                            $payment->due = $due;
                            $payment->discount = number_format2Dec($discount);
                            $payment->is_paid = 1;
                            $payment->save();

                            $licenseAmountHistory = [
                                'business_id'   => $request->input('business_id'),
                                'dateTime'  => $dateTime,
                                'assessment_amount' =>  $assessment_amount,
                                'assessment_arrears'    => $assessment_arrears,
                                'plenty'    => $pelanty,
                                'amount_paid'   => $amount_paid,
                                'discount' => number_format2Dec($discount),
                                'due'   => $due
                            ];
                            $this->licenseAmountHistory($licenseAmountHistory);
                    }
                    else
                    {
                        $assessment_amount = number_format2Dec($paymentHistory->assessment_amount);
                        $assessment_arrears = number_format2Dec($paymentHistory->assessment_arrears);
                        $pelanty = number_format2Dec($paymentHistory->plenty);
                        $amount_paid = $paymentHistory->amount_paid + $price;
                        $amount_paid = number_format2Dec($amount_paid);
                        // $due = ($paymentHistory->assessment_amount + $paymentHistory->plenty) - $price;
                        if($request->payment_type == "partial")
                            {
                                // return "if";
                                if($discount > 0 AND $plenty_off == 0)
                                {
                                    $amount_paid = number_format2Dec($request->price);
                                    $discount = $paymentHistory->discount + $discount;
                                    $due = $paymentHistory->due - ($request->price + $discount);
                                    $pelanty = $paymentHistory->plenty;
                                }
                                // return $due;
                                // return $request->price;
                                if($plenty_off != 0 AND $discount == 0)
                                {
                                    $amount_paid = number_format2Dec($request->price);
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                    $due = $paymentHistory->due - $plenty_off - $request->price;
                                    $pelanty = $paymentHistory->plenty - $plenty_off;
                                }
                                if($plenty_off != 0 AND $discount > 0)
                                {
                                    $amount_paid = $request->price;
                                    $pelanty = $paymentHistory->plenty - $plenty_off;
                                    $due = $paymentHistory->due - $discount - $request->price -$plenty_off;
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                }
                                if($plenty_off == 0 AND $discount == 0)
                                {
                                    $due = $paymentHistory->due - $price;
                                    $discount = $paymentHistory->discount + $discount;
                                    $amount_paid = number_format2Dec($request->price);
                                }
                                // return "end";
                            }
                            else
                            {
                                // return "if";
                                if($plenty_off != 0 AND $discount == 0)
                                {
                                    $amount_paid = ($paymentHistory->due - $discount - $plenty_off);
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                    $pelanty = $paymentHistory->plenty - $plenty_off;
                                    $due = 0;
                                }
                                else if($discount > 0 AND $plenty_off == 0)
                                {
                                    // $amount_paid = $paymentHistory->amount_paid;
                                    $amount_paid = number_format2Dec($paymentHistory->due - $discount);
                                    $discount = $paymentHistory->discount + $discount;
                                    $due = (($paymentHistory->due - $discount) + $discount) / $request->price;
                                    if($due == 1)
                                    {
                                        $due = 0;
                                    }
                                }
                                else if($plenty_off != 0 AND $discount > 0)
                                {
                                    $amount_paid = $request->price - ($discount + $plenty_off);
                                    $discount = $paymentHistory->discount + $discount + $plenty_off;
                                    $pelanty = 0;
                                    $due = 0;
                                }
                                else if($plenty_off == 0 AND $discount == 0)
                                {
                                    $due = $paymentHistory->due - $price - $discount;
                                    $discount = $paymentHistory->discount + $discount;
                                    $amount_paid = $price;
                                }else{
                                    return "bilal";
                                }

                            }

                        $amount_paid = number_format2Dec($amount_paid);
                        $due = number_format2Dec($due);
                        $licenseAmountHistory = [
                            'business_id'   => $request->input('business_id'),
                            'dateTime'  => $dateTime,
                            'assessment_amount' =>  $assessment_amount,
                            'assessment_arrears'    => $assessment_arrears,
                            'plenty'    => $pelanty,
                            'amount_paid'   => $amount_paid,
                            'discount' => $discount,
                            'due'   => $due
                        ];
                        $this->licenseAmountHistory($licenseAmountHistory);

                        $paymentHistory->amount_paid = $amount_paid;
                        $paymentHistory->due = $due;
                        $paymentHistory->discount = $discount;
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

                $payment->assessment_amount = number_format2Dec($businessLic->LicenseFee);
                $assessment_arrears = 0;
                $payment->assessment_arrears = $assessment_arrears;
                $pelanty = 0;
                $payment->plenty = $pelanty;
                $payment->amount_paid = number_format2Dec($price);
                $payment->price = $pelanty + $price;
                $payment->due = number_format2Dec($businessLic->LicenseFee) - number_format2Dec($price);

                $payment->is_paid = 1;
                $payment->save();
            }

            $payment = PaymentHistory::find($paymentHistory->id);
            $business = BusinessReg::find($businessLic->BusinessRegId);

            if ($businessLic->BusinessType == "normal") {
                $businessType = "Normal";
            } elseif ($businessLic->BusinessType == "non_profit") {
                $businessType = "Non Profit";
            } else {
                $businessType = "-";
            }

            $extendedData = [
                "type"  => "payment_id",
                'due'   => $payment->due ? $payment->due : null,
                'dateTime'  => $payment->dateTime ? date("Y",$payment->dateTime) : date("Y"),
                'assessment_amount' => $payment->assessment_amount ? number_format($payment->assessment_amount,2) : 0.00,
                'assessment_arrears'    =>  $payment->assessment_arrears ? number_format($payment->assessment_arrears,2) : 0.00,
                'plenty'    =>  $payment->plenty ? number_format($payment->plenty,2) : 0.00,
                'payer_name'    => $payment->payer_name,
                'pay_type' => $payment->pay_type,
                'pay_taken_by'  => $payment->pay_taken_by,
                "OrganizationName"    =>  $business->nameOfOrganization ? $business->nameOfOrganization : "N/A",
                "Address"   => $business->addressHeadOrg ? $business->addressHeadOrg : "N/A",
                "BusinessCategory" => $businessLic->BusinessCategory ? $businessLic->category->title  : "N/A",
            ];

            $size = $businessLic->BusinessSize;
            $businessLicenseCategory = BusinessLicenseCategory::find($businessLic->BusinessLicenseCategory);
            if($businessLicenseCategory)
            {
                $businessLicenseCategoryName = $businessLicenseCategory->name;
                $businessLicenseCategoryPrice = $businessLicenseCategory->$size;
            }
            else
            {
                $businessLicenseCategoryName = null;
                $businessLicenseCategoryPrice = 0;
            }

            if(!empty($businessLic->BusinessLocation))
            {
                if($businessLic->BusinessLocation == "Within_CBD")
                {
                    $percent = 20;
                    $BusinessLocation = "Within CBD";
                }
                elseif($businessLic->BusinessLocation == "Close_to_CBD")
                {
                    $percent = 10;
                    $BusinessLocation = "Close to CBD";
                }
                elseif($businessLic->BusinessLocation == "Far_from_CBD")
                {
                    $percent = 0;
                    $BusinessLocation = "Far from CBD";
                }
                $businessLicenseCategoryName = $businessLicenseCategory->name;
                $BusinessLocationPrice = $businessLicenseCategoryPrice * ($percent/100);
            }
            $data = [
                "BusinessID"    => $business->id,
                'BusinessName' => $businessLic->BusinessName ? $businessLic->BusinessName : "N/A",
                "BusinessType"  => $businessLic->BusinessType ? ucwords($businessLic->BusinessType) : "N/A",
                "RegistrationDate" => date('d/m/Y',strtotime($business->created_at)),
                "OwnerName" => $businessLic->NameBusinesOwner ? $businessLic->NameBusinesOwner : "N/A",
                "Phone" => $businessLic->phoneOne ? $businessLic->phoneOne : "N/A",
                "Email" => $businessLic->emailIfAny ? $businessLic->emailIfAny : "N/A",
                "BusinessLicenseCategory"  => $businessLicenseCategoryName ? $businessLicenseCategoryName : "N/A",
                "BusinessSize" => $businessLic->BusinessSize ? ucwords($businessLic->BusinessSize) : "N/A",
                "BusinessLocation" => $BusinessLocation ? $BusinessLocation : "N/A",
                "BusinessLocationPrice" => $BusinessLocationPrice ? $BusinessLocationPrice : "0.00",
                "percent" => $percent,
                "BaseRate"  => number_format($businessLicenseCategoryPrice,2),
                "LicenseFee"    =>  number_format($businessLic->LicenseFee,2),
                "AmountPaid"    => !empty($payment->amount_paid) ?$payment->amount_paid: "0.00",
                "PrintDate"     => date('d/m/Y'),
                "percentBaseRate" => number_format($BusinessLocationPrice,2),
                "image1" => $business->Signature,
                "image2" => $business->pic,
            ];
            $newArray = array_merge($data,$extendedData);
            // $pdf = PDF::loadView('admin.users.license_certificate', $newArray);
            // $attachment = $pdf->output();
            // $filename = 'License Demand Notice.pdf';

            // $pdf2 = PDF::loadView('admin.users.license_specific_certificate', $newArray);
            // $attachment2 = $pdf2->output();
            // $filename2 = 'Payment Receipt.pdf';

            // Mail::send('admin.emails.license_certificate', $data, function ($message) use ($businessLic,$attachment,$filename,$attachment2,$filename2) {
            //     $message->to($businessLic->emailIfAny);
            //     $message->subject('Payment');
            //     $message->attachData($attachment, $filename);
            //     $message->attachData($attachment2, $filename2);
            // });

        }
        else
        {
            $validator = Validator::make($request->all(), [
                'pay_type' => 'required|string',
                'payer_name' => 'required|string',
                'pay_taken_by' => 'required|string',
            ]);
            if ($validator->fails())
            {
                return redirect()->back()->withErrors($validator->errors());
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
            $payment_id = $paymentHistory->id;

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
                $LicenseFee = number_format2Dec($businessLic->LicenseFee);
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
                $payment_id = $payment_history->id;

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

            $this->downloadBothCertificate($payment_id,$businessLic->BusinessRegId);

        }

        return $this->success([
            'success'   => true,
            'message' => 'Your payment has been saved.'
        ]);
    }

    // public function paymentStore(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'business_id' => 'required',
    //         'method' => 'required|string',
    //         'pay_type' => 'required|string',
    //         'payer_name' => 'required|string',
    //         'pay_taken_by' => 'required|string',
    //         'price' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //             'errors' => $validator->errors()
    //         ]);
    //     }

    //     $businessLic = BusinessLicense::where('BusinessRegId',request()->input('business_id'))->first();
    //     if($request->method == "license")
    //     {
    //         if($businessLic->BusinessType == "non_profit" || $businessLic->BusinessType == "")
    //         {
    //             return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //                 'errors' => 'Business Type is Non Profit.You cant pay license fee'
    //             ]);
    //         }
    //         $method = $request->method;
    //         if($businessLic->payment_status == "unpaid")
    //         {
    //             return redirect()->back()->with('error', 'Kindly Pay your Registration Fee First');
    //         }
    //         $paymentHistory = PaymentHistory::where('business_id', $businessLic->BusinessRegId)->where('type','license')->latest()->first();
    //         if($paymentHistory->due == 0)
    //         {
    //             return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //                 'errors' => 'License Fee paid of this year'
    //             ]);
    //         }
    //         if($paymentHistory)
    //         {
    //             $paymentYear = date('Y',$paymentHistory->dateTime);
    //             $currentYear = date('Y');

    //             if($currentYear == $paymentYear AND $paymentHistory->is_paid == 1 AND $paymentHistory->due == 0)
    //             {
    //                 return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //                     'errors' => 'Already Paid this year payment'
    //                 ]);
    //             }
    //             if($request->price == $businessLic->LicenseFee)
    //             {
    //                 $is_paid = 1;
    //             }
    //             else
    //             {
    //                 $is_paid = 0;
    //             }
    //             $dateTime = strtotime($request->date);
    //             $paymentHistory->type = $method;
    //             $paymentHistory->business_id = $request->input('business_id');
    //             $paymentHistory->price = $request->input('price');
    //             $paymentHistory->pay_type = $request->input('pay_type');
    //             $paymentHistory->payer_name = $request->input('payer_name');
    //             $paymentHistory->pay_taken_by   = $request->input('pay_taken_by');
    //             $paymentHistory->dateTime = $dateTime;
    //             $paymentHistory->is_paid = 1;


    //             if($request->price < $paymentHistory->due && $currentYear < $paymentYear)
    //             {
    //                 $payment = new PaymentHistory;
    //                 $payment->type = $method;
    //                 $payment->business_id = $request->input('business_id');
    //                 $payment->price = $request->input('price');
    //                 $payment->pay_type = $request->input('pay_type');
    //                 $payment->payer_name = $request->input('payer_name');
    //                 $payment->pay_taken_by   = $request->input('pay_taken_by');
    //                 $payment->is_paid = $is_paid;
    //                 $newEndingDate = date('Y-m-d', strtotime('+1 year'));
    //                 $payment->dateTime = strtotime($newEndingDate);
    //                 $payment->assessment_amount = $paymentHistory->assessment_amount;
    //                 $assessment_arrears = $paymentHistory->due - $request->price;
    //                 $payment->assessment_arrears = $assessment_arrears;
    //                 $pelanty =  $assessment_arrears;
    //                 $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
    //                 $payment->plenty = $pelanty;
    //                 $payment->amount_paid = 0;
    //                 $payment->price = $pelanty + $paymentHistory->assessment_amount;
    //                 $payment->due = $pelanty + $paymentHistory->assessment_amount;

    //                 $payment->is_paid = 0;
    //                 $payment->save();
    //                 $paymentHistory->amount_paid = $request->price;
    //                 $paymentHistory->due = ($paymentHistory->assessment_amount + $paymentHistory->plenty) - $request->price;
    //                 $paymentHistory->save();
    //             }
    //             else
    //             {
    //                 if($paymentHistory->due == 0)
    //                 {
    //                     return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //                         'errors' => 'Already Paid this year payment'
    //                     ]);
    //                 }
    //                 if($paymentHistory->amount_paid != 0)
    //                 {
    //                     // dd("if ander");
    //                         $price = $request->price;
    //                         $payment = new PaymentHistory;
    //                         $payment->type = $method;
    //                         $payment->business_id = $request->input('business_id');
    //                         $payment->price = $request->input('price');
    //                         $payment->pay_type = $request->input('pay_type');
    //                         $payment->payer_name = $request->input('payer_name');
    //                         $payment->pay_taken_by   = $request->input('pay_taken_by');
    //                         $payment->is_paid = 1;
    //                         $newEndingDate = date('Y-m-d');
    //                         $payment->dateTime = strtotime($newEndingDate);

    //                         $assessment_amount = $paymentHistory->assessment_amount;
    //                         $assessment_arrears = $paymentHistory->due;
    //                         $amount_paid = $request->price;
    //                         $payment->assessment_amount = $assessment_amount;
    //                         $pelanty = 0;
    //                         $due =$assessment_arrears - $request->price;

    //                         $payment->assessment_arrears = $assessment_arrears;
    //                         $payment->plenty = $pelanty;
    //                         $payment->amount_paid = $amount_paid;
    //                         $payment->price = $pelanty + $paymentHistory->assessment_amount;
    //                         $payment->due = $due;

    //                         $payment->is_paid = 1;
    //                         $payment->save();

    //                         $licenseAmountHistory = [
    //                             'business_id'   => $request->input('business_id'),
    //                             'dateTime'  => $dateTime,
    //                             'assessment_amount' =>  $assessment_amount,
    //                             'assessment_arrears'    => $assessment_arrears,
    //                             'plenty'    => $pelanty,
    //                             'amount_paid'   => $request->price,
    //                             'due'   => $due
    //                         ];
    //                         $this->licenseAmountHistory($licenseAmountHistory);
    //                 }
    //                 else
    //                 {
    //                     $assessment_amount = $paymentHistory->assessment_amount;
    //                     $assessment_arrears = $paymentHistory->assessment_arrears;
    //                     $pelanty = $paymentHistory->plenty;
    //                     $amount_paid = $paymentHistory->amount_paid + $request->price;
    //                     $due = $paymentHistory->due-$request->price;
    //                     $licenseAmountHistory = [
    //                         'business_id'   => $request->input('business_id'),
    //                         'dateTime'  => $dateTime,
    //                         'assessment_amount' =>  $assessment_amount,
    //                         'assessment_arrears'    => $assessment_arrears,
    //                         'plenty'    => $pelanty,
    //                         'amount_paid'   => $amount_paid,
    //                         'due'   => $due
    //                     ];
    //                     $this->licenseAmountHistory($licenseAmountHistory);

    //                     $paymentHistory->amount_paid = $request->price;
    //                     $paymentHistory->due = $due;
    //                     $paymentHistory->save();
    //                 }

    //             }

    //             if($request->method == "license")
    //             {
    //                 $businessLic->license_payment_status = $request->payment;
    //             }
    //             else
    //             {
    //                 $businessLic->payment_status = 'paid';
    //             }
    //             $businessLic->save();
    //         }
    //         else
    //         {
    //             $businessLic = BusinessLicense::where('BusinessRegId',$request->input('business_id'))->first();
    //             $payment = new PaymentHistory;
    //             $payment->type = $method;
    //             $payment->business_id = $request->input('business_id');
    //             $payment->price = $request->input('price');
    //             $payment->pay_type = $request->input('pay_type');
    //             $payment->payer_name = $request->input('payer_name');
    //             $payment->pay_taken_by   = $request->input('pay_taken_by');
    //             $newEndingDate = date('Y-m-d');
    //             $payment->dateTime = strtotime($newEndingDate);
    //             $payment->is_paid = 1;

    //             $payment->assessment_amount = $businessLic->LicenseFee;
    //             $assessment_arrears = 0;
    //             $payment->assessment_arrears = $assessment_arrears;
    //             $pelanty = 0;
    //             $payment->plenty = $pelanty;
    //             $payment->amount_paid = $request->price;
    //             $payment->price = $pelanty + $request->price;
    //             $payment->due = $businessLic->LicenseFee - $request->price;

    //             $payment->is_paid = 1;
    //             $payment->save();
    //         }
    //     }
    //     else
    //     {
    //         if($businessLic->payment_status == "paid")
    //         {
    //             return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //                 'errors' => 'Registration Fees already Paid'
    //             ]);
    //         }
    //         if($businessLic->category->price != $request->price)
    //         {
    //             return $this->error(ApiStatusCode::VALIDATION_ERROR, [
    //                 'errors' => 'Registration Fees already Paid'
    //             ]);
    //         }
    //         $method = "registration";
    //         $dateTime = strtotime(date('Y-m-d H:i:s'));
    //         $is_paid = 1;
    //         $paymentHistory = new PaymentHistory();
    //         $paymentHistory->type = $method;
    //         $paymentHistory->business_id = $request->input('business_id');
    //         $paymentHistory->price = $request->input('price');
    //         $paymentHistory->pay_type = $request->input('pay_type');
    //         $paymentHistory->payer_name = $request->input('payer_name');
    //         $paymentHistory->pay_taken_by   = $request->input('pay_taken_by');
    //         $paymentHistory->dateTime = $dateTime;
    //         $paymentHistory->is_paid = $is_paid;
    //         $paymentHistory->save();

    //         if($request->method == "license")
    //         {
    //             $businessLic->license_payment_status = $request->payment;
    //         }
    //         else
    //         {
    //             $businessLic->payment_status = 'paid';
    //         }
    //         $businessLic->save();

    //         if($businessLic->BusinessType == "normal")
    //         {
    //             $LicenseFee = $businessLic->LicenseFee;
    //             $payment_history = new PaymentHistory();
    //             $payment_history->type = 'license';
    //             $payment_history->business_id = $request->input('business_id');
    //             $payment_history->price = $LicenseFee;
    //             $payment_history->pay_type = $request->input('pay_type');
    //             $payment_history->payer_name = $request->input('payer_name');
    //             $payment_history->pay_taken_by   = $request->input('pay_taken_by');
    //             $payment_history->dateTime = $dateTime;
    //             $payment_history->is_paid = 0;
    //             $payment_history->assessment_amount = $LicenseFee;
    //             $payment_history->assessment_arrears = 0;
    //             $payment_history->plenty = 0;
    //             $payment_history->amount_paid = 0;
    //             $payment_history->due = $LicenseFee;
    //             $payment_history->save();

    //             $licenseAmountHistory = [
    //                 'business_id'   => $request->input('business_id'),
    //                 'dateTime'  => $dateTime,
    //                 'assessment_amount' =>  $LicenseFee,
    //                 'assessment_arrears'    => 0,
    //                 'plenty'    => 0,
    //                 'amount_paid'   => 0,
    //                 'due'   => $LicenseFee
    //             ];
    //             $this->licenseAmountHistory($licenseAmountHistory);
    //         }
    //     }

    //     return $this->success([
    //         'success'   => true,
    //     ]);
    // }

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
            $business_images = null;
            if(!empty($business->Signature))
            {
                $multiple_business_images = explode(",",$business->Signature);
                foreach($multiple_business_images as $key => $value)
                {
                    $business_images[] = asset("business_images/".$value);
                }
            }
            $businessResponse['business_images'] = $business_images;

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

            $business_images = null;
            if(!empty($business->Signature))
            {
                $multiple_business_images = explode(",",$business->Signature);
                foreach($multiple_business_images as $key => $value)
                {
                    $business_images[] = asset("business_images/".$value);
                }
            }
            $businessResponse['business_images'] = $business_images;

            $mergedData = $businessResponse;
        }

        return $this->success([
            'success'   => true,
            'data' => $mergedData
        ]);
    }

    public function nonProfitInsertBusiness(Request $request)
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
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }
        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
        }

        $BusinessReg = new BusinessReg;

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
        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
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

        $BusinessLicense = new BusinessLicense;
        $LastId = $BusinessReg->id;
        $BusinessLicense->BusinessRegId = $LastId;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->BusinessType = 'non_profit';
        $BusinessLicense->BusinessCategory = $request->IsTheOrganization;
        $BusinessLicense->save();

        return $this->success([
            'success'   => true,
            'message' => 'Non Profit business created successfully'
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
        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
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
        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
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

    public function profitInsertBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
        }

        $BusinessReg = new BusinessReg;
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
        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
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

        $BusinessLicense = new BusinessLicense;
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
            'message' => 'Profit business created successfully'
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

        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
            if(count($images) > 5)
            {
                return [
                    'errors'    => 'Image must be less than 5'
                ];
            }
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
        if ($request->hasFile('business_images')) {
            $images = $request->file('business_images');
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
