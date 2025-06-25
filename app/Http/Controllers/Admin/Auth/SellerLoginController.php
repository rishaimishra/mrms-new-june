<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerLoginController extends AdminController
{
    use AuthenticatesUsers;
    protected $redirectTo;

    public function __construct()
    {
        $this->redirectTo = route('admin.sellerDashboard');
        // $this->middleware('guest:admin')->except('logout');
    }

    public function showForm()
    {
        return view('admin.auth.seller_login');
    }

    public function username()
    {
        return 'username';
    }

    protected function guard()
    {
        return \Auth::guard('user');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect()->route('admin.seller.login.form');
    }
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string|min:6',
    ]);

    if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
        // return Auth::user();
        // if (Auth::user()->is_seller == 1) {
            return redirect()->route('admin.sellerDashboard'); // Redirect after successful login
        // }
        // else{
        //     return redirect()->back()->withErrors(['username' => 'You are not a seller.'])->withInput();
        // }
         
    }
    return redirect()->back()->withErrors(['username' => 'Invalid username or password.'])->withInput(); // Redirect back with error
}
}
