<?php

namespace App\Http\Controllers;

use PDF;
use App\Ward;
use App\Community;
use App\Constituency;
use App\StreetCategory;
use Illuminate\Http\Request;
use App\StreetNameApplication;
use App\StreetNameApplicationForm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StreetNamingAppController extends Controller
{
    public function index()
    {
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();
        $street_categories = StreetCategory::where('status','A')->OrderBy('title','ASC')->get();

        return view('street_name_application.index')->with(compact('constituencies','street_categories'));
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'phone' => 'required',
        //     'email' => 'required|unique:street_name_applications,email',
        //     'amount' => 'required',
        //     'address' => 'required',
        //     'street_address' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator);
        // }

        $street_name_application = StreetNameApplication::where('unique_payment_no',$request->payment_id)->first();
        if(!$street_name_application)
        {
            return back()->with('error','Invalid Payment ID');
        }
        $exist_street_name_application_form = StreetNameApplicationForm::where('street_name_application_id',$street_name_application->id)->first();
        if($exist_street_name_application_form)
        {
            return back()->with('error','Data already submitted against this payment Key');
        }
        $count_email = StreetNameApplicationForm::where('email',$request->email)->count();
        if($count_email > 0)
        {
            return back()->with('error','Email already exist');

        }

        $street_name_application_form = new StreetNameApplicationForm();
        $street_name_application_form->street_name_application_id = $street_name_application->id;
        $street_name_application_form->surname = $request->surname;
        $street_name_application_form->other_name = $request->other_name;
        $street_name_application_form->first_name = $request->first_name;
        $street_name_application_form->email = $request->email;
        $street_name_application_form->sex = $request->sex;
        $street_name_application_form->current_residential_address = $request->current_residential_address;
        $street_name_application_form->existing_street_drive_name = $request->existing_street_drive_name;
        $street_name_application_form->telephone_no = $request->telephone_no;
        $street_name_application_form->name_of_community = $request->name_of_community;
        $street_name_application_form->constituency = $request->constituency;
        $street_name_application_form->ward = $request->ward;
        $street_name_application_form->street_category = $request->street_category;
        $street_name_application_form->street_drive_name_developed = $request->street_drive_name_developed;
        $street_name_application_form->street_drive_name_developed_description = $request->street_drive_name_developed_description;
        $street_name_application_form->personal_contribution_development_community = implode(",",$request->personal_contribution_development_community);
        $street_name_application_form->personal_contribution_development_community_future = implode(",",$request->personal_contribution_development_community_future);
        $street_name_application_form->personal_view_street_drive = implode(",",$request->personal_view_street_drive);
        $street_name_application_form->approval_from_community = implode(",",$request->approval_from_community);
        $street_name_application_form->additional_info_council = implode(",",$request->additional_info_council);
        $street_name_application_form->near_by_houses = implode(",",$request->near_by_houses);
        $street_name_application_form->near_by_plots = implode(",",$request->near_by_plots);
        $street_name_application_form->tracking_no = 'str-'.date("YmdHis");
        $street_name_application_form->save();


        Mail::send('emails.street_name_application_form', compact('street_name_application_form'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Street Name Application Form');
        });

        return back()->with('success','Form Submitted Successfully and here is tracking no '.$street_name_application_form->tracking_no);
    }


    public function payment()
    {
        return view('street_name_application.payment');
    }

    public function paymentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|unique:street_name_applications,email',
            'amount' => 'required',
            'address' => 'required',
            'street_address' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $unique_payment_no = $this->unique_payment_no();

        $street_name_application = new StreetNameApplication();
        $street_name_application->name = $request->name;
        $street_name_application->phone = $request->phone;
        $street_name_application->email = $request->email;
        $street_name_application->amount = $request->amount;
        $street_name_application->address = $request->address;
        $street_name_application->street_address = $request->street_address;
        $street_name_application->unique_payment_no = $unique_payment_no;
        $street_name_application->date = date('Y-m-d H:i:s');
        $street_name_application->save();

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'amount' => $request->amount,
            'address' => $request->address,
            'phone' => $request->phone,
            'street_address' => $request->street_address,
            'unique_payment_no' => $unique_payment_no,
        ];

        $pdf = PDF::loadView('street_name_application.pdf', compact('street_name_application'));
        $attachment = $pdf->output();
        $filename = 'Street Name Application.pdf';

        Mail::send('emails.street_name_appilication', $data, function ($message) use ($request,$attachment,$filename) {
            $message->to($request->email);
            $message->subject('Street Name Application');
            $message->attachData($attachment, $filename);
        });

        return redirect()->route("street_name_application.previewKey",$unique_payment_no)->with('success', 'Payment Key generated successfully.');
    }

    public function previewKey($key)
    {
        return view("street_name_application.preview")->with(compact('key'));
    }

    public function downloadPDF($key)
    {
        $street_name_application = StreetNameApplication::where('unique_payment_no', $key)->first();

        $pdf = PDF::loadView('street_name_application.pdf', compact('street_name_application'));
        return $pdf->download("Street Name Application.pdf");

    }

    private function unique_payment_no()
    {
        $paymnet_no = rand(0000,999999999);
        $count = StreetNameApplication::where('unique_payment_no', $paymnet_no)->count();
        if($count > 0)
        {
            $paymnet_no = $paymnet_no.''.$count;
        }

        return $paymnet_no;
    }

    public function sendStreetNameAppEditFormEmail(Request $request)
    {
        // return $request;
        $street_name_application_form = StreetNameApplicationForm::where('email',$request->email)->first();
        if(!$street_name_application_form)
        {
            return back()->with('error','Street Name Application Form Not Found');
        }
        if($street_name_application_form->email != $request->email)
        {
            return back()->with('error','Email is not valid for this Street Name Application');
        }

        if($request->method != "print")
        {
            $encrptedBusinessID = encrypt($street_name_application_form->id);
            $email = $street_name_application_form->email;

            $moduleName = implode(",",$request->moduleName);
            $link = route("street_name_application.form.updateStreetNameApp",$encrptedBusinessID);
            $link = $link.'&module='.$moduleName;

            $data = [
                'subject' => 'Update Street Name Application Form',
                'message' => 'Please click the link below to update your information.',
                'link' => $link,
            ];

            Mail::send([], [], function ($message) use ($email, $data) {
                $message->to($email)
                        ->subject('Update Street Name Application Form')
                        ->setBody(view('admin.emails.send_email_business_update', $data)->render(), 'text/html');
            });

            // return back()->with('success','Link has been sent to your email. here the link is '.$link);
            return back()->with('success','Link has been sent to your email.');
        }
        else
        {
            if($street_name_application_form->development_officer_status == 0 AND $street_name_application_form->cheif_officer_status == 0)
            {
                return back()->with('error','Your Street Name Application Form is not approved yet');
            }


            $pdf = PDF::loadView('emails.street_name_application_form', compact('street_name_application_form'));
            $attachment = $pdf->output();
            $filename = 'Street Name Application Form.pdf';
            // return $pdf->download($filename);
            // Mail::send('emails.business-detail', $details, function ($message) use ($businessLic,$attachment,$filename) {
            //     $message->to($businessLic->emailIfAny);
            //     $message->subject('Get Business Detail');
            //     $message->attachData($attachment, $filename);
            // });
            Mail::send('emails.street_name_application_form', compact('street_name_application_form'), function ($message) use ($request,$attachment,$filename) {
                $message->to($request->email);
                $message->subject('Street Name Application Form');
                $message->attachData($attachment, $filename);
            });


            return back()->with('success','Print has been sent to your email.');
        }

    }

    public function updateStreetNameApp($token)
    {
        $explode = explode("&module=",$token);
        $token = $explode[0];
        $moduleName = explode(",",$explode[1]);

        $street_name_application_form_id = decrypt($token);
        $street_name_application_form = StreetNameApplicationForm::where('id',$street_name_application_form_id)->first();

        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();
        $street_categories = StreetCategory::where('status','A')->OrderBy('title','ASC')->get();
        return view("street_name_application.edit")->with(compact('street_categories','street_name_application_form','moduleName','constituencies'));
    }

    public function reapplied($token)
    {
        $street_name_application_form_id = decrypt($token);
        $street_name_application_form = StreetNameApplicationForm::where('id',$street_name_application_form_id)->first();
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();

        $street_categories = StreetCategory::where('status','A')->OrderBy('title','ASC')->get();
        return view("street_name_application.reapplied")->with(compact('street_categories','street_name_application_form','constituencies'));
    }

    public function reappliedStore(Request $request)
    {
        $count_email = StreetNameApplicationForm::where('email',$request->email)->where('id','!=',$request->id)->count();
        if($count_email > 0)
        {
            return back()->with('error','Email already exist');

        }

        $street_name_application_form = StreetNameApplicationForm::find($request->id);
        $street_name_application_form->surname = $request->surname;
        $street_name_application_form->other_name = $request->other_name;
        $street_name_application_form->first_name = $request->first_name;
        $street_name_application_form->email = $request->email;
        $street_name_application_form->sex = $request->sex;
        $street_name_application_form->current_residential_address = $request->current_residential_address;
        $street_name_application_form->existing_street_drive_name = $request->existing_street_drive_name;
        $street_name_application_form->telephone_no = $request->telephone_no;
        $street_name_application_form->name_of_community = $request->name_of_community;
        $street_name_application_form->constituency = $request->constituency;
        $street_name_application_form->ward = $request->ward;
        $street_name_application_form->street_category = $request->street_category;
        $street_name_application_form->street_drive_name_developed = $request->street_drive_name_developed;
        $street_name_application_form->street_drive_name_developed_description = $request->street_drive_name_developed_description;
        $street_name_application_form->personal_contribution_development_community = implode(",",$request->personal_contribution_development_community);
        $street_name_application_form->personal_contribution_development_community_future = implode(",",$request->personal_contribution_development_community_future);
        $street_name_application_form->personal_view_street_drive = implode(",",$request->personal_view_street_drive);
        $street_name_application_form->approval_from_community = implode(",",$request->approval_from_community);
        $street_name_application_form->additional_info_council = implode(",",$request->additional_info_council);
        $street_name_application_form->near_by_houses = implode(",",$request->near_by_houses);
        $street_name_application_form->near_by_plots = implode(",",$request->near_by_plots);
        $street_name_application_form->development_officer_status = 0;
        $street_name_application_form->cheif_officer_status = 0;
        $street_name_application_form->development_officer_status_reason = null;
        $street_name_application_form->cheif_officer_status_reason = null;
        $street_name_application_form->form_status = 'reapplied';
        $street_name_application_form->reapplied_date = date("Y-m-d H:i:s");
        $street_name_application_form->save();

        return back()->with('success','Form Submitted Successfully');
    }

    public function update(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'phone' => 'required',
        //     'email' => 'required|unique:street_name_applications,email',
        //     'amount' => 'required',
        //     'address' => 'required',
        //     'street_address' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator);
        // }
        $count_email = StreetNameApplicationForm::where('email',$request->email)->where('id','!=',$request->id)->count();
        if($count_email > 0)
        {
            return back()->with('error','Email already exist');

        }

        $street_name_application_form = StreetNameApplicationForm::find($request->id);
        $street_name_application_form->surname = $request->surname;
        $street_name_application_form->other_name = $request->other_name;
        $street_name_application_form->first_name = $request->first_name;
        $street_name_application_form->email = $request->email;
        $street_name_application_form->sex = $request->sex;
        $street_name_application_form->current_residential_address = $request->current_residential_address;
        $street_name_application_form->existing_street_drive_name = $request->existing_street_drive_name;
        $street_name_application_form->telephone_no = $request->telephone_no;
        $street_name_application_form->name_of_community = $request->name_of_community;
        $street_name_application_form->constituency = $request->constituency;
        $street_name_application_form->ward = $request->ward;
        $street_name_application_form->street_category = $request->street_category;
        $street_name_application_form->street_drive_name_developed = $request->street_drive_name_developed;
        $street_name_application_form->street_drive_name_developed_description = $request->street_drive_name_developed_description;
        $street_name_application_form->personal_contribution_development_community = implode(",",$request->personal_contribution_development_community);
        $street_name_application_form->personal_contribution_development_community_future = implode(",",$request->personal_contribution_development_community_future);
        $street_name_application_form->personal_view_street_drive = implode(",",$request->personal_view_street_drive);
        $street_name_application_form->approval_from_community = implode(",",$request->approval_from_community);
        $street_name_application_form->additional_info_council = implode(",",$request->additional_info_council);
        $street_name_application_form->near_by_houses = implode(",",$request->near_by_houses);
        $street_name_application_form->near_by_plots = implode(",",$request->near_by_plots);
        $street_name_application_form->save();

        return back()->with('success','Form Submitted Successfully');
    }

    public function trackApplication(Request $request)
    {
        $street_name_application_form = StreetNameApplicationForm::where('tracking_no',$request->tracking_no)->first();
        if(!$street_name_application_form)
        {
            $message = 'Tracking No is not valid';
        }
        else
        {
            if ($street_name_application_form->development_officer_status == 0) {
                $work_officer_status =  'Pending';
            } elseif ($street_name_application_form->development_officer_status == 1) {
                $work_officer_status = 'Approved';
            } elseif ($street_name_application_form->development_officer_status == 2) {
                $work_officer_status = 'Rejected';
            }
            if ($street_name_application_form->cheif_officer_status == 0) {
                $cheif_officer_status =  'Pending';
            } elseif ($street_name_application_form->cheif_officer_status == 1) {
                $cheif_officer_status = 'Approved';
            } elseif ($street_name_application_form->cheif_officer_status == 2) {
                $cheif_officer_status = 'Rejected';
            }

            $message = '<p><strong>Work officer Status:</strong> '.$work_officer_status.'</p>';
            if($street_name_application_form->development_officer_status == 2){
                $message .= '<p><strong>Work officer Reason:</strong> '.$street_name_application_form->development_officer_status_reason.'</p>';
            }
            $message .= '<p><strong>CA officer Status:</strong> '.$cheif_officer_status.'</p>';
            if($street_name_application_form->cheif_officer_status == 2){
                $message .= '<p><strong>CA officer Reason:</strong> '.$street_name_application_form->cheif_officer_status_reason.'</p>';
            }
            if($street_name_application_form->development_officer_status == 2 OR $street_name_application_form->cheif_officer_status == 2){
                $encrptedBusinessID = encrypt($street_name_application_form->id);
                $link = route("street_name_application.form.reapplied",$encrptedBusinessID);

                $message .= '<a href="'.$link.'" class="btn btn-success">Re-apply Application</a>';
            }
        }

        $html = '<div class="row">
        <div class="col-md-12">
            '.$message.'
            </div>
        </div>';

        return $html;
    }

    public function getWardsByCommunity($id)
    {
        $wards = Ward::where('status','A')->where('constituency_id',$id)->OrderBy('title','ASC')->get();

        return $wards;
    }

    public function getCommunityByWards($id)
    {
        $community = Community::where('status','A')->where('ward_id',$id)->get();

        return $community;
    }
}
