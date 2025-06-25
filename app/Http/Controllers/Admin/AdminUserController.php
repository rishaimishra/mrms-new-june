<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['role:admin']);
    }

    public function showUserForm()
    {
        return view('admin.system_user.create');
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'user_role' => 'required',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:6',
            'username' => 'required|unique:admin_users,username'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        $user = new AdminUser();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole($request->user_role);

        return Redirect()->route('admin.system-user.list')->with($this->setMessage('User Created Successfully !', self::MESSAGE_SUCCESS));
    }

    public function list(Request $request)
    {

        $users = AdminUser::where('username', '!=', 'admin')->latest()->paginate();

        return view('admin.system_user.list', compact('users'));
    }

    public function show($id)
    {

        $data['admin_user'] = AdminUser::find($id);

        return view('admin.system_user.update', $data);
    }

    public function update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'string',
            'user_role' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        if ($request->password != '') {
            $update_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => Hash::make($request->password),
            ];
        } else {
            $update_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,

            ];
        }

        $user = AdminUser::findOrFail($request->id);
        $user->fill($update_data);
        $user->save();

        $user->syncRoles([$request->user_role]);

        return Redirect()->back()->with($this->setMessage('User Updated Successfully !', self::MESSAGE_SUCCESS));;
    }

    public function destroy($id, Request $request)
    {
        $admin_user = AdminUser::find($id);
        $admin_user->delete();
        return Redirect()->route('admin.system-user.list')->with($this->setMessage('User Deleted Successfully !', self::MESSAGE_SUCCESS));
    }

    public function profilePhoto()
    {
        return view('admin.profile-photo');
    }

    public function storeProfilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $user = $request->user();

        if ($request->hasFile('image')) {
            if ($user->hasImage()) {
                unlink($user->getAdminImage());
            }
            $user->image = $request->file('image')->store(AdminUser::USER_IMAGE);
        }

        $user->save();
        return Redirect()->back()->with('success', 'Updated Successfully !');
    }
}
