<?php

namespace App\Http\Controllers\Admin;

use App\BusinessReg;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Grids\AdminUsersGrid;
use App\UserAssignedBusiness;
use App\UserAssignedProperty;
use App\StreetNameApplicationForm;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\UserAssignedStreetApplication;
use Illuminate\Support\Facades\Validator;
use App\Models\BoundaryDelimitation;

class BusinessUserController extends Controller
{
    public function index()
    {
        $role = Role::where('name','BusinessUser')->first();
        $users = AdminUser::where('super_admin',$role->id)->where('is_active',1)->OrderBy('id', 'DESC')->get();
        return view("admin.business_user.index")->with(compact('users'));
    }

    public function create()
    {
        $businesses = BusinessReg::select('*')
        ->OrderBy('business_registration.id','DESC')
        ->get();

        $street_name_application_forms = StreetNameApplicationForm::where('is_deleted','0')->get();

        if (request()->user()->hasRole('Super Admin')) {
            $wards = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
        } else {
            $wards = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
        }

        return view("admin.business_user.create")->with(compact('businesses','street_name_application_forms','wards'));
    }

    public function store(Request $request)
    {
        // return $request;
        $role = Role::where('name','BusinessUser')->first();
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' =>  'string',
            'gender' => 'required',
            'type' => 'required',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:6',
            'username' => 'required|unique:admin_users,username',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }

        $user = new AdminUser();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->ward = '';
        $user->constituency = '';
        $user->section = '';
        $user->chiefdom = '';
        $user->district = '';
        $user->province = '';
        $user->street_name = '';
        $user->street_number = '';
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->is_active = 1;
        $user->super_admin = $role->id;
        $user->type = $request->type;

        if(!empty($request->module_access))
        {
            $data = $this->moduelAccess();
            $newArr = [];
            foreach ($data as $response) {
                $newArr[$response] = in_array($response, $request->module_access) ? 1 : 0;
            }

            $user->access = json_encode($newArr);
        }

        if(!empty($request->street_module_access))
        {
            $data = $this->streetModuelAccess();
            $newArr = [];
            foreach ($data as $response) {
                $newArr[$response] = in_array($response, $request->street_module_access) ? 1 : 0;
            }

            $user->street_module_access = json_encode($newArr);
            $user->user_type = "street";
        }

        if(!empty($request->property_module_access))
        {
            $data = $this->propertyModuelAccess();
            $newArr = [];
            foreach ($data as $response) {
                $newArr[$response] = in_array($response, $request->property_module_access) ? 1 : 0;
            }

            $user->property_module_access = json_encode($newArr);
            $user->user_type = "property";
        }

        $user->save();

        $role = Role::findByName('Super Admin Cus');
        if(!$role)
        {
            Role::create(['name' => 'Super Admin Cus']);
        }
        $user->assignRole($role);

        if(!empty($request->business_id))
        {
            foreach($request->business_id as $businessID)
            {
                $count = UserAssignedBusiness::where('user_id',$user->id)->where('business_id',$businessID)->first();
                if($count == 0)
                {
                    $UserAssignedBusiness = new UserAssignedBusiness;
                    $UserAssignedBusiness->user_id = $user->id;
                    $UserAssignedBusiness->business_id = $businessID;
                    $UserAssignedBusiness->save();
                }
            }
        }

        if(!empty($request->street_id))
        {
            foreach($request->street_id as $street_id)
            {
                $count = UserAssignedStreetApplication::where('user_id',$user->id)->where('street_application_id',$street_id)->first();
                if($count == 0)
                {
                    $UserAssignedStreetApp = new UserAssignedStreetApplication;
                    $UserAssignedStreetApp->user_id = $user->id;
                    $UserAssignedStreetApp->street_application_id = $street_id;
                    $UserAssignedStreetApp->save();
                }
            }
        }

