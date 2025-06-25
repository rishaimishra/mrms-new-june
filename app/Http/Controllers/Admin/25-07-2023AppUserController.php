<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\BusinessReg;
use App\Models\User;
use App\PaymentHistory;
use App\BusinessLicense;
use App\Grids\UsersGrid;
use App\Models\District;
use Illuminate\Http\Request;
use App\LicenseAmountHistory;
use Illuminate\Validation\Rule;
use App\BusinessLicenseCategory;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Hash;
// require_once 'dompdf/vendor/autoload.php';
// use Dompdf\Dompdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AppUserController extends Controller
{
    public function create()
    {
        $district = District::pluck('name', 'id');
        $business = [];
        $businessLic = [];
        $payment_histories = [];
        if(!empty(request()->input('business_id')))
        {
            $business = BusinessReg::find(request()->input('business_id'));
            if(!empty($business))
            {
                $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
                $payment_histories = PaymentHistory::where('business_id',$business->id)->where('is_paid',1)->get();
            }
        }

        return view('admin.users.app_user_create', compact('district','business','businessLic','payment_histories'));
    }

    public function paymentStore(Request $request)
    {
        // dd($request->all());
        $businessLic = BusinessLicense::where('BusinessRegId',request()->input('business_id'))->first();
        if($request->method == "license")
        {
            $validator = Validator::make($request->all(), [
                'price' =>  'required|integer',
                'pay_type' => 'required|string',
                'payer_name' => 'required|string',
                'pay_taken_by' => 'required|string',
            ]);
            if ($validator->fails())
            {
                return redirect()->back()->withErrors($validator->errors());
            }
            $method = $request->method;
            if($businessLic->payment_status == "unpaid")
            {
                return redirect()->back()->with('error', 'Kindly Pay your Registration Fee First');
            }
            $paymentHistory = PaymentHistory::where('business_id', $businessLic->BusinessRegId)->where('type','license')->latest()->first();
            if($paymentHistory)
            {
                $paymentYear = date('Y',$paymentHistory->dateTime);
                $currentYear = date('Y');

                if($currentYear == $paymentYear AND $paymentHistory->is_paid == 1 AND $paymentHistory->due == 0)
                {
                    return redirect()->back()->with('error', 'Already Paid this year payment');
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
                        return redirect()->back()->with('error', 'Already Paid this year payment');
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
                        // dd("else andr");
                        $assessment_amount = $paymentHistory->assessment_amount;
                        $assessment_arrears = $paymentHistory->assessment_arrears;
                        $pelanty = $paymentHistory->plenty;
                        $amount_paid = $paymentHistory->amount_paid + $request->price;
                        // $due = ($paymentHistory->assessment_amount + $paymentHistory->plenty) - $request->price;
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
            $pdf = PDF::loadView('admin.users.license_certificate', $newArray);
            $attachment = $pdf->output();
            $filename = 'License Demand Notice.pdf';

            $pdf2 = PDF::loadView('admin.users.license_specific_certificate', $newArray);
            $attachment2 = $pdf2->output();
            $filename2 = 'Payment Receipt.pdf';

            Mail::send('admin.emails.license_certificate', $data, function ($message) use ($businessLic,$attachment,$filename,$attachment2,$filename2) {
                $message->to($businessLic->emailIfAny);
                $message->subject('Payment');
                $message->attachData($attachment, $filename);
                $message->attachData($attachment2, $filename2);
            });

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

        return redirect()->back()->with('success', 'Your payment has been saved.');
    }


    private function downloadBothCertificate($payment_id,$BusinessRegId)
    {
            $payment = PaymentHistory::find($payment_id);
            $business = BusinessReg::find($BusinessRegId);
            $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();

            if ($businessLic->BusinessType == "normal") {
                $businessType = "Normal";
            } elseif ($businessLic->BusinessType == "non_profit") {
                $businessType = "Non Profit";
            } else {
                $businessType = "-";
            }

            $data = [
                'business_id'   => $business->id,
                'business_name'  => $businessLic->BusinessName,
                'location' => $businessLic->BusinessLocation,
                'type' => $businessType,
                'category'  => $businessLic->category->title,
                'price' => $businessLic->category->price,
                'month' => date('M', $payment->dateTime),
                'year' => date('Y', $payment->dateTime),
                'currentDate'   => date('d/M/Y', strtotime(date('Y-m-d'))),
                'address' => $business->organizationMainContactAddress,
            ];

            $pdf = PDF::loadView('admin.users.certificate', $data)->setPaper('a4', 'landscape');
            $attachment = $pdf->output();
            $filename = 'Registration Certification.pdf';

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
            // dd($businessLicenseCategory);
            if(!empty($businessLicenseCategory))
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
            $pdf2 = PDF::loadView('admin.users.license_certificate', $newArray);
            $attachment2 = $pdf2->output();
            $filename2 = 'License Demand Notice.pdf';

            Mail::send('admin.emails.certificate', $data, function ($message) use ($businessLic,$attachment,$attachment2, $filename,$filename2) {
                $message->to($businessLic->emailIfAny);
                $message->subject('Payment');
                $message->attachData($attachment, $filename);
                $message->attachData($attachment2, $filename2);
            });
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'gender' => 'required',
            'ward' => 'required',
            'constituency' => 'required',
            'section' => 'required',
            'chiefdom' => 'required',
            'district' => 'required',
            'province' => 'required',
            'street_name' => 'nullable|string|max:254',
            'street_number' => 'nullable|string|max:254',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'assign_district' => 'required'
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->ward = $request->ward;
        $user->constituency = $request->constituency;
        $user->section = $request->section;
        $user->chiefdom = $request->chiefdom;
        $user->district = $request->district;
        $user->province = $request->province;
        $user->street_name = $request->street_name;
        $user->street_number = $request->street_number;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_active = $request->is_active ?: false;
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $user->assign_district_id = $district->id ?: null;
                $user->assign_district = $district->name ?: null;
            }
        }
        $user->save();

        return Redirect()->route('admin.app-user.list')->with('success', 'User Created Successfully !');
    }

    public function list(UsersGrid $usersGrid, Request $request)
    {
        $query = User::query();
        if (request()->user()->hasRole('Super Admin')) {
        } else {
            $query->where('assign_district', request()->user()->assign_district);
        }
        return $usersGrid
            ->create(['query' => $query->latest(), 'request' => $request])
            ->renderOn('admin.users.app_user_list');
    }

    public function show(Request $request)
    {
        //dd($request->adminuser);

        $data['app_user'] = User::find($request->user);
        $data['district'] =  District::pluck('name', 'id');
        return view('admin.users.app_user_update', $data);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);

        //dd($request->all());
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'gender' => 'required',
            'ward' => 'required',
            'constituency' => 'required',
            'section' => 'required',
            'chiefdom' => 'required',
            'district' => 'required',
            'province' => 'required',
            'street_name' => 'nullable|string|max:254',
            'street_number' => 'nullable|string|max:254',
            'assign_district' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $update_data = [
            'name' => $request->name,
            'email' => $request->email,
            'ward' => $request->ward,
            'constituency' => $request->constituency,
            'section' => $request->section,
            'chiefdom' => $request->chiefdom,
            'district' => $request->district,
            'province' => $request->province,
            'street_name' => $request->street_name,
            'street_number' => $request->street_number,
            'gender' => $request->gender,
        ];
        $update_data['is_active'] = $request->is_active ?: false;

        if ($request->password != '') {
            $update_data['password'] =  Hash::make($request->password);
        }
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $update_data['assign_district_id'] = $district->id ?: null;
                $update_data['assign_district'] = $district->name ?: null;
            }
        }
        $user->fill($update_data);
        $user->save();

        //dd($admin_user);

        return Redirect()->back()->with('success', 'Updated Successful !');
    }

    public function destroy(Request $request)
    {

        $user = User::find($request->user);
        if (!$user->properties()->count()) {
            $user->delete();
            return Redirect()->route('admin.app-user.list')->with('success', 'User Deleted Successfully !');
        }

        return Redirect()->route('admin.app-user.list')->with('success', 'You can not delete the user. User is associated with the properties.');
    }
    public function resetPassword(Request $request)
    {

        //dd($request);
        $v = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'id' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }

        $user =  User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset = PasswordResetRequest::where('user_id', $request->id)->update(['process' => 1]);
        return redirect()->back()->with('success', 'Password Update Successfully !');
    }

    public function generateCertifcate($payment_id)
    {
        $payment = PaymentHistory::find($payment_id);
        $business = BusinessReg::find($payment->business_id);
        $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
        if($businessLic->BusinessType == "normal")
        {
            $businessType = "Normal";
        }
        elseif($businessLic->BusinessType == "non_profit")
        {
            $businessType = "Non Profit";
        }
        else
        {
            $businessType = "-";
        }
        $data = [
            'business_id'   => $business->id,
            'business_name'  => $businessLic->BusinessName,
            'location' => $businessLic->BusinessLocation,
            'type' => $businessType,
            'category'  => $businessLic->category->title,
            'price' => $businessLic->category->price,
            'month' => date('M',$payment->dateTime),
            'year' => date('Y',$payment->dateTime),
            'currentDate'   => date('d/M/Y',strtotime(date('Y-m-d'))),
            'address' => $business->organizationMainContactAddress,
        ];
        $pdf = PDF::loadView('admin.users.certificate', $data)->setPaper('a4', 'landscape');

        return $pdf->download('Registration Certification.pdf');
    }

    public function generateLicensePaymentCertifcate($payment_id,$business_id)
    {
        if($payment_id != 0)
        {
            $payment = PaymentHistory::find($payment_id);
            $business = BusinessReg::find($payment->business_id);
            $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
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
        }
        elseif($business_id != 0)
        {
            $business = BusinessReg::find($business_id);
            $payment = LicenseAmountHistory::where("business_id",$business->id)->first();
            $extendedData = [
                "type"  => "business_id",
                'due'   => !empty($payment->due) ? number_format($payment->due,2) : 0.00,
                'dateTime'  => !empty($payment->dateTime) ? $payment->dateTime : date("Y"),
                'assessment_amount' => !empty($payment->assessment_amount) ? number_format($payment->assessment_amount,2) : 0.00,
                'assessment_arrears'    =>  !empty($payment->assessment_arrears) ? number_format($payment->assessment_arrears,2) : 0.00,
                'plenty'    =>  !empty($payment->plenty) ? number_format($payment->plenty,2) : 0.00,
            ];
        }

            $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();
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
                "AmountPaid"    => !empty($payment->amount_paid) ?$payment->amount_paid: "N/A",
                "PrintDate"     => date('d/m/Y'),
                "percentBaseRate" => number_format($BusinessLocationPrice,2),
                "image1" => $business->Signature,
                "image2" => $business->pic,
            ];
            $newArray = array_merge($data,$extendedData);
            // dd($newArray);

            if($payment_id != 0)
            {
                $pdf = PDF::loadView('admin.users.license_specific_certificate', $newArray);
                return $pdf->download('License.pdf');
                // return view("admin.users.license_specific_certificate");
            }
            else
            {
                $pdf = PDF::loadView('admin.users.license_certificate', $newArray);
                return $pdf->download('License.pdf');
            }

    }
}
