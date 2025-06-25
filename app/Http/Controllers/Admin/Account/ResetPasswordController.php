<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends AdminController
{
    public function __invoke()
    {
        return view('admin.auth.reset-password');
    }

    public function update(Request $request)
    {

        $this->validate($request, [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|string|max:191',
            'username' => 'required|string|max:191',
            'current_password' => 'required',

        ]);

        $admin = $request->user('admin');


        if (Hash::check($request->current_password, $admin->password)) {


            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->email = $request->email;
            $admin->username = $request->username;

            if ($request->new_password) {
                $admin->password = Hash::make($request->new_password);
            }


            $admin->save();

            return redirect()->back()->with($this->setMessage('Profile successfully updated', self::MESSAGE_SUCCESS));
        } else {
            return redirect()->back()->withErrors(['old_password' => 'Invalid Password.']);
        }
    }
}
