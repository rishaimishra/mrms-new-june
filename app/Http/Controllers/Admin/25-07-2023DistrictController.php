<?php

namespace App\Http\Controllers\Admin;

use PDF;
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
use App\Grids\AdminUsersGrid;
use App\LicenseAmountHistory;
use App\BusinessLicenseCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

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
            // dd($previousYear);
            // dd($currentYear);
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
                    $assessment_amount = $latestPayment->assessment_amount;
                    // $assessment_arrears = $businessLic->LicenseFee - $latestPayment->due;
                    $assessment_arrears = $latestPayment->due;
                    if($latestPayment->due == 0)
                    {
                        $pelanty = 0;
                    }
                    else
                    {
                        $pelanty = $assessment_arrears + ((25/100)*$assessment_arrears);
                    }
                    $due = $pelanty + $latestPayment->assessment_amount;

                    $payment->assessment_amount = $assessment_amount;
                    $payment->assessment_arrears = $assessment_arrears;
                    $payment->plenty = $pelanty;
                    $payment->amount_paid = 0;
                    $payment->price = $pelanty + $latestPayment->assessment_amount;
                    $payment->due = $pelanty + $latestPayment->assessment_amount;
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

    public function businessEdit($id){
        $business = BusinessReg::find($id);

        return view('admin.business-edit', compact('business'));
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
            $certificates = $data;

            $pdf = PDF::loadView('admin.users.certificate_new', compact('certificates'))->setPaper('a4', 'landscape');

            return $pdf->download("RegistrationCertificates.pdf");
        }
        elseif($method == "license")
        {
            foreach ($businessIds as $index => $business_id) {
                $business = BusinessReg::find($business_id);
                $payment = LicenseAmountHistory::where("business_id",$business->id)->first();
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
            $certificates = $newArray;

            $pdf = PDF::loadView('admin.users.license_certificate_new', compact('certificates'));

            return $pdf->download("License.pdf");
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
}