        if(!empty($request->property_id))
        {
            foreach($request->property_id as $property_id)
            {
                $count = UserAssignedProperty::where('user_id',$user->id)->where('ward_id',$property_id)->first();
                if($count == 0)
                {
                    $UserAssignedStreetApp = new UserAssignedProperty;
                    $UserAssignedStreetApp->user_id = $user->id;
                    $UserAssignedStreetApp->ward_id = $property_id;
                    $UserAssignedStreetApp->save();
                }
            }
        }

        return Redirect()->route("admin.business.index-user")->with('success', 'User Created Successfully !');
    }

    public function edit($id)
    {
        $user = AdminUser::find($id);

        $keysWithValueOne = [];
        if(!empty($user->access))
        {
            $data = $this->moduelAccess();
            $jsonObj = json_decode($user->access, true);
            if (!isset($jsonObj["non_profit_business"])) {
                $jsonObj["non_profit_business"] = 0;
            }

            if (!isset($jsonObj["profit_based_business"])) {
                $jsonObj["profit_based_business"] = 0;
            }

            foreach ($data as $item) {
                if ($jsonObj[$item] === 1) {
                    $keysWithValueOne[] = $item;
                }
            }
        }

        $street_datakeysWithValueOne = [];
        if(!empty($user->street_module_access))
        {
            $street_data = $this->streetModuelAccess();
            $street_datajsonObj = json_decode($user->street_module_access, true);
            $street_datakeysWithValueOne = [];
            foreach ($street_data as $item) {
                if ($street_datajsonObj[$item] === 1) {
                    $street_datakeysWithValueOne[] = $item;
                }
            }
        }
        
        $property_datakeysWithValueOne = [];
        if(!empty($user->property_module_access))
        {
            $property_data = $this->propertyModuelAccess();
            $property_datajsonObj = json_decode($user->property_module_access, true);
    
            foreach ($property_data as $item) {
                if ($property_datajsonObj[$item] === 1) {
                    $property_datakeysWithValueOne[] = $item;
                }
            }
        }

        $businesses = BusinessReg::select('*')
        ->OrderBy('business_registration.id','DESC')
        ->get();
        $street_name_application_forms = StreetNameApplicationForm::where('is_deleted','0')->get();

        $userAssignedBusiness = [];
        $user_assigned_businesses = UserAssignedBusiness::where('user_id',$user->id)->get();
        if(count($user_assigned_businesses) > 0)
        {
            foreach($user_assigned_businesses as $user_assigned_business)
            {
                $userAssignedBusiness[] = $user_assigned_business->business_id;
            }
        }

        $userAssignedStreetApp = [];
        $user_street_applications = UserAssignedStreetApplication::where('user_id',$user->id)->get();
        if(count($user_street_applications) > 0)
        {
            foreach($user_street_applications as $user_street_application)
            {
                $userAssignedStreetApp[] = $user_street_application->street_application_id;
            }
        }

        $userAssignedProperty = [];
        $user_street_properties = UserAssignedProperty::where('user_id',$user->id)->get();
        if(count($user_street_properties) > 0)
        {
            foreach($user_street_properties as $user_street_application)
            {
                $userAssignedProperty[] = $user_street_application->ward_id;
            }
        }

        if (request()->user()->hasRole('Super Admin')) {
            $wards = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
        } else {
            $wards = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
        }

        return view("admin.business_user.edit")->with(compact('user','wards','userAssignedProperty','property_datakeysWithValueOne','street_datakeysWithValueOne','userAssignedStreetApp','keysWithValueOne','businesses','userAssignedBusiness','street_name_application_forms'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' =>  'string',
            'gender' => 'required',
            'email' => 'required|email|unique:admin_users,email,'.$request->id,
            'username' => 'required|unique:admin_users,username,'.$request->id,
            'type' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }

        $user = AdminUser::find($request->id);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->type = $request->type;
        if(!empty($request->password))
        {
            $user->password = Hash::make($request->password);
        }

        $data = $this->moduelAccess();
        $newArr = [];
        foreach ($data as $response) {
            if(!empty($request->module_access))
            {
                $bit = in_array($response, $request->module_access) ? 1 : 0;
            }
            else
            {
                $bit =0;
            }
            $newArr[$response] = $bit;
        }

        $user->access = json_encode($newArr);
        

        if(!empty($request->module_access))
        {
            $data = $this->moduelAccess();
            $newArr = [];
            foreach ($data as $response) {
                $newArr[$response] = in_array($response, $request->module_access) ? 1 : 0;
            }

            $user->access = json_encode($newArr);
        }


        $street_data = $this->streetModuelAccess();
        $newArr = [];
        foreach ($street_data as $response) {
            if(!empty($request->street_module_access))
            {
                $bit = in_array($response, $request->street_module_access) ? 1 : 0;
            }
            else
            {
                $bit =0;
            }
            $newArr[$response] = $bit;
        }

        $user->street_module_access = json_encode($newArr);

        if(!empty($request->street_module_access))
        {
            $street_data = $this->streetModuelAccess();
            $street_newArr = [];
            foreach ($street_data as $response) {
                $street_newArr[$response] = in_array($response, $request->street_module_access) ? 1 : 0;
            }

            $user->street_module_access = json_encode($street_newArr);
            $user->user_type = "street";
        }

        $property_data = $this->propertyModuelAccess();
        $newArr = [];
        foreach ($property_data as $response) {
            if(!empty($request->property_module_access))
            {
                $bit = in_array($response, $request->property_module_access) ? 1 : 0;
            }
            else
            {
                $bit =0;
            }
            $newArr[$response] = $bit;
        }

        $user->property_module_access = json_encode($newArr);

        if(!empty($request->property_module_access))
        {
            $property_data = $this->propertyModuelAccess();
            $property_newArr = [];
            foreach ($property_data as $response) {
                $property_newArr[$response] = in_array($response, $request->property_module_access) ? 1 : 0;
            }

            $user->property_module_access = json_encode($property_newArr);
            $user->user_type = "property";
        }

        $user->save();
        
        $role = Role::findByName('Super Admin Cus');
        if(!$role)
        {
            Role::create(['name' => 'Super Admin Cus']);
        }
        $user->assignRole($role);

        UserAssignedBusiness::where('user_id',$user->id)->delete();
        if(!empty($request->module_access))
        {
            if(!empty($request->business_id))
            {
                foreach($request->business_id as $businessID)
                {
                    $count = UserAssignedBusiness::where('user_id',$user->id)->where('business_id',$businessID)->first();
                    if($count == 0)
                    {
                        $UserAssignedBusiness = new UserAssignedBusiness;
                        $UserAssignedBusiness->user_id = $user->id;
                        $UserAssignedBusiness->business_id = $businessID;
                        $UserAssignedBusiness->save();
                    }
                }
            }
        }
        
        UserAssignedStreetApplication::where('user_id',$user->id)->delete();
        if(!empty($request->street_module_access))
        {
            if(!empty($request->street_id))
            {
                foreach($request->street_id as $street_id)
                {
                    $count = UserAssignedStreetApplication::where('user_id',$user->id)->where('street_application_id',$street_id)->first();
                    if($count == 0)
                    {
                        $UserAssignedStreetApp = new UserAssignedStreetApplication;
                        $UserAssignedStreetApp->user_id = $user->id;
                        $UserAssignedStreetApp->street_application_id = $street_id;
                        $UserAssignedStreetApp->save();
                    }
                }
            }
        }


        UserAssignedProperty::where('user_id',$user->id)->delete();
        if(!empty($request->property_module_access))
        {
            if(!empty($request->property_id))
            {
                foreach($request->property_id as $property_id)
                {
                    $count = UserAssignedProperty::where('user_id',$user->id)->where('ward_id',$property_id)->first();
                    if($count == 0)
                    {
                        $UserAssignedStreetApp = new UserAssignedProperty;
                        $UserAssignedStreetApp->user_id = $user->id;
                        $UserAssignedStreetApp->ward_id = $property_id;
                        $UserAssignedStreetApp->save();
                    }
                }
            }
            
        }

        return Redirect()->route("admin.business.index-user")->with('success', 'User Update Successfully !');
    }

    public function detail($id)
    {
        $user = AdminUser::find($id);
        
        $keysWithValueOne = [];
        if(!empty($user->access))
        {
            $data = $this->moduelAccess();
            $jsonObj = json_decode($user->access, true);
            if (!isset($jsonObj["non_profit_business"])) {
                $jsonObj["non_profit_business"] = 0;
            }

            if (!isset($jsonObj["profit_based_business"])) {
                $jsonObj["profit_based_business"] = 0;
            }
            foreach ($data as $item) {
                if ($jsonObj[$item] === 1) {
                    $keysWithValueOne[] = ucwords(str_replace("_", " ", $item));
                }
            }
        }

        $street_datakeysWithValueOne = [];
        if(!empty($user->street_module_access))
        {
            $street_data = $this->streetModuelAccess();
            $street_datajsonObj = json_decode($user->street_module_access, true);
    
            foreach ($street_data as $item) {
                if ($street_datajsonObj[$item] === 1) {
                    $street_datakeysWithValueOne[] = ucwords(str_replace("_", " ", $item));
                }
            }
        }

        $property_datakeysWithValueOne = [];        
        if(!empty($user->property_module_access))
        {
            $property_data = $this->propertyModuelAccess();
            $property_datajsonObj = json_decode($user->property_module_access, true);

            foreach ($property_data as $item) {
                if ($property_datajsonObj[$item] === 1) {
                    $property_datakeysWithValueOne[] = ucwords(str_replace("_", " ", $item));
                }
            }
        }

        $userAssignedBusiness = [];
        $user_assigned_businesses = UserAssignedBusiness::where('user_id',$user->id)->get();
        if(count($user_assigned_businesses) > 0)
        {
            foreach($user_assigned_businesses as $user_assigned_business)
            {
                $userAssignedBusiness[] = $user_assigned_business->business_id;
            }
        }

        $userAssignedStreetApp = [];
        $user_street_applications = UserAssignedStreetApplication::where('user_id',$user->id)->get();
        if(count($user_street_applications) > 0)
        {
            foreach($user_street_applications as $user_street_application)
            {
                $userAssignedStreetApp[] = $user_street_application->street_application_id;
            }
        }

        $userAssignedProperty = [];
        $user_street_properties = UserAssignedProperty::where('user_id',$user->id)->get();
        if(count($user_street_properties) > 0)
        {
            foreach($user_street_properties as $user_street_application)
            {
                $userAssignedProperty[] = $user_street_application->ward_id;
            }
        }

        return view("admin.business_user.detail")->with(compact('user','street_datakeysWithValueOne','userAssignedProperty','property_datakeysWithValueOne','userAssignedStreetApp','keysWithValueOne','userAssignedBusiness'));
    }

    public function delete($id)
    {
        $user = AdminUser::find($id);
        $user->is_active = 0;
        $user->save();
        return Redirect()->route("admin.business.index-user")->with('success', 'User Deleted Successfully !');
    }

    private function moduelAccess()
    {
        $data = [
            "business_payment",
            "business_registration",
            "non_profit_business",
            "profit_based_business",
            "business_list",
            "business_detail",
            "business_filter",
            "multiple_download_reg_certificate",
            "multiple_download_license",
            "edit_business",
            "business_view_detail",
            "single_download_reg_certificate",
            "single_download_license",
            "business_reg_payment",
            "business_license_payment"
        ];
        return $data;
    }

    private function streetModuelAccess()
    {
        $data = [
            "approved_application",
            "pending_wo",
            "approved_ca",
            "rejected_wo_ca",
            "reapplied_application",
            "constituency",
            "ward",
            "community",
            "street_category",
        ];
        return $data;
    }

    private function propertyModuelAccess()
    {
        $data = [
            "property_filter",
            "property_list",
            "property_view",
            "property_delete",
            "property_payments",
            "assign_property",
            "property_characteristics",
            "property_characteristics_value",
        ];
        return $data;
    }
}
