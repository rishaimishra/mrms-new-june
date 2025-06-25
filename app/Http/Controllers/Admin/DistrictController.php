<?php

namespace App\Http\Controllers\Admin;

use PDF;
use DateTime;
use ZipArchive;
use Dompdf\Dompdf;
use App\Rules\Name;
use App\BusinessReg;
use App\BusinessType;
use App\PaymentHistory;
use App\BusinessLicense;
use App\Models\District;
use App\Models\Property;
use App\Models\AdminUser;
use App\Grids\DistrictsGrid;
use Illuminate\Http\Request;
use App\BackupPaymentHistory;
use App\Grids\AdminUsersGrid;
use App\LicenseAmountHistory;
use App\BusinessLicenseCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function businessList(){
        if(!empty(request()->input('BusinessType')) OR !empty(request()->input('location')) OR !empty(request()->input('paid_type')) OR !empty(request()->input('registration_date')) OR !empty(request()->input('e_date')) OR !empty(request()->input('amount_paid')) OR !empty(request()->input('street')) OR !empty(request()->input('p_status')))
        {
            $employees = [];
            $query = new BusinessLicense();
            if(!empty(request()->input('BusinessType')))
            {
                $query = $query->where('BusinessType', request()->input('BusinessType'));

            }
            if(!empty(request()->input('location')))
            {
                $location = request()->input('location');
                $query = $query->where('BusinessLocation', $location);
            }
            if(!empty(request()->input('street')))
            {
                $street = request()->input('street');
                $query = $query->where('Street', $street);
            }

            $BusinessLicenseData = $query->Orderby('id','DESC')->get();
            $businessArray = [];
            // dd($BusinessLicenseData);
            foreach($BusinessLicenseData as $BusinessLicense)
            {
                if(!empty(request()->input('paid_type')))
                {
                    $paid_type = request()->input('paid_type');
                    $payment_hostory_data = PaymentHistory::where('business_id',$BusinessLicense->BusinessRegId)
                    ->where('pay_type',$paid_type)->OrderBy('id','DESC')->get();
                    foreach($payment_hostory_data as $payment_hostory)
                    {
                        if(!in_array($payment_hostory->business_id,$businessArray))
                        {
                            $businessArray[] = $payment_hostory->business_id;
                            $employees[] = Businessreg::find($payment_hostory->business_id);
                        }
                    }
                }
                else
                {
                    $employees[] = Businessreg::find($BusinessLicense->BusinessRegId);
                }

            }
            if(!empty(request()->input('registration_date')))
            {
                $resgistration_date = request()->input('registration_date');
                $explode = explode('-',$resgistration_date);
                $start_date = date('Y-m-d',strtotime($explode[0]));
                $end_date = date('Y-m-d',strtotime($explode[1]));
                if($start_date == $end_date)
                {
                    $BusinessRegData = BusinessReg::where('created_at', $start_date)->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }
                else
                {
                    $BusinessRegData = BusinessReg::whereBetween('created_at', [$start_date, $end_date])->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }
            }

            if(!empty(request()->input('e_date')))
            {
                $e_date = request()->input('e_date');
                $explode = explode('-',$e_date);
                $start_date = date('Y-m-d',strtotime($explode[0]));
                $end_date = date('Y-m-d',strtotime($explode[1]));
                if($start_date == $end_date)
                {
                    $BusinessRegData = BusinessReg::where('dateOfEstablishment', $start_date)->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }
                else
                {
                    $BusinessRegData = BusinessReg::whereBetween('dateOfEstablishment', [$start_date, $end_date])->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }

            }
            if(!empty(request()->input('amount_paid')))
            {
                $businessArray = [];
                $amount_paid = request()->input('amount_paid');
                $explode = explode('-',$amount_paid);
                $start_date = strtotime(date('Y-m-d',strtotime($explode[0])));
                $end_date = strtotime(date('Y-m-d',strtotime($explode[1])));
                if($start_date == $end_date)
                {
                    $payment_hostory_data = PaymentHistory::where('dateTime', $start_date)->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($payment_hostory_data as $payment_hostory)
                    {
                        if(!in_array($payment_hostory->business_id,$businessArray))
                            {
                                $businessArray[] = $payment_hostory->business_id;
                                $employees[] = Businessreg::find($payment_hostory->business_id);
                            }
                    }
                }
                else
                {
                    $payment_hostory_data = PaymentHistory::whereBetween('dateTime', [$start_date, $end_date])->OrderBy('id','DESC')->get();
                    // dd($payment_hostory_data);
                    $employeesData = $employees;
                    $employees = [];
                    foreach($payment_hostory_data as $payment_hostory)
                    {
                        foreach($employeesData as $employee)
                        {
                            if(!in_array($payment_hostory->business_id,$businessArray))
                            {
                                $businessArray[] = $payment_hostory->business_id;
                                $employees[] = Businessreg::find($payment_hostory->business_id);
                            }
                        }
                    }
                }
            }

            if(!empty(request()->input('p_status')))
            {
                $p_status = request()->input('p_status');
                $employeesData = $employees;
                $employees = [];
                foreach($employeesData as $business)
                {
                    $businessSLIC = BusinessLicense::where('BusinessRegId',$business->id)->first();
                    if(!empty($businessSLIC->BusinessLicenseCategory))
                    {
                        $license_amount_history = LicenseAmountHistory::where('business_id', $business->id)->OrderBy('id','DESC')->get();
                        foreach($license_amount_history as $license_amount)
                        {
                            if(!in_array($license_amount->business_id,$businessArray))
                            {
                                if($p_status == "due" AND $license_amount->due != 0)
                                {
                                    $businessArray[] = $license_amount->business_id;
                                    $employees[] = Businessreg::find($license_amount->business_id);
                                }
                                elseif($p_status == "paid" AND $license_amount->due == 0)
                                {
                                    $businessArray[] = $license_amount->business_id;
                                    $employees[] = Businessreg::find($license_amount->business_id);
                                }
                            }
                        }
                    }

                }
            }
        }
        else
        {
            $employees = BusinessReg::OrderBy('id', 'desc')->get();
        }
        $locations = BusinessLicense::where('Street','!=',null)->distinct()->get('Street');
        return view('admin.business-reg-list', compact('employees','locations'));
    //    dd(BusinessReg::get());
    }

    public function businessRegList($bit){
        // return request()->input('business_id');
        if(!empty(request()->input('BusinessType')) OR !empty(request()->input('location')) OR !empty(request()->input('paid_type')) OR !empty(request()->input('registration_date')) OR !empty(request()->input('e_date')) OR !empty(request()->input('amount_paid')) OR !empty(request()->input('street')) OR !empty(request()->input('p_status')) OR !empty(request()->input('business_id')) OR !empty(request()->input('business_name')))
        {
            $employees = [];
            $query = new BusinessLicense();
            if(!empty(request()->input('BusinessType')))
            {
                $query = $query->where('BusinessType', request()->input('BusinessType'));

            }
            if(!empty(request()->input('business_name')))
            {
                $query = $query->where('BusinessName', request()->input('business_name'));
            }
            if(!empty(request()->input('location')))
            {
                $location = request()->input('location');
                $query = $query->where('BusinessLocation', $location);
            }
            if(!empty(request()->input('street')))
            {
                $street = request()->input('street');
                $query = $query->where('Street', $street);
            }

            $BusinessLicenseData = $query->Orderby('id','DESC')->get();
            $businessArray = [];
            // dd($BusinessLicenseData);
            foreach($BusinessLicenseData as $BusinessLicense)
            {
                if(!empty(request()->input('paid_type')))
                {
                    $paid_type = request()->input('paid_type');
                    $payment_hostory_data = PaymentHistory::where('business_id',$BusinessLicense->BusinessRegId)
                    ->where('pay_type',$paid_type)->OrderBy('id','DESC')->get();
                    foreach($payment_hostory_data as $payment_hostory)
                    {
                        if(!in_array($payment_hostory->business_id,$businessArray))
                        {
                            $businessArray[] = $payment_hostory->business_id;
                            $employees[] = Businessreg::find($payment_hostory->business_id);
                        }
                    }
                }
                else
                {
                    $employees[] = Businessreg::find($BusinessLicense->BusinessRegId);
                }

            }
            if(!empty(request()->input('registration_date')))
            {
                $resgistration_date = request()->input('registration_date');
                $explode = explode('-',$resgistration_date);
                $start_date = date('Y-m-d',strtotime($explode[0]));
                $end_date = date('Y-m-d',strtotime($explode[1]));
                if($start_date == $end_date)
                {
                    $BusinessRegData = BusinessReg::where('created_at', $start_date)->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }
                else
                {
                    $BusinessRegData = BusinessReg::whereBetween('created_at', [$start_date, $end_date])->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }
            }


            if(!empty(request()->input('business_id')))
            {
                $req_business_id = request()->input('business_id');
                $BusinessRegData = BusinessReg::where('id', $req_business_id)->OrderBy('id','DESC')->get();
                $employeesData = $employees;
                $employees = [];
                foreach($BusinessRegData as $Business)
                {
                    foreach($employeesData as $employee)
                    {
                        if($employee->id == $Business->id)
                        {
                            $employees[] = $employee;
                        }
                    }
                }

                // $query = $query->where('BusinessType', request()->input('business_id'));

            }


            if(!empty(request()->input('e_date')))
            {
                $e_date = request()->input('e_date');
                $explode = explode('-',$e_date);
                $start_date = date('Y-m-d',strtotime($explode[0]));
                $end_date = date('Y-m-d',strtotime($explode[1]));
                if($start_date == $end_date)
                {
                    $BusinessRegData = BusinessReg::where('dateOfEstablishment', $start_date)->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }
                else
                {
                    $BusinessRegData = BusinessReg::whereBetween('dateOfEstablishment', [$start_date, $end_date])->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($BusinessRegData as $Business)
                    {
                        foreach($employeesData as $employee)
                        {
                            if($employee->id == $Business->id)
                            {
                                $employees[] = $employee;
                            }
                        }
                    }
                }

            }
            if(!empty(request()->input('amount_paid')))
            {
                $businessArray = [];
                $amount_paid = request()->input('amount_paid');
                $explode = explode('-',$amount_paid);
                $start_date = strtotime(date('Y-m-d',strtotime($explode[0])));
                $end_date = strtotime(date('Y-m-d',strtotime($explode[1])));
                if($start_date == $end_date)
                {
                    $payment_hostory_data = PaymentHistory::where('dateTime', $start_date)->OrderBy('id','DESC')->get();
                    $employeesData = $employees;
                    $employees = [];
                    foreach($payment_hostory_data as $payment_hostory)
                    {
                        if(!in_array($payment_hostory->business_id,$businessArray))
                            {
                                $businessArray[] = $payment_hostory->business_id;
                                $employees[] = Businessreg::find($payment_hostory->business_id);
                            }
                    }
                }
                else
                {
                    $payment_hostory_data = PaymentHistory::whereBetween('dateTime', [$start_date, $end_date])->OrderBy('id','DESC')->get();
                    // dd($payment_hostory_data);
                    $employeesData = $employees;
                    $employees = [];
                    foreach($payment_hostory_data as $payment_hostory)
                    {
                        foreach($employeesData as $employee)
                        {
                            if(!in_array($payment_hostory->business_id,$businessArray))
                            {
                                $businessArray[] = $payment_hostory->business_id;
                                $employees[] = Businessreg::find($payment_hostory->business_id);
                            }
                        }
                    }
                }
            }

            if(!empty(request()->input('p_status')))
            {
                $p_status = request()->input('p_status');
                $employeesData = $employees;
                $employees = [];
                foreach($employeesData as $business)
                {
                    $businessSLIC = BusinessLicense::where('BusinessRegId',$business->id)->first();
                    if(!empty($businessSLIC->BusinessLicenseCategory))
                    {
                        $license_amount_history = LicenseAmountHistory::where('business_id', $business->id)->OrderBy('id','DESC')->get();
                        foreach($license_amount_history as $license_amount)
                        {
                            if(!in_array($license_amount->business_id,$businessArray))
                            {
                                if($p_status == "due" AND $license_amount->due != 0)
                                {
                                    $businessArray[] = $license_amount->business_id;
                                    $employees[] = Businessreg::find($license_amount->business_id);
                                }
                                elseif($p_status == "paid" AND $license_amount->due == 0)
                                {
                                    $businessArray[] = $license_amount->business_id;
                                    $employees[] = Businessreg::find($license_amount->business_id);
                                }
                            }
                        }
                    }

                }
            }
        }
        else
        {
            if($bit == 0)
            {
                $employees = BusinessReg::OrderBy('id', 'desc')
                ->orWhere('development_officer_status',1)
                ->orWhere('cheif_officer_status',1)
                ->orWhere('development_officer_status',NULL)
                ->orWhere('cheif_officer_status',NULL)
                ->get();
            }
            else if($bit == 1)
            {
                $employees = BusinessReg::join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
                ->where('business_license.BusinessType', 'non_profit')
                ->where('business_registration.development_officer_status', 0)
                ->where('business_registration.cheif_officer_status', 0)
                ->select('business_registration.*')
                ->OrderBy('business_registration.id','DESC')
                ->get();
            }
            else if($bit == 2)
            {
                $employees = BusinessReg::join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
                ->where('business_license.BusinessType', 'non_profit')
                ->where('business_registration.development_officer_status', 1)
                ->where('business_registration.cheif_officer_status', 0)
                ->select('business_registration.*')
                ->OrderBy('business_registration.id','DESC')
                ->get();
            }
            else if($bit == 3)
            {
                $employees = BusinessReg::join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
                ->where('business_license.BusinessType', 'non_profit')
                ->where('business_registration.development_officer_status', 2)
                ->select('business_registration.*')
                ->OrderBy('business_registration.id','DESC')
                ->get();
            }
            else if($bit == 4)
            {
                $employees = BusinessReg::join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
                ->where('business_license.BusinessType', 'normal')
                ->where('business_registration.development_officer_status', 0)
                ->where('business_registration.cheif_officer_status', 0)
                ->select('business_registration.*')
                ->OrderBy('business_registration.id','DESC')
                ->get();
            }
            else if($bit == 5)
            {
                $employees = BusinessReg::join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
                ->where('business_license.BusinessType', 'normal')
                ->where('business_registration.development_officer_status', 1)
                ->where('business_registration.cheif_officer_status', 0)
                ->select('business_registration.*')
                ->OrderBy('business_registration.id','DESC')
                ->get();
            }
            else if($bit == 6)
            {
                $employees = BusinessReg::join('business_license', 'business_registration.id', '=', 'business_license.BusinessRegId')
                ->where('business_license.BusinessType', 'normal')
                ->where('business_registration.development_officer_status', 2)
                ->where('business_registration.cheif_officer_status', 0)
                ->select('business_registration.*')
                ->OrderBy('business_registration.id','DESC')
                ->get();
            }
        }
        $locations = BusinessLicense::where('Street','!=',null)->distinct()->get('Street');
        return view('admin.business-reg-list', compact('employees','locations','bit'));
    }

    public function changeApprovalStatus(Request $request)
    {
        
        if($request->type == "development_officer_status")
        {
            $businessReg = BusinessReg::find($request->id);
            $businessReg->development_officer_status = $request->status;
            $businessReg->save();
        }
        elseif($request->type == "bulk_development_officer_status")
        {
            $explode_business_ids = explode(",",$request->id);
            foreach($explode_business_ids as $business_id)
            {
                $businessReg = BusinessReg::find($business_id);
                $businessReg->development_officer_status = $request->status;
                $businessReg->save();
            }

        }
        elseif($request->type == "bulk_cheif_officer_status")
        {
            $explode_business_ids = explode(",",$request->id);
            foreach($explode_business_ids as $business_id)
            {
                $businessReg = BusinessReg::find($business_id);
                $businessReg->cheif_officer_status = $request->status;
                $businessReg->save();

                $businessLic = BusinessLicense::where('BusinessRegId',$businessReg->id)->first();
                $dateTime = strtotime(date('Y-m-d H:i:s'));

                $LicenseFee = number_format2Dec($businessLic->LicenseFee);
                $payment_history = new PaymentHistory();
                $payment_history->type = 'license';
                $payment_history->business_id = $businessReg->id;
                $payment_history->price = $LicenseFee;
                $payment_history->pay_type = '';
                $payment_history->payer_name = '';
                $payment_history->pay_taken_by   = '';
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
                    'business_id'   => $businessReg->id,
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
        else
        {
            $businessReg = BusinessReg::find($request->id);
            $businessReg->cheif_officer_status = $request->status;

            $businessLic = BusinessLicense::where('BusinessRegId',$businessReg->id)->first();
            $dateTime = strtotime(date('Y-m-d H:i:s'));

            $LicenseFee = number_format2Dec($businessLic->LicenseFee);
            $payment_history = new PaymentHistory();
            $payment_history->type = 'license';
            $payment_history->business_id = $businessReg->id;
            $payment_history->price = $LicenseFee;
            $payment_history->pay_type = '';
            $payment_history->payer_name = '';
            $payment_history->pay_taken_by   = '';
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
                'business_id'   => $businessReg->id,
                'dateTime'  => $dateTime,
                'assessment_amount' =>  $LicenseFee,
                'assessment_arrears'    => 0,
                'plenty'    => 0,
                'amount_paid'   => 0,
                'due'   => $LicenseFee
            ];
            $this->licenseAmountHistory($licenseAmountHistory);
            $businessReg->save();
        }

        return back()->with('success','Approval Status updated successfully');
    }

    public function businessView($id){
        $business = BusinessReg::find($id);
        $businessLic = BusinessLicense::where('BusinessRegId','=',$id)->first();
        $license_history_amount = LicenseAmountHistory::where('business_id',$id)->get();
        $payment_histories = PaymentHistory::where('type', 'license')->where('business_id', $business->id)->get();
        $all_payment_histories = PaymentHistory::where('business_id',$business->id)->where('is_paid',1)->get();
        $latestPayment = PaymentHistory::where('type', 'license')->where('business_id', $business->id)->latest()->first();
        if($latestPayment)
        {
            $previousYear = date('Y',$latestPayment->dateTime);
            $currentYear = date('Y');
            if($currentYear > $previousYear)
            {
                // if($businessLic->LicenseFee - $latestPayment->price != 0)
                // {
                    $dateTime = strtotime(date('Y-m-d'));
                    $payment = new PaymentHistory;
                    $payment->business_id = $business->id;
                    $payment->type = 'license';
                    $payment->payer_name = '';
                    $payment->pay_type = '';
                    $payment->pay_taken_by = '';
                    $payment->dateTime = $dateTime;
                    $assessment_amount = number_format2Dec($latestPayment->assessment_amount);
                    // $assessment_arrears = $businessLic->LicenseFee - $latestPayment->due;
                    $assessment_arrears = number_format2Dec($latestPayment->due);
                    if($latestPayment->due == 0)
                    {
                        $pelanty = 0;
                    }
                    else
                    {
                        // $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
                        $pelanty = ((25/100)*$assessment_arrears);
                        $pelanty = number_format2Dec($pelanty);
                    }
                    $due = $pelanty + $assessment_arrears + $latestPayment->assessment_amount;
                    $due = number_format2Dec($due);

                    $payment->assessment_amount = $assessment_amount;
                    $payment->assessment_arrears = $assessment_arrears;
                    $payment->plenty = $pelanty;
                    $payment->amount_paid = 0;
                    $payment->price = $pelanty + number_format2Dec($latestPayment->assessment_amount);
                    $payment->due = $pelanty + number_format2Dec($latestPayment->assessment_amount);
                    $payment->save();

                    $licenseAmountHistory = [
                        'business_id'   => $business->id,
                        'dateTime'  => $dateTime,
                        'assessment_amount' =>  $assessment_amount,
                        'assessment_arrears'    => $assessment_arrears,
                        'plenty'    => $pelanty,
                        'amount_paid'   => 0,
                        'due'   => $due
                    ];
                    $this->licenseAmountHistory($licenseAmountHistory);
                // }
            }
        }
        return view('admin.business-view', compact('business','businessLic','payment_histories','latestPayment','all_payment_histories','license_history_amount'));
    }

    public function checkPaymentHistory($id)
    {
        $license_history_amount = LicenseAmountHistory::find($id);
        return $license_history_amount;
    }

    public function updateCheckPaymentHistory(Request $request)
    {
        // return $request;
        $license_history_amount = LicenseAmountHistory::find($request->id);
        $payment = PaymentHistory::where('type', 'license')->where('business_id', $license_history_amount->business_id)->latest()->first();
        // return $payment;
        if($payment)
        {
            $assessment_amount = number_format2Dec($request->assessment_amount);
            $assessment_arrears = number_format2Dec($request->assessment_arrears);
            $pelanty = number_format2Dec($request->pelanty);
            $due = number_format2Dec($request->due);

            $payment->assessment_amount = $assessment_amount;
            $payment->assessment_arrears = $assessment_arrears;
            $payment->plenty = $pelanty;
            $payment->amount_paid = $request->amount_paid;
            $payment->price = $pelanty + number_format2Dec($assessment_amount);
            $payment->due = $due;
            $payment->is_change = 1;
            $payment->save();


            $license_history_amount->business_id = $license_history_amount->business_id;
            $license_history_amount->assessment_amount = $request->assessment_amount;
            $license_history_amount->assessment_arrears = $request->assessment_arrears;
            $license_history_amount->plenty = $request->plenty;
            $license_history_amount->amount_paid = $request->amount_paid;
            $license_history_amount->due = $request->due;
            $license_history_amount->is_change = 1;
            $license_history_amount->save();

            return back()->with("success","Payment Update Successfully");
        }
        

    }

    public function businessEdit($id){
        $business = BusinessReg::find($id);
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

            return view("admin.profit_edit_business")->with(compact('business','businessLic','mainProjectACtivities','communityProjectEstablishmentOrganization','communityDevelopmentActivities','businessTypes','BusinessLicenseCategory'));
        }
        else
        {
            $businessTypes = BusinessType::where('type','non_profit')->get();

            $objectOfOrganization = explode(",",$business->objectOfOrganization);
            $mainProjectACtivities = explode(",",$business->mainProjectACtivities);
            $categoryOfTarget = explode(",",$business->categoryOfTargetBEnefeciary);
            $communityProjectEstablishmentOrganization = explode(",",$business->communityProjectEstablishment);
            $SourceOfFunding = explode(",",$business->sourceOfFunding);

            return view('admin.non_profit_edit_business')->with(compact('business','businessLic','businessTypes','SourceOfFunding','communityProjectEstablishmentOrganization','mainProjectACtivities','categoryOfTarget','objectOfOrganization'));
        }
        // return view('admin.business-edit', compact('business'));
    }

    public function profitBusinessUpdate(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'nameOfOrganization' => 'required',
            'dateofEstablishment' => 'required',
            'contactNumber' => 'required',
            'oranganisationName' => 'required',
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
                return redirect()->back()->with('error','Image must be less than 5');
            }
        }
        $BusinessReg = BusinessReg::find($id);
        $BusinessReg->nameOfOrganization = $request->nameOfOrganization;
        $BusinessReg->dateOfEstablishment = $request->dateofEstablishment;
        $BusinessReg->contactNumber = $request->contactNumber;
        $BusinessReg->NameOfOtherContactPerson = $request->nameOfOtherContPerson;
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
        $BusinessLicense->BusinessType = $request->BusinessType;
        $BusinessLicense->BusinessClass = '';
        $BusinessLicense->BusinessSize = $request->BusinessSize;
        $BusinessLicense->BusinessLocation = $request->BusinessLocation;
        $BusinessLicense->BusinessCategory = $request->BusinessCategory;
        $BusinessLicense->BusinessLicenseCategory = $request->BusinessLicenseCategory;
        $BusinessLicense->LicenseFee = $request->LicenseFee;
        $BusinessLicense->save();

        return redirect()->route('admin.business.view',$BusinessReg->id)
                        ->with('success','Business updated successfully');
    }

    public function nonProfitBusinessUpdate(Request $request,$id)
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
                return redirect()->back()->with('error','Image must be less than 5');
            }
        }

        $BusinessReg = BusinessReg::find($id);

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

        $BusinessLicense = BusinessLicense::where('BusinessRegId',$BusinessReg->id)->first();
        $LastId = $BusinessReg->id;
        $BusinessLicense->BusinessRegId = $LastId;
        $BusinessLicense->emailIfAny = $request->emailIfAny;
        $BusinessLicense->BusinessType = $request->BusinessType;
        $BusinessLicense->BusinessCategory = $request->BusinessCategory;
        $BusinessLicense->save();

        return redirect()->route('admin.business.view',$BusinessReg->id)
                        ->with('success','Business updated successfully');
    }

    public function businessLicEdit($id){
        $business =BusinessLicense::where('BusinessRegId','=',$id)->first();
        if(!$business)
        {
            return redirect()->back();
        }
        $businessLicenseCatgeories = BusinessLicenseCategory::where('type','normal')->get();

        return view('admin.businessLic-edit', compact('business'))->with(compact('businessLicenseCatgeories'));
    }

    public function businessUpdate(Request $request, $id){
        $this->validate($request,[
            'nameOfOrganization'=>'required',
            'dateofEstablishment'=>'required',
            'organContactAddress'   => 'required',
            'organSubContactAddress'    => 'required',
            'contactNumber'    => 'required',
            'IsTheOrganization'    => 'required',
            'nameOfheadOrganization'    => 'required',
            'LeadinPioneerAddress'    => 'required',
            'nameOfOtherContPerson'    => 'required',
            'otherContactPersonAddress'    => 'required',
            'membershipTotal'    => 'required',
            'MembershipMale'    => 'required',
            'MemberShipFemale'    => 'required',
            'mainAim'    => 'required',
            'ObjectOfOrganization'    => 'required',
            'mainProjectActivities'    => 'required',
            'categoryOfTargetBEnefeciary'   => 'required',
            'communityProjectEstablishment'    => 'required',
            'LocationHasYourOrganOperating'    => 'required',
            'OrganizationIntendOperating'   => 'required',
            'SourceOfFunding'    => 'required',
            'AddresOfOrganizationChairman'  =>  'required',
         ]);

        $business = BusinessReg::find($id);
        $business->nameOfOrganization = $request->nameOfOrganization;
        $business->dateOfEstablishment = $request->dateofEstablishment;
        $business->organizationMainContactAddress = $request->organContactAddress;
        $business->organizationSubContactAddress = $request->organSubContactAddress;
        $business->contactNumber = $request->contactNumber;
        $business->IsTheOrganization = $request->IsTheOrganization;
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
        $business->categoryOfTargetBEnefeciary = $request->categoryOfTargetBEnefeciary;
        $business->communityProjectEstablishment = $request->communityProjectEstablishment;
        $business->LocationHasYourOrganOperating = $request->LocationHasYourOrganOperating;
        $business->OrganizationIntendOperating = $request->OrganizationIntendOperating;
        $business->sourceOfFunding = $request->SourceOfFunding;
        $business->AddresOfOrganizationChairman = $request->AddresOfOrganizationChairman;

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
            $extension1 = $pic->getClientOriginalExtension();
            $filename1 = time().rand(000,9999) . '.' . $extension1;
            $path = "business_images";
            $pic->move(public_path($path),$filename1);
            $business->pic = $filename1;
        }

        $business->latitude = $request->latitude;
        $business->longitude = $request->longitude;
        $business->save();

        return redirect()->route('admin.business.list')
                        ->with('success','Business updated successfully');
        // return view('admin.business-edit', compact('business'));
    }

    public function businessLicUpdate(Request $request, $id){
        // dd($request->all());
        $validate = $this->validate($request,[
            'BusinessName'=>'required',
            'BusinessAccro'=>'required',
            'NameBusinesOwner'   => 'required',
            'phoneOne'    => 'required',
            'phoneTwo'    => 'required',
            'emailIfAny'    => 'required',
            'ownership'    => 'required',
            'BusinessHousing'    => 'required',
            'house'    => 'required',
            'Street'    => 'required',
            'Section'    => 'required',
            'Zone'    => 'required',
            'BusinessSize'    => 'required',
            'BusinessLocation'    => 'required',
         ]);

        $business = BusinessLicense::find($id);

        $business->BusinessName = $request->BusinessName;
        $business->BusinessAccro = $request->BusinessAccro;
        $business->NameBusinesOwner = $request->NameBusinesOwner;
        $business->phoneOne = $request->phoneOne;
        $business->phoneTwo = $request->phoneTwo;
        $business->emailIfAny = $request->emailIfAny;
        $business->ownership = $request->ownership;
        $business->BusinessHousing = $request->BusinessHousing;
        $business->house = $request->house;
        $business->Street = $request->Street;
        $business->Section = $request->Section;
        $business->Zone = $request->Zone;
        $business->BusinessType = $request->BusinessType;
        $business->BusinessCategory = $request->BusinessCategory;
        $business->BusinessSize = $request->BusinessSize;
        $business->BusinessLocation = $request->BusinessLocation;
        $business->BusinessLicenseCategory = $request->BusinessLicenseCategory;
        $business->LicenseFee = $request->LicenseFee;

        $business->save();

        return redirect()->route('admin.business.list')
                        ->with('success','Business updated successfully');
        // return view('admin.business-edit', compact('business'));
    }

    public function index(Request $request)
    {
        $query = District::query();
        if (request()->user()->hasRole('Super Admin')) {
		$query = $query->where('id',13);
        } else {
            $query = $query->where('id', request()->user()->assign_district_id);
        }
        //dd(District::where('id',13)->get());
        return (new DistrictsGrid())
            ->create(['query' => $query, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.district.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $district = District::find($id);

        return view('admin.district.edit', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $request->validate( [
            'name' => ['required', 'max:70'],
            'council_name' => ['required', 'max:150'],
            'council_short_name' => ['required', 'max:70'],
            'council_address' => ['required', 'max:70'],
            'penalties_note' => ['nullable', 'max:250'],
            'warning_note' => ['nullable', 'max:250'],
            'collection_point.*' => ['nullable'],
            'collection_point2.*' => ['nullable'],
            'bank_details.*' => ['nullable'],
            'enquiries_email' => ['required', 'email', 'max:70'],
            'enquiries_phone' => ['required', 'max:250'],
            'enquiries_phone2' => ['nullable', 'max:250'],
            'feedback' => ['nullable', 'max:500'],
            'primary_logo' => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'secondary_logo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:4096'],
            'chif_administrator_sign' => ['nullable', 'mimes:jpg,jpeg,png', 'max:4096'],
            'ceo_sign' => ['nullable', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);
        $district = District::findOrFail($id);
        $district->council_name = $request->council_name;
        $district->council_short_name = $request->council_short_name;
        $district->council_address = $request->council_address;
        $district->penalties_note = $request->penalties_note;
        $district->warning_note = $request->warning_note;
        $district->collection_point = ($request->collection_point);
        $district->collection_point2 = ($request->collection_point2);
        $district->bank_details = ($request->bank_details);
        $district->enquiries_email = $request->enquiries_email;
        $district->enquiries_phone = $request->enquiries_phone;
        $district->enquiries_phone2 = $request->enquiries_phone2;
        $district->feedback = $request->feedback;
        $district->sq_meter_value = $request->sq_meter_value;

        if ($request->hasFile('primary_logo')) {
            $district->primary_logo = $request->primary_logo->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('secondary_logo')) {
            $district->secondary_logo = $request->secondary_logo->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('chif_administrator_sign')) {
            $district->chif_administrator_sign = $request->chif_administrator_sign->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('ceo_sign')) {
            $district->ceo_sign = $request->ceo_sign->store(District::IMAGE_PATH);
        }

        $district->save();

        return redirect()->back()->with('success', 'District successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (request()->user()->hasRole('Super Admin')) {
            $district = District::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.districts.index')->with('success', 'District Deleted Successfully !');
        } else {
            return Redirect()->route('admin.districts.index')->with('error', "You can't delete district. ");
        }
    }

    public function businessEditRegistration($id)
    {
        $businessLic = BusinessLicense::where('BusinessRegId',$id)->first();
        if(!$businessLic)
        {
            return redirect()->back();
        }
        if($businessLic->payment_status == "paid")
        {
            return redirect()->back()->with('error','Business Registration is paid, you cant edit now');
        }
        return view("admin.district.edit_registration")->with(compact('businessLic'));
    }

    public function businessUpdateRegistration(Request $request)
    {
        $businessLic = BusinessLicense::find($request->id);
        $businessLic->BusinessType = $request->BusinessType;
        $businessLic->BusinessCategory = $request->BusinessCategory;
        $businessLic->save();
        return redirect('back-admin/business-view/'.$businessLic->BusinessRegId)->with('success', 'Business Registration Updated Successfully!');
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

    public function downloadCertificates(Request $request)
    {
        $method =  $request->method;
        $businessIds = $request->business_ids;

        if($method == "registration")
        {
            // Create a temporary folder to store the PDF files
            $tempFolderPath = public_path('certificates_new');
            if (!File::exists($tempFolderPath)) {
                File::makeDirectory($tempFolderPath, 0755, true);
            }

            // Loop through the business IDs and generate the PDF files
            foreach ($businessIds as $business_id) {
                $businessLic = BusinessLicense::where('BusinessRegId', $business_id)->first();
                $payment = PaymentHistory::where('business_id', $business_id)->where('type', 'registration')->first();

                if ($payment) {
                    $business = BusinessReg::find($payment->business_id);

                    if ($businessLic->BusinessType == "normal") {
                        $businessType = "Normal";
                    } elseif ($businessLic->BusinessType == "non_profit") {
                        $businessType = "Non Profit";
                    } else {
                        $businessType = "-";
                    }

                    $data = [
                        'business_id' => $business->id,
                        'business_name' => $businessLic->BusinessName,
                        'address' => $businessLic->BusinessLocation,
                        'type' => $businessType,
                        'category' => $businessLic->category->title,
                        'price' => $businessLic->category->price,
                        'month' => date('M', $payment->dateTime),
                        'year' => date('Y', $payment->dateTime),
                        'currentDate' => date('d/M/Y', strtotime(date('Y-m-d')))
                    ];

                    $pdf = PDF::loadView('admin.users.certificate', $data)->setPaper('a4', 'landscape');

                    // Set the filename for the PDF
                    $filename = 'Registration Certification_' . $business_id . '.pdf';

                    // Save the PDF to the temporary folder
                    $pdf->save($tempFolderPath . '/' . $filename);
                }
            }

            // Create a unique zip filename
            $zipFilename = 'certificates_' . time() . '.zip';
            $zipFilePath = $tempFolderPath . '/' . $zipFilename;

            // Create a new zip archive
            $zip = new ZipArchive;
            $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add the PDF files to the zip archive
            $pdfFiles = File::allFiles($tempFolderPath);
            foreach ($pdfFiles as $file) {
                $zip->addFile($file->getPathname(), $file->getFilename());
            }

            $zip->close();

            // Set the appropriate headers for the zip file download
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $zipFilename . '"',
            ];

            // Send the zip file as a download response
            return response()->download($zipFilePath, $zipFilename, $headers);
        }
        elseif($method == "license")
        {
            // Create a temporary folder to store the PDF files
            $tempFolderPath = public_path('license_new');
            if (!File::exists($tempFolderPath)) {
                File::makeDirectory($tempFolderPath, 0755, true);
            }

            foreach ($businessIds as $business_id) {
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
                    $businessLicenseCategoryName = '';
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
                    else
                    {
                        $percent = 0;
                        $BusinessLocation = "";
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
                    "AmountPaid"    => !empty($payment->amount_paid) ?$payment->amount_paid: "N/A",
                    "PrintDate"     => date('d/m/Y'),
                    "percentBaseRate" => number_format($BusinessLocationPrice,2),
                    "image1" => $business->Signature,
                    "image2" => $business->pic,
                ];
                $newArray = array_merge($data,$extendedData);

                $pdf = PDF::loadView('admin.users.license_certificate', $newArray);
                // Set the filename for the PDF
                $filename = 'License_' . $business_id . '.pdf';

                // Save the PDF to the temporary folder
                $pdf->save($tempFolderPath . '/' . $filename);
            }

            // Create a unique zip filename
            $zipFilename = 'license_' . time() . '.zip';
            $zipFilePath = $tempFolderPath . '/' . $zipFilename;

            // Create a new zip archive
            $zip = new ZipArchive;
            $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add the PDF files to the zip archive
            $pdfFiles = File::allFiles($tempFolderPath);
            foreach ($pdfFiles as $file) {
                $zip->addFile($file->getPathname(), $file->getFilename());
            }

            $zip->close();

            // Set the appropriate headers for the zip file download
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $zipFilename . '"',
            ];

            // Send the zip file as a download response
            return response()->download($zipFilePath, $zipFilename, $headers);
        }
    }

    public function downloadAllCertificates(Request $request)
    {
        $method =  $request->method;
        $businessIds = $request->business_ids;
        if($method == "registration")
        {
            foreach ($businessIds as $business_id) {
                $businessLic = BusinessLicense::where('BusinessRegId', $business_id)->first();
                $payment = PaymentHistory::where('business_id', $business_id)->where('type', 'registration')->first();

                if ($payment) {
                    $business = BusinessReg::find($payment->business_id);

                    if ($businessLic->BusinessType == "normal") {
                        $businessType = "Normal";
                    } elseif ($businessLic->BusinessType == "non_profit") {
                        $businessType = "Non Profit";
                    } else {
                        $businessType = "-";
                    }

                    $data[] = [
                        'business_id' => $business->id,
                        'business_name' => $businessLic->BusinessName,
                        'location' => $businessLic->BusinessLocation,
                        'type' => $businessType,
                        'category' => $businessLic->category->title,
                        'price' => $businessLic->category->price,
                        'month' => date('M', $payment->dateTime),
                        'year' => date('Y', $payment->dateTime),
                        'currentDate' => date('d/M/Y', strtotime(date('Y-m-d'))),
                        'address' => $business->organizationMainContactAddress,
                    ];
                }

            }
            if($payment)
            {
                $certificates = $data;

                $pdf = PDF::loadView('admin.users.certificate_new', compact('certificates'))->setPaper('a4', 'landscape');

                return $pdf->download("RegistrationCertificates.pdf");
            }
            else
            {
                return back()->with("error","Business Registration Fee Not paid yet");
            }
        }
        elseif($method == "license")
        {
            foreach ($businessIds as $index => $business_id) {
                $business = BusinessReg::find($business_id);
                $payment = LicenseAmountHistory::where("business_id",$business->id)->first();
                if($payment)
                {
                    $extendedData[$index] = [
                        "type"  => "business_id",
                        'due'   => !empty($payment->due) ? number_format($payment->due,2) : 0.00,
                        'dateTime'  => !empty($payment->dateTime) ? $payment->dateTime : date("Y"),
                        'assessment_amount' => !empty($payment->assessment_amount) ? number_format($payment->assessment_amount,2) : 0.00,
                        'assessment_arrears'    =>  !empty($payment->assessment_arrears) ? number_format($payment->assessment_arrears,2) : 0.00,
                        'plenty'    =>  !empty($payment->plenty) ? number_format($payment->plenty,2) : 0.00,
                    ];
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
                        $businessLicenseCategoryName = '';
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
                        else
                        {
                            $percent = 0;
                            $BusinessLocation = "";
                        }
                        $BusinessLocationPrice = $businessLicenseCategoryPrice * ($percent/100);
                    }
                    $data[$index] = [
                        "BusinessID"    => $business->id,
                        'BusinessName' => $businessLic->BusinessName ? $businessLic->BusinessName : "N/A",
                        "BusinessType"  => $businessLic->BusinessType ? ucwords($businessLic->BusinessType) : "N/A",
                        "RegistrationDate" => date('d/m/Y',strtotime($business->created_at)),
                        "OwnerName" => $businessLic->NameBusinesOwner ? $businessLic->NameBusinesOwner : "N/A",
                        "Phone" => $businessLic->phoneOne ? $businessLic->phoneOne : "N/A",
                        "Email" => $businessLic->emailIfAny ? $businessLic->emailIfAny : "N/A",
                        "Zone" => $businessLic->Zone ? $businessLic->Zone : "N/A",
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
                    $newArray[$index] = array_merge($data{$index},$extendedData[$index]);
                }
            }
            if($payment)
            {
                $certificates = $newArray;

                $pdf = PDF::loadView('admin.users.license_certificate_new', compact('certificates'));

                return $pdf->download("License.pdf");
            }
            else
            {
                return back()->with("error","There is no License Demand Notice for this Business");
            }
        }
    }

    public function checkRegistrationPaymentStatus($business_id)
    {
        $business = BusinessLicense::where('BusinessRegId',$business_id)->first();
        if($business)
        {
            return $business->payment_status;
        }
        return 'unpaid';
    }

    public function changeEstablishmentDate(Request $request)
    {
        $business = BusinessReg::find($request->id);
        $business->dateOfEstablishment = $request->date;
        $business->save();
        return back()->with('success','Establishment Date changed successfully');
    }

    public function changeRegDate(Request $request)
    {
        $business = BusinessReg::find($request->id);
        $businessLic = BusinessLicense::where('BusinessRegId','=',$request->id)->first();
        if($businessLic->payment_status == "unpaid")
        {
            return back()->with('error','Kindly pay your Registration First');
        }
        $business->created_at = $request->date;
        $business->save();
        if($business->businessLic->BusinessType == "normal")
        {
            $calculateYearDifference = $this->calculateYearDifference(date("Y",strtotime($request->date)),date("Y"));
            if($calculateYearDifference > 0)
            {
                $date = new DateTime($request->date);
                $end_date = date("Y-m-d");
                for ($i = 0; $i <= $calculateYearDifference; $i++) {
                    $latestPayment = PaymentHistory::where('type', 'license')->where('business_id', $business->id)->latest()->first();
                    // return $i;
                    if($i == 0)
                    {
                        if($latestPayment)
                        {
                            $backup_payment_history = new BackupPaymentHistory;
                            $backup_payment_history->business_id = $business->id;
                            $backup_payment_history->dateTime = date("Y",$latestPayment->dateTime);
                            $backup_payment_history->assessment_amount = $latestPayment->assessment_amount;
                            $backup_payment_history->assessment_arrears = $latestPayment->assessment_arrears;
                            $backup_payment_history->plenty = $latestPayment->plenty;
                            $backup_payment_history->amount_paid = $latestPayment->amount_paid;
                            $backup_payment_history->due = $latestPayment->due;
                            $backup_payment_history->save();
                            PaymentHistory::where('type', 'license')->where('business_id', $business->id)->delete();
                            LicenseAmountHistory::where('business_id', $business->id)->delete();
                            $latestPayment = false;
                        }
                    }
                    if($latestPayment)
                    {
                        $previousYear = date('Y',$latestPayment->dateTime);
                        $currentYear = $date->format('Y');
                        $backup_payment_history = BackupPaymentHistory::where('business_id', $business->id)->where("dateTime",$date->format('Y'))->latest()->first();

                            $dateTime = strtotime($date->format('Y-m-d'));
                            $payment = new PaymentHistory;
                            $payment->business_id = $business->id;
                            $payment->type = 'license';
                            $payment->payer_name = '';
                            $payment->pay_type = '';
                            $payment->pay_taken_by = '';
                            $payment->dateTime = $dateTime;

                            $assessment_amount = number_format2Dec($latestPayment->assessment_amount);
                            $assessment_arrears = number_format2Dec($latestPayment->due);
                            if($latestPayment->due == 0)
                            {
                                $pelanty = 0;
                            }
                            else
                            {
                                // $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
                                $pelanty = ((25/100)*$assessment_arrears);
                                $pelanty = number_format2Dec($pelanty);
                            }
                            // $due = $pelanty + $assessment_amount;
                            $due = $pelanty + $assessment_amount + $assessment_arrears;
                            $due = number_format2Dec($due);
                            $amount_paid = 0;

                            if(!empty($backup_payment_history) AND $backup_payment_history->dateTime == $date->format('Y'))
                            {
                                if($backup_payment_history->due == 0)
                                {
                                    $pelanty = 0;
                                }
                                else
                                {
                                    // $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
                                    $pelanty = ((25/100)*$assessment_arrears);
                                    $pelanty = number_format2Dec($pelanty);
                                }

                                $backup_payment_history_due = $backup_payment_history->due;
                                if(!empty($backup_payment_history->amount_paid) AND $backup_payment_history->amount_paid != 0)
                                {
                                    $due = $pelanty + $assessment_amount + $assessment_arrears - $backup_payment_history->amount_paid;
                                }
                                else
                                {
                                    $due = $pelanty + $assessment_amount + $assessment_arrears;
                                }
                                $due = number_format2Dec($due);

                                $amount_paid = $backup_payment_history->amount_paid - $latestPayment->amount_paid;
                                $amount_paid = number_format2Dec($amount_paid);
                            }


                            $payment->assessment_amount = $assessment_amount;
                            $payment->assessment_arrears = $assessment_arrears;
                            $payment->plenty = $pelanty;
                            $payment->amount_paid = $amount_paid;
                            $payment->price = $due;
                            $payment->due = $due;
                            $payment->created_at =$date->format('Y-m-d');
                            $payment->save();

                            $licenseAmountHistory = [
                                'business_id'   => $business->id,
                                'dateTime'  => $dateTime,
                                'assessment_amount' =>  $assessment_amount,
                                'assessment_arrears'    => $assessment_arrears,
                                'plenty'    => $pelanty,
                                'amount_paid'   => $amount_paid,
                                'due'   => $due
                            ];
                            $this->licenseAmountHistory($licenseAmountHistory);
                    }
                    else
                    {
                        $dateTime = strtotime($date->format('Y-m-d'));
                        $businessLic = BusinessLicense::where('BusinessRegId',$business->id)->first();

                        $payment = new PaymentHistory;
                        $payment->type = "license";
                        $payment->business_id = $business->id;
                        $payment->price = number_format2Dec($businessLic->LicenseFee);
                        $payment->payer_name = '';
                        $payment->pay_type = '';
                        $payment->pay_taken_by = '';
                        $payment->dateTime = $dateTime;
                        $payment->is_paid = 0;
                        $assessment_amount = number_format2Dec($businessLic->LicenseFee);
                        $due = $assessment_amount - 0;
                        $payment->assessment_amount = $assessment_amount;
                        $assessment_arrears = 0;
                        $payment->assessment_arrears = $assessment_arrears;
                        $pelanty = 0;
                        $payment->plenty = $pelanty;
                        $payment->amount_paid = 0;
                        $payment->price = $pelanty + 0;
                        $payment->due = $due;
                        $payment->created_at =$date->format('Y-m-d');
                        $payment->save();

                        $licenseAmountHistory = [
                            'business_id'   => $business->id,
                            'dateTime'  => $dateTime,
                            'assessment_amount' =>  $assessment_amount,
                            'assessment_arrears'    => $assessment_arrears,
                            'plenty'    => $pelanty,
                            'amount_paid'   => 0,
                            'due'   => $due
                        ];
                        $this->licenseAmountHistory($licenseAmountHistory);
                    }
                    $date->modify('+1 year');
                }
            }
        }

        return back()->with('success','Registration Date changed successfully');
    }

    public function calculateYearDifference($year1, $year2) {
        $date1 = new DateTime("$year1-01-01");
        $date2 = new DateTime("$year2-01-01");

        $interval = $date1->diff($date2);
        $years = $interval->y;

        return $years;
    }
}
