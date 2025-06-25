<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class DealerLoginController extends Controller
{
    // use AuthenticatesUsers;
    // protected $redirectTo;

    // public function __construct()
    // {
    //     $this->redirectTo = route('admin.dashboard');
    //     $this->middleware('guest:admin')->except('logout');
    // }

    public function showForm()
    {
        return view('dealer.auth.login');
    }

    // public function username()
    // {
    //     return 'username';
    // }

    // protected function guard()
    // {
    //     return \Auth::guard('admin');
    // }

    // public function logout(Request $request)
    // {
    //     $this->guard()->logout();
    //     $request->session()->invalidate();
    //     return redirect()->route('admin.auth.login');
    // }
}
