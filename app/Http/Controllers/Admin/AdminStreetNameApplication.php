<?php

namespace App\Http\Controllers\Admin;

use App\Ward;
use App\Community;
use App\Constituency;
use App\StreetCategory;
use Illuminate\Http\Request;
use App\StreetNameApplicationForm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdminStreetNameApplication extends Controller
{
    public function index($bit){
        if($bit == 0)
        {
            $query = new StreetNameApplicationForm;
            if(!empty(request()->date))
            {
                $date = request()->input('date');
                $explode = explode('-',$date);
                $start_date = date('Y-m-d',strtotime($explode[0]));
                $end_date = date('Y-m-d',strtotime($explode[1]));
                if($start_date == $end_date)
                {
                    $query->where('created_at', $start_date);
                }
                else
                {
                    $query = $query->whereBetween('created_at', [$start_date, $end_date]);
                }
            }
            if(!empty(request()->tracking_no))
            {
                $query = $query->where('tracking_no', request()->tracking_no);
            }

            if(!empty(request()->surname))
            {
                $query = $query->where('surname', request()->surname);
            }
            if(!empty(request()->first_name))
            {
                $query = $query->where('first_name', request()->first_name);
            }

            if(!empty(request()->constituency))
            {
                $query = $query->where('constituency', request()->constituency);
            }

            if(!empty(request()->ward))
            {
                $query = $query->where('ward', request()->ward);
            }

            if(!empty(request()->name_of_community))
            {
                $query = $query->where('name_of_community', request()->name_of_community);
            }

            if(!empty(request()->street_category))
            {
                $query = $query->where('street_category', request()->street_category);
            }

            $street_name_application_forms = $query->OrderBy('id','DESC')
                ->where('development_officer_status', 1)
                ->where('cheif_officer_status', 1)
                ->where('is_deleted',0)
                ->get();
        }
        else if($bit == 1)
        {
            $street_name_application_forms = StreetNameApplicationForm::where('development_officer_status', 0)
            ->where('cheif_officer_status', 0)
            ->where('form_status','new')
            ->OrderBy('id','DESC')
            ->where('is_deleted',0)
            ->get();
        }
        else if($bit == 2)
        {
            $street_name_application_forms = StreetNameApplicationForm::where('development_officer_status', 1)
            ->where('cheif_officer_status', 0)
            ->OrderBy('id','DESC')
            ->where('is_deleted',0)
            ->get();
        }
        else if($bit == 3)
        {
            $street_name_application_forms = StreetNameApplicationForm::where('development_officer_status', 2)
            ->OrderBy('id','DESC')
            ->where('is_deleted',0)
            ->get();
        }
        else if($bit == 4)
        {
            $street_name_application_forms = StreetNameApplicationForm::where('development_officer_status', 0)
            ->where('form_status','reapplied')
            ->OrderBy('id','DESC')
            ->where('is_deleted',0)
            ->get();
        }
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();
        $street_categories = StreetCategory::where('status','A')->OrderBy('title','ASC')->get();

        return view('admin.street_name_application.index', compact('street_categories','street_name_application_forms','bit','constituencies'));
    }

    public function view($id)
    {
        $street_name_application_form = StreetNameApplicationForm::findOrFail($id);

        return view('admin.street_name_application.view', compact('street_name_application_form'));
    }

    public function edit($id)
    {
        $street_name_application_form = StreetNameApplicationForm::findOrFail($id);
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();

        return view('admin.street_name_application.edit', compact('street_name_application_form','constituencies'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'surname' => 'required',
            'other_name' => 'required',
            'email' => 'required|unique:street_name_application_forms,email,'.$request->id,
            'first_name' => 'required',
            'sex' => 'required',
            'current_residential_address' => 'required',
            'existing_street_drive_name' => 'required',
            'telephone_no' => 'required',
            'constituency_id' => 'required',
            'ward_id' => 'required',
            'name_of_community_id' => 'required',
            'street_drive_name_developed' => 'required',
            'street_drive_name_developed_description' => 'required',
            'near_by_plots.*' => 'required',
            'near_by_houses.*' => 'required',
            'personal_contribution_development_community.*' => 'required',
            'personal_contribution_development_community_future.*' => 'required',
            'approval_from_community.*' => 'required',
            'personal_view_street_drive.*' => 'required',
            'additional_info_council.*' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $count_email = StreetNameApplicationForm::where('email',$request->email)->where('id','!=',$request->id)->count();
        if($count_email > 0)
        {
            return back()->with('error','Email already exist');

        }

        $street_name_application_form = StreetNameApplicationForm::findOrFail($request->id);
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

        return redirect()->route('admin.street_name_application.view',$street_name_application_form->id)->with('success','Street Name Application Form Updated Successfully');
    }

    public function changeApprovalStatus(Request $request)
    {
        $street_name_application_form = StreetNameApplicationForm::find($request->id);
        if($request->type == "development_officer_status")
        {
            $street_name_application_form->development_officer_status = $request->status;
            $street_name_application_form->development_officer_status_reason = $request->reason;
        }
        else
        {
            $street_name_application_form->cheif_officer_status = $request->status;
            $street_name_application_form->cheif_officer_status_reason = $request->reason;
        }
        $street_name_application_form->save();

        return back()->with('success','Approval Status updated successfully');
    }

    public function changeVerificationStatus($id,$type)
    {
        $street_name_application_form = StreetNameApplicationForm::findOrFail($id);
        $street_name_application_form->$type = $street_name_application_form->$type == 0 ? 1 : 0;
        $street_name_application_form->save();

        return back()->with('success','Verification Status changed successfully');
    }

    public function constituency()
    {
        $constituencies = Constituency::where('status','!=','D')->OrderBy('id','DESC')->get();

        return view('admin.street_name_application.settings.constituency.index')->with(compact('constituencies'));
    }

    public function constituencyCreate()
    {
        return view('admin.street_name_application.settings.constituency.create');
    }

    public function constituencyStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:constituencies,title',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $constituency = new Constituency;
        $constituency->title = $request->title;
        $constituency->save();

        return redirect()->route('admin.street_name_application.setting.constituency')->with('success','Constituency added successfully');
    }

    public function constituencyEdit($id)
    {
        $constituency = Constituency::findOrFail($id);

        return view('admin.street_name_application.settings.constituency.edit', compact('constituency'));
    }

    public function constituencyUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:constituencies,title,'.$request->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $constituency = Constituency::find($request->id);
        $constituency->title = $request->title;
        $constituency->save();

        return redirect()->route('admin.street_name_application.setting.constituency')->with('success','Constituency updated successfully');
    }

    public function constituencyDelete($id)
    {
        $constituency = Constituency::findOrFail($id);
        $constituency->status = 'D';
        $constituency->save();

        return back()->with('success','Constituency deleted successfully');
    }

    public function wards()
    {
        $wards = Ward::where('status','!=','D')->OrderBy('id','DESC')->get();

        return view('admin.street_name_application.settings.wards.index')->with(compact('wards'));
    }

    public function wardsCreate()
    {
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();
        return view('admin.street_name_application.settings.wards.create')->with(compact('constituencies'));
    }

    public function wardsStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'constituency_id' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $count = Ward::where('title',$request->title)->where('constituency_id',$request->constituency_id)->count();
        if($count > 0)
        {
            return back()->with('error','Ward already exist');
        }

        $wards = new Ward;
        $wards->constituency_id = $request->constituency_id;
        $wards->title = $request->title;
        $wards->save();

        return redirect()->route('admin.street_name_application.setting.wards')->with('success','Ward added successfully');
    }

    public function wardsEdit($id)
    {
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();
        $ward = Ward::findOrFail($id);

        return view('admin.street_name_application.settings.wards.edit', compact('ward','constituencies'));
    }

    public function wardsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'constituency_id' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $count = Ward::where('id','!=',$request->id)->where('title',$request->title)->where('constituency_id',$request->constituency_id)->count();
        if($count > 0)
        {
            return back()->with('error','Ward already exist');
        }

        $wards = Ward::find($request->id);
        $wards->constituency_id = $request->constituency_id;
        $wards->title = $request->title;
        $wards->save();

        return redirect()->route('admin.street_name_application.setting.wards')->with('success','Ward updated successfully');
    }

    public function wardsDelete($id)
    {
        $ward = Ward::findOrFail($id);
        $ward->status = 'D';
        $ward->save();

        return back()->with('success','Ward deleted successfully');
    }


    public function communities()
    {
        $communities = Community::where('status','!=','D')->OrderBy('id','DESC')->get();

        return view('admin.street_name_application.settings.communities.index')->with(compact('communities'));
    }

    public function communitiesCreate()
    {
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();

        return view('admin.street_name_application.settings.communities.create')->with(compact('constituencies'));
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

    public function communitiesStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'constituency_id' => 'required',
            'ward_id' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $count = Community::where('title',$request->title)->where('ward_id',$request->ward_id)->where('constituency_id',$request->constituency_id)->count();
        if($count > 0)
        {
            return back()->with('error','Community already exist');
        }

        $wards = new Community;
        $wards->constituency_id = $request->constituency_id;
        $wards->ward_id = $request->ward_id;
        $wards->title = $request->title;
        $wards->save();

        return redirect()->route('admin.street_name_application.setting.communities')->with('success','Community added successfully');
    }

    public function communitiesEdit($id)
    {
        $constituencies = Constituency::where('status','A')->OrderBy('title','ASC')->get();
        $community = Community::findOrFail($id);

        return view('admin.street_name_application.settings.communities.edit')->with(compact('constituencies','community'));
    }

    public function communitiesUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'constituency_id' => 'required',
            'ward_id' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $count = Community::where('id','!=',$request->id)->where('title',$request->title)->where('ward_id',$request->ward_id)->where('constituency_id',$request->constituency_id)->count();
        if($count > 0)
        {
            return back()->with('error','Community already exist');
        }
        $community = Community::find($request->id);
        $community->constituency_id = $request->constituency_id;
        $community->ward_id = $request->ward_id;
        $community->title = $request->title;
        $community->save();

        return redirect()->route('admin.street_name_application.setting.communities')->with('success','Community added successfully');
    }

    public function communitiesDelete($id)
    {
        $ward = Community::findOrFail($id);
        $ward->status = 'D';
        $ward->save();

        return back()->with('success','Community deleted successfully');
    }

    public function category()
    {
        $street_category = StreetCategory::where('status','!=','D')->OrderBy('id','DESC')->get();

        return view('admin.street_name_application.settings.category.index')->with(compact('street_category'));
    }

    public function categoryCreate()
    {
        return view('admin.street_name_application.settings.category.create');
    }

    public function categoryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:street_categories,title',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $constituency = new StreetCategory;
        $constituency->title = $request->title;
        $constituency->save();

        return redirect()->route('admin.street_name_application.setting.category')->with('success','Street Category added successfully');
    }

    public function categoryEdit($id)
    {
        $street_category = StreetCategory::findOrFail($id);

        return view('admin.street_name_application.settings.category.edit', compact('street_category'));
    }

    public function categoryUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:street_categories,title,'.$request->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $constituency = StreetCategory::find($request->id);
        $constituency->title = $request->title;
        $constituency->save();

        return redirect()->route('admin.street_name_application.setting.category')->with('success','Street Category updated successfully');
    }

    public function categoryDelete($id)
    {
        $street_category = StreetCategory::findOrFail($id);
        $street_category->status = 'D';
        $street_category->save();

        return back()->with('success','Street Category deleted successfully');
    }
}
