<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use App\Grids\AdminUsersGrid;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        return view("admin.business_user.create");
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $role = Role::where('name','BusinessUser')->first();
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' =>  'string',
            'gender' => 'required',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:6',
            'username' => 'required|unique:admin_users,username',
            'module_access' => 'required',
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

        $data = $this->moduelAccess();
        $newArr = [];
        foreach ($data as $response) {
            $newArr[$response] = in_array($response, $request->module_access) ? 1 : 0;
        }

        $user->access = json_encode($newArr);

        $user->save();

        return Redirect()->route("admin.business.index-user")->with('success', 'User Created Successfully !');
    }

    public function edit($id)
    {
        $user = AdminUser::find($id);
        $data = $this->moduelAccess();
        $jsonObj = json_decode($user->access, true);

        $keysWithValueOne = [];
        foreach ($data as $item) {
            if ($jsonObj[$item] === 1) {
                $keysWithValueOne[] = $item;
            }
        }
        return view("admin.business_user.edit")->with(compact('user','keysWithValueOne'));
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
            'module_access' => 'required',
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

        $data = $this->moduelAccess();
        $newArr = [];
        foreach ($data as $response) {
            $newArr[$response] = in_array($response, $request->module_access) ? 1 : 0;
        }

        $user->access = json_encode($newArr);

        $user->save();

        return Redirect()->route("admin.business.index-user")->with('success', 'User Update Successfully !');
    }

    public function detail($id)
    {
        $user = AdminUser::find($id);
        $data = $this->moduelAccess();
        $jsonObj = json_decode($user->access, true);
        // dd($jsonObj);

        $keysWithValueOne = [];
        foreach ($data as $item) {
            if ($jsonObj[$item] === 1) {
                $keysWithValueOne[] = ucwords(str_replace("_", " ", $item));
            }
        }
        return view("admin.business_user.detail")->with(compact('user','keysWithValueOne'));
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
}
